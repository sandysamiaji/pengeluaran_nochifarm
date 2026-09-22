<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseTemplate;

class MasterTemplateController extends Controller
{
    /**
     * Tampilkan Daftar Master Template Pengeluaran Rutin
     */
    public function index()
    {
        $templates = ExpenseTemplate::orderBy('order_num', 'asc')->orderBy('id', 'asc')->get();
        $categories = ExpenseController::getCategoriesData();

        return view('master.templates.index', compact('templates', 'categories'));
    }

    /**
     * Simpan Template Pengeluaran Baru
     */
    public function store(Request $request)
    {
        $rawAmount = $request->input('amount');
        if (is_string($rawAmount)) {
            $cleanAmount = preg_replace('/[^0-9]/', '', $rawAmount);
            $request->merge(['amount' => $cleanAmount]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'subcategory' => 'required|string|max:100',
            'purpose' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'order_num' => 'nullable|integer',
        ]);

        ExpenseTemplate::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'subcategory' => $validated['subcategory'],
            'purpose' => $validated['purpose'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
            'order_num' => $validated['order_num'] ?? 0,
        ]);

        return redirect()->route('master.templates.index')->with('success', 'Template pengeluaran berhasil ditambahkan!');
    }

    /**
     * Update Template Pengeluaran
     */
    public function update(Request $request, $id)
    {
        $template = ExpenseTemplate::findOrFail($id);

        $rawAmount = $request->input('amount');
        if (is_string($rawAmount)) {
            $cleanAmount = preg_replace('/[^0-9]/', '', $rawAmount);
            $request->merge(['amount' => $cleanAmount]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'subcategory' => 'required|string|max:100',
            'purpose' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'order_num' => 'nullable|integer',
        ]);

        $template->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'subcategory' => $validated['subcategory'],
            'purpose' => $validated['purpose'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $template->is_active,
            'order_num' => $validated['order_num'] ?? 0,
        ]);

        return redirect()->route('master.templates.index')->with('success', 'Template pengeluaran berhasil diperbarui!');
    }

    /**
     * Toggle Status Aktif / Nonaktif
     */
    public function toggle($id)
    {
        $template = ExpenseTemplate::findOrFail($id);
        $template->is_active = !$template->is_active;
        $template->save();

        $statusStr = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Template '{$template->name}' berhasil {$statusStr}!");
    }

    /**
     * Hapus Template
     */
    public function destroy($id)
    {
        $template = ExpenseTemplate::findOrFail($id);
        $name = $template->name;
        $template->delete();

        return redirect()->route('master.templates.index')->with('success', "Template '{$name}' berhasil dihapus!");
    }
}
