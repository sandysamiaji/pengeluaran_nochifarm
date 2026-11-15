<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

/**
 * Sinkron pembelian pakan di Pengeluaran -> stok masuk Gudang Pakan (farm_stocks, dipakai nochifarminput).
 * Baris hasil sinkron ditandai "[EXP:kode]" di notes agar bisa diedit/dihapus bersama pengeluarannya.
 * Anti-duplikat: bila owner sudah input manual di Gudang Pakan (jenis & kg sama, selisih tanggal <= 3 hari), tidak dibuat lagi.
 */
class FeedStockSync
{
    private const SUBCATEGORIES = ['pakan layer', 'pakan grower', 'pakan starter'];
    private const DATE_TOLERANCE_DAYS = 3;
    // Data lama kemungkinan sudah diinput manual dengan jumlah yang tidak persis sama, jadi tidak dibuatkan stok baru.
    private const CREATE_WINDOW_DAYS = 14;

    public static function sync(Expense $expense): void
    {
        $tag = self::tag($expense);
        $linked = self::linked($expense);
        $parsed = self::parse($expense);

        if (!$parsed) { // bukan pembelian pakan / jumlah tidak terbaca
            if ($linked) DB::table('farm_stocks')->where('id', $linked->id)->delete();
            return;
        }

        [$itemName, $qty, $unit] = $parsed;
        $date = $expense->date->toDateString();
        $notes = $tag . ' ' . $expense->purpose;
        if ($linked && str_starts_with(trim($linked->notes ?? ''), '[NONAKTIF]')) {
            $notes = '[NONAKTIF] ' . $notes; // jaga status nonaktif dari Gudang Pakan
        }

        if ($linked) {
            DB::table('farm_stocks')->where('id', $linked->id)->update([
                'date' => $date, 'item_name' => $itemName, 'quantity' => $qty, 'unit' => $unit,
                'notes' => $notes, 'updated_at' => now(),
            ]);
            return;
        }

        if ($expense->date->lt(now()->subDays(self::CREATE_WINDOW_DAYS)->startOfDay())) {
            return;
        }

        if (self::hasManualDuplicate($itemName, self::toKg($qty, $unit), $date)) {
            return;
        }

        DB::table('farm_stocks')->insert([
            'user_id' => $expense->user_id, 'date' => $date, 'category' => 'pakan', 'item_name' => $itemName,
            'type' => 'masuk', 'quantity' => $qty, 'unit' => $unit,
            'source' => 'Pengeluaran ' . $expense->transaction_code, 'notes' => $notes,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public static function remove(Expense $expense): void
    {
        DB::table('farm_stocks')->where('notes', 'like', '%' . self::tag($expense) . '%')->delete();
    }

    /** @return array{0:string,1:float,2:string}|null [item_name, qty, unit] */
    public static function parse(Expense $expense): ?array
    {
        if (strtolower($expense->category) !== 'pakan' || !in_array(strtolower($expense->subcategory), self::SUBCATEGORIES, true)) {
            return null;
        }
        // Jumlah dibaca dari teks keperluan/catatan, contoh: "pembelian pakan 70 Karung", "80 krg 524A"
        if (!preg_match('/(\d+(?:[.,]\d+)?)\s*(karung|krg|sak|kg|ton)\b/i', $expense->purpose . ' ' . $expense->notes, $m)) {
            return null;
        }
        $qty = (float) str_replace(',', '.', $m[1]);
        $u = strtolower($m[2]);
        $unit = $u === 'kg' ? 'Kg' : ($u === 'ton' ? 'Ton' : 'Karung');

        return $qty > 0 ? [ucwords(strtolower($expense->subcategory)), $qty, $unit] : null;
    }

    private static function tag(Expense $expense): string
    {
        return '[EXP:' . $expense->transaction_code . ']';
    }

    private static function linked(Expense $expense)
    {
        return DB::table('farm_stocks')->where('notes', 'like', '%' . self::tag($expense) . '%')->first();
    }

    private static function toKg(float $qty, string $unit): float
    {
        $perKarung = (float) (Setting::get('kg_per_karung') ?: 50);
        return match (strtolower($unit)) {
            'karung', 'sak', 'krg' => $qty * $perKarung,
            'ton' => $qty * 1000,
            default => $qty,
        };
    }

    private static function isGrower(?string $name): bool
    {
        return (bool) preg_match('/grower|starter|pullet/i', $name ?? '');
    }

    private static function hasManualDuplicate(string $itemName, float $kg, string $date): bool
    {
        $rows = DB::table('farm_stocks')
            ->whereRaw('LOWER(category) = ?', ['pakan'])->whereRaw('LOWER(type) = ?', ['masuk'])
            ->whereBetween('date', [
                date('Y-m-d', strtotime($date . ' -' . self::DATE_TOLERANCE_DAYS . ' days')),
                date('Y-m-d', strtotime($date . ' +' . self::DATE_TOLERANCE_DAYS . ' days')),
            ])
            ->where(fn($q) => $q->whereNull('notes')->orWhere('notes', 'not like', '%[EXP:%'))
            ->get();

        foreach ($rows as $r) {
            if (self::isGrower($r->item_name) === self::isGrower($itemName)
                && abs(self::toKg((float) $r->quantity, $r->unit ?? 'Kg') - $kg) < 1) {
                return true;
            }
        }
        return false;
    }
}
