<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    /**
     * Definisi Master Kategori dan Subkategori Sesuai Desain Mockup
     */
    public static function getCategoriesData()
    {
        return [
            [
                'id' => 'pakan',
                'name' => 'Pakan',
                'description' => 'Pakan Grower, Pakan Layer, Ongkos Angkut',
                'icon' => 'package-open',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'subcategories' => [
                    'Pakan Grower',
                    'Pakan Layer',
                    'Ongkos Angkut Pakan',
                    'Bongkar/Muat Pakan',
                    'Pakan Starter',
                    'Konsentrat',
                    'Jagung Giling',
                    'Dedak/Bekatul',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'obat_vitamin',
                'name' => 'Obat & Vitamin',
                'description' => 'Obat, vitamin, vaksin, suplemen',
                'icon' => 'pill',
                'badge_color' => 'bg-rose-100 text-rose-800 border-rose-200',
                'subcategories' => [
                    'Vitamin & Suplemen',
                    'Vaksinasi Ayam',
                    'Obat-obatan Khusus',
                    'Antibiotik & Antistres',
                    'Disinfektan Kandang',
                    'Sanitasi Air Minum',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'operasional_kandang',
                'name' => 'Operasional Kandang',
                'description' => 'Listrik, air, gas, kebersihan, disinfeksi',
                'icon' => 'settings',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                'subcategories' => [
                    'Listrik PLN',
                    'Air Bersih / PDAM / Sumur',
                    'Gas Elpiji (Pemanas/Mess)',
                    'Kebersihan & Disinfeksi',
                    'Sekam / Litter Padi',
                    'Pest Control / Semprot Lalat',
                    'Perlengkapan Harian Kandang',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'peralatan_kandang',
                'name' => 'Peralatan Kandang',
                'description' => 'Peralatan baru, penggantian, perbaikan',
                'icon' => 'wrench',
                'badge_color' => 'bg-purple-100 text-purple-800 border-purple-200',
                'subcategories' => [
                    'Tempat Pakan (Feeder)',
                    'Tempat Minum (Nipple / Bell)',
                    'Pemanas (Gasolek / Brooder)',
                    'Timbangan Digital / Manual',
                    'Lampu & Kelistrikan Kandang',
                    'Sekop, Cangkul & Ember',
                    'Alat Semprot (Sprayer)',
                    'Penggantian Alat Rusak',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'perawatan_kandang',
                'name' => 'Perawatan Kandang',
                'description' => 'Perbaikan kandang, instalasi, renovasi',
                'icon' => 'home',
                'badge_color' => 'bg-stone-100 text-stone-800 border-stone-200',
                'subcategories' => [
                    'Perbaikan Atap & Seng',
                    'Perbaikan Tirai / Terpal',
                    'Bambu, Kawat & Kayu',
                    'Instalasi Pipa Air & Kabel',
                    'Bahan Semen & Material',
                    'Upah Tukang / Renovasi',
                    'Pengecatan / Kapur Dinding',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'ayam',
                'name' => 'Ayam',
                'description' => 'Pembelian ayam, transport, penanganan',
                'icon' => 'egg',
                'badge_color' => 'bg-orange-100 text-orange-800 border-orange-200',
                'subcategories' => [
                    'Pembelian DOC / Pullet',
                    'Ongkos Angkut / Truk DOC',
                    'Penanganan & Seleksi Bibit',
                    'Biaya Afkir Ayam',
                    'Karantina & Uji Lab',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'telur',
                'name' => 'Telur',
                'description' => 'Peti telur, tray, kemasan, perlengkapan',
                'icon' => 'circle-dot',
                'badge_color' => 'bg-amber-100 text-amber-900 border-amber-300',
                'subcategories' => [
                    'Peti Telur Kayu',
                    'Tray Telur (Egg Tray Karton/Plastik)',
                    'Tali Rafia & Lakban Pengemas',
                    'Plastik / Kemasan Khusus',
                    'Stiker / Label Nochi Farm',
                    'Timbangan Telur',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'transportasi',
                'name' => 'Transportasi',
                'description' => 'BBM, parkir, tol, servis, perawatan',
                'icon' => 'truck',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'subcategories' => [
                    'BBM (Solar / Dexlite / Pertalite)',
                    'Uang Tol & Parkir',
                    'Servis Rutin Kendaraan',
                    'Ganti Oli & Sparepart',
                    'Tambal Ban & Cuci Kendaraan',
                    'Sewa Truk / Pickup Tambahan',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'tenaga_kerja',
                'name' => 'Tenaga Kerja',
                'description' => 'Gaji, uang makan, lembur, insentif',
                'icon' => 'users',
                'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                'subcategories' => [
                    'Gaji Karyawan Kandang',
                    'Gaji Supir & Helper',
                    'Uang Makan Harian',
                    'Upah Lembur',
                    'Bonus & Insentif Produksi',
                    'Kesehatan & Pengobatan Karyawan',
                    'Tunjangan Hari Raya (THR)',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'administrasi',
                'name' => 'Administrasi',
                'description' => 'ATK, print, internet, pulsa, dokumen',
                'icon' => 'file-text',
                'badge_color' => 'bg-sky-100 text-sky-800 border-sky-200',
                'subcategories' => [
                    'Alat Tulis Kantor (ATK)',
                    'Kertas, Print & Fotocopy Nota',
                    'Pulsa & Kuota Paket Data HP',
                    'Langganan WiFi / Internet Kantor',
                    'Materai & Legalitas Usaha',
                    'Jasa Pembukuan / Akuntansi',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'logistik_konsumsi',
                'name' => 'Logistik & Konsumsi',
                'description' => 'Bahan makanan, air minum, keperluan mess',
                'icon' => 'utensils',
                'badge_color' => 'bg-teal-100 text-teal-800 border-teal-200',
                'subcategories' => [
                    'Beras & Bahan Makanan Mess',
                    'Air Minum Galon Karyawan',
                    'Kopi, Teh & Gula Dapur',
                    'Gas Elpiji Dapur Mess',
                    'Sabun Cuci & Perlengkapan Mess',
                    'Lainnya',
                ]
            ],
            [
                'id' => 'lain_lain',
                'name' => 'Lain-lain',
                'description' => 'Pengeluaran lain yang tidak termasuk',
                'icon' => 'more-horizontal',
                'badge_color' => 'bg-gray-100 text-gray-800 border-gray-200',
                'subcategories' => [
                    'Iuran Warga & Lingkungan',
                    'Keamanan & Ronda Desa',
                    'Sumbangan / Donasi Sosial',
                    'Pengeluaran Tak Terduga',
                    'Lainnya',
                ]
            ],
        ];
    }

    /**
     * Tampilkan Halaman Form Input Pengeluaran Kandang
     */
    public function create()
    {
        $categories = self::getCategoriesData();
        $templates = \App\Models\ExpenseTemplate::where('is_active', true)->orderBy('order_num', 'asc')->get();
        $defaultDate = Carbon::today()->format('Y-m-d');
        $displayDefaultDate = Carbon::today()->translatedFormat('d F Y');

        return view('expenses.create', compact('categories', 'templates', 'defaultDate', 'displayDefaultDate'));
    }

    /**
     * Tampilkan Daftar Pengeluaran Kandang
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $category = $request->query('category');
        $search = $request->query('q');

        $query = Expense::with('user')->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        if (!empty($category) && $category !== 'all') {
            $query->where('category', $category);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('subcategory', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $expenses = $query->paginate(10)->withQueryString();
        $totalNominal = (clone $query)->sum('amount');
        $categories = self::getCategoriesData();

        return view('expenses.index', compact('expenses', 'totalNominal', 'categories', 'startDate', 'endDate', 'category', 'search'));
    }

    /**
     * Simpan Pengeluaran Baru
     */
    public function store(Request $request)
    {
        // Bersihkan nominal dari karakter non-digit jika dikirim berformat 'Rp 350.000'
        $rawAmount = $request->input('amount');
        if (is_string($rawAmount)) {
            $cleanAmount = preg_replace('/[^0-9]/', '', $rawAmount);
            $request->merge(['amount' => $cleanAmount]);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'category' => 'required|string|max:100',
            'subcategory' => 'required|string|max:100',
            'purpose' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|in:Kas Tunai,Transfer Bank',
            'notes' => 'nullable|string|max:500',
            'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'date.required' => 'Tanggal pengeluaran wajib diisi.',
            'category.required' => 'Kategori wajib dipilih.',
            'subcategory.required' => 'Subkategori wajib dipilih.',
            'purpose.required' => 'Keperluan pengeluaran wajib diisi.',
            'amount.required' => 'Nominal pengeluaran wajib diisi.',
            'amount.min' => 'Nominal pengeluaran minimal Rp 1.',
            'receipt_photo.image' => 'File bukti nota harus berupa gambar.',
            'receipt_photo.max' => 'Ukuran foto nota maksimal 5 MB.',
        ]);

        // Upload bukti nota jika ada
        $receiptPath = null;
        if ($request->hasFile('receipt_photo')) {
            $file = $request->file('receipt_photo');
            $filename = 'nota_' . date('Ymd_His') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/expenses'), $filename);
            $receiptPath = 'uploads/expenses/' . $filename;

            // Server-side fallback: pastikan ukuran file di disk maksimal ~100 KB
            self::ensureMaxFileSize(public_path($receiptPath), 100);
        }

        // Generate Transaction Code (EXP-YYYYMMDD-XXXX)
        $dateFormatted = Carbon::parse($validated['date'])->format('Ymd');
        $latestExpenseToday = Expense::where('transaction_code', 'like', "EXP-{$dateFormatted}-%")->latest('id')->first();
        $nextSeq = 1;
        if ($latestExpenseToday && preg_match('/-(\d+)$/', $latestExpenseToday->transaction_code, $matches)) {
            $nextSeq = (int)$matches[1] + 1;
        }
        $transactionCode = sprintf('EXP-%s-%04d', $dateFormatted, $nextSeq);

        $expense = Expense::create([
            'transaction_code' => $transactionCode,
            'date' => $validated['date'],
            'category' => $validated['category'],
            'subcategory' => $validated['subcategory'],
            'purpose' => $validated['purpose'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'] ?? 'Kas Tunai',
            'notes' => $validated['notes'] ?? null,
            'receipt_photo' => $receiptPath,
            'user_id' => auth()->id() ?? 1,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran kandang telah dicatat dengan sukses.',
                'data' => [
                    'id' => $expense->id,
                    'transaction_code' => $expense->transaction_code,
                    'formatted_amount' => $expense->formatted_amount,
                    'purpose' => $expense->purpose,
                    'date' => Carbon::parse($expense->date)->translatedFormat('d F Y'),
                ],
                'redirect_url' => route('dashboard'),
            ]);
        }

        return redirect()->route('dashboard')->with('success', "Pengeluaran #{$transactionCode} berhasil dicatat!");
    }

    /**
     * Tampilkan Detail Pengeluaran (JSON untuk Modal Detail & Edit)
     */
    public function show(Request $request, $id)
    {
        try {
            $expense = Expense::with('user')->find($id);

            if (!$expense) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data pengeluaran tidak ditemukan.'
                    ], 404);
                }
                return redirect()->route('expenses.index')->with('error', 'Data pengeluaran tidak ditemukan.');
            }

            $user = $expense->user;
            $username = $user ? ($user->username ?: $user->name) : 'admin';
            $name = $user ? $user->name : 'Admin Kandang';
            $role = $user ? ($user->role ?? 'Petugas Input') : 'Petugas Kandang';

            $data = [
                'id' => $expense->id,
                'transaction_code' => $expense->transaction_code,
                'date' => Carbon::parse($expense->date)->format('Y-m-d'),
                'display_date' => Carbon::parse($expense->date)->translatedFormat('d F Y'),
                'category' => $expense->category,
                'subcategory' => $expense->subcategory,
                'purpose' => $expense->purpose,
                'amount' => (float)$expense->amount,
                'formatted_amount' => 'Rp ' . number_format($expense->amount, 0, ',', '.'),
                'payment_method' => $expense->payment_method ?? 'Kas Tunai',
                'notes' => $expense->notes ?? '',
                'receipt_photo' => $expense->receipt_url ?? ($expense->receipt_photo ? asset($expense->receipt_photo) : null),
                'penginput_username' => $username,
                'penginput_name' => $name,
                'penginput_role' => $role,
            ];

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            // Jika diakses langsung via browser URL bar tanpa AJAX, kembalikan response JSON atau redirect
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('expenses.index')->with('error', 'Gagal memuat detail pengeluaran.');
        }
    }

    /**
     * Update Pengeluaran
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        // Bersihkan nominal dari karakter non-digit jika dikirim berformat 'Rp 350.000'
        $rawAmount = $request->input('amount');
        if (is_string($rawAmount)) {
            $cleanAmount = preg_replace('/[^0-9]/', '', $rawAmount);
            $request->merge(['amount' => $cleanAmount]);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'category' => 'required|string|max:100',
            'subcategory' => 'required|string|max:100',
            'purpose' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|in:Kas Tunai,Transfer Bank',
            'notes' => 'nullable|string|max:500',
            'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'date.required' => 'Tanggal pengeluaran wajib diisi.',
            'category.required' => 'Kategori wajib dipilih.',
            'subcategory.required' => 'Subkategori wajib dipilih.',
            'purpose.required' => 'Keperluan pengeluaran wajib diisi.',
            'amount.required' => 'Nominal pengeluaran wajib diisi.',
            'amount.min' => 'Nominal pengeluaran minimal Rp 1.',
            'receipt_photo.image' => 'File bukti nota harus berupa gambar.',
            'receipt_photo.max' => 'Ukuran foto nota maksimal 5 MB.',
        ]);

        if ($request->hasFile('receipt_photo')) {
            if ($expense->receipt_photo && file_exists(public_path($expense->receipt_photo))) {
                @unlink(public_path($expense->receipt_photo));
            }

            $file = $request->file('receipt_photo');
            $filename = 'nota_' . date('Ymd_His') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/expenses'), $filename);
            $expense->receipt_photo = 'uploads/expenses/' . $filename;

            self::ensureMaxFileSize(public_path($expense->receipt_photo), 100);
        }

        $expense->date = $validated['date'];
        $expense->category = $validated['category'];
        $expense->subcategory = $validated['subcategory'];
        $expense->purpose = $validated['purpose'];
        $expense->amount = $validated['amount'];
        if (isset($validated['payment_method'])) {
            $expense->payment_method = $validated['payment_method'];
        }
        $expense->notes = $validated['notes'] ?? null;
        $expense->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pengeluaran #{$expense->transaction_code} berhasil diperbarui.",
                'data' => [
                    'id' => $expense->id,
                    'transaction_code' => $expense->transaction_code,
                    'formatted_amount' => $expense->formatted_amount,
                    'purpose' => $expense->purpose,
                    'date' => Carbon::parse($expense->date)->translatedFormat('d F Y'),
                ]
            ]);
        }

        return back()->with('success', "Pengeluaran #{$expense->transaction_code} berhasil diperbarui.");
    }

    /**
     * Hapus Pengeluaran
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // Hapus file nota jika ada di disk lokal
        if ($expense->receipt_photo && file_exists(public_path($expense->receipt_photo))) {
            @unlink(public_path($expense->receipt_photo));
        }

        $code = $expense->transaction_code;
        $expense->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pengeluaran #{$code} berhasil dihapus.",
            ]);
        }

        return back()->with('success', "Pengeluaran #{$code} berhasil dihapus.");
    }

    /**
     * Optimasi ukuran file foto nota menjadi maksimal ~100 KB (Fallback Server-Side via GD)
     */
    protected static function ensureMaxFileSize(string $filePath, int $maxKb = 100): void
    {
        if (!file_exists($filePath) || !extension_loaded('gd')) {
            return;
        }

        clearstatcache(true, $filePath);
        $currentSizeKb = filesize($filePath) / 1024;
        if ($currentSizeKb <= $maxKb) {
            return;
        }

        $imageInfo = @getimagesize($filePath);
        if (!$imageInfo) {
            return;
        }

        $mime = $imageInfo['mime'];
        $srcImage = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($filePath),
            'image/png' => @imagecreatefrompng($filePath),
            'image/webp' => @imagecreatefromwebp($filePath),
            default => null,
        };

        if (!$srcImage) {
            return;
        }

        $width = imagesx($srcImage);
        $height = imagesy($srcImage);

        // Kecilkan dimensi jika resolusi terlalu besar (> 1400px)
        $maxDim = 1400;
        if ($width > $maxDim || $height > $maxDim) {
            if ($width > $height) {
                $newWidth = $maxDim;
                $newHeight = (int)round(($height * $maxDim) / $width);
            } else {
                $newHeight = $maxDim;
                $newWidth = (int)round(($width * $maxDim) / $height);
            }

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($resized, 255, 255, 255);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $white);
            imagecopyresampled($resized, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($srcImage);
            $srcImage = $resized;
        }

        // Simpan iteratif dengan kualitas menurun sampai ukuran <= 100 KB
        $quality = 80;
        while ($quality >= 20) {
            imagejpeg($srcImage, $filePath, $quality);
            clearstatcache(true, $filePath);
            if (filesize($filePath) <= ($maxKb * 1024)) {
                break;
            }
            $quality -= 10;
        }

        imagedestroy($srcImage);
    }
}
