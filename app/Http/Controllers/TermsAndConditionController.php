<?php

namespace App\Http\Controllers;

use App\Models\TermsAndCondition;
use Illuminate\Http\Request;

class TermsAndConditionController extends Controller
{
    /**
     * Tampilkan daftar semua terms & conditions.
     */
    public function index()
    {
        $terms = TermsAndCondition::all();
        return view('admin.sdk.index', compact('terms'));
    }

    /**
     * Tampilkan form untuk membuat terms & conditions baru.
     */
    public function create()
    {
        return view('admin.sdk.create');
    }

    /**
     * Simpan terms & conditions baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'sub_content' => 'nullable|string',
        ]);

        TermsAndCondition::create($request->all());

        return redirect()->route('terms.index')->with('success', 'Terms & Conditions berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail terms & conditions.
     */
    public function show(TermsAndCondition $term)
    {
        return view('admin.sdk.show', compact('term'));
    }

    /**
     * Tampilkan form untuk mengedit terms & conditions.
     */
    public function edit(TermsAndCondition $term)
    {
        return view('admin.sdk.edit', compact('term'));
    }

    /**
     * Perbarui terms & conditions di database.
     */
    public function update(Request $request, TermsAndCondition $term)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'sub_content' => 'nullable|string',
        ]);

        $term->update($request->all());

        return redirect()->route('terms.index')->with('success', 'Terms & Conditions berhasil diperbarui.');
    }

    /**
     * Hapus terms & conditions dari database.
     */
    public function destroy(TermsAndCondition $term)
    {
        $term->delete();

        return redirect()->route('terms.index')->with('success', 'Terms & Conditions berhasil dihapus.');
    }
}