<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Background;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class BackgroundController extends Controller
{
    /**
     * Tampilkan daftar semua background.
     *
    
     */
    public function index()
    {
        $backgrounds = Background::latest()->get();
        return view('admin.backgrounds.index', compact('backgrounds'));
    }

    /**
     * Tampilkan form untuk membuat background baru.
     *
    
     */
    public function create()
    {
        $categories = Background::CATEGORIES;
        return view('admin.backgrounds.create', compact('categories'));
    }

    /**
     * Simpan background baru ke database.
     *
     
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => ['required', 'string', Rule::in(Background::CATEGORIES)],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
        ]);

        // 2. Upload gambar dan simpan path-nya
        $imagePath = $request->file('image')->store('backgrounds', 'public');

        // 3. Gabungkan data dan simpan ke database
        Background::create([
            'name' => $validatedData['name'],
            'category' => $validatedData['category'],
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('backgrounds.index')->with('success', 'Background berhasil ditambahkan!');
    }

    /**
     * Tampilkan form untuk mengedit background.
     *
     * 
     */
    public function edit(Background $background)
    {
        $categories = Background::CATEGORIES;
        return view('admin.backgrounds.edit', compact('background', 'categories'));
    }

    /**
     * Update background yang sudah ada di database.
     *
     
     */
    public function update(Request $request, Background $background)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => ['required', 'string', Rule::in(Background::CATEGORIES)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // 2. Handle gambar jika ada upload baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            Storage::disk('public')->delete($background->image);
            // Upload gambar baru
            $validatedData['image'] = $request->file('image')->store('backgrounds', 'public');
        }

        // 3. Update data di database
        $background->update([
            'name' => $validatedData['name'],
            'category' => $validatedData['category'],
            'image' => $validatedData['image'] ?? $background->image,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('backgrounds.index')->with('success', 'Background berhasil diupdate!');
    }

    /**
     * Hapus background dari database.
     *
     
     */
    public function destroy(Background $background)
    {
        // 1. Hapus gambar dari storage
        Storage::disk('public')->delete($background->image);

        // 2. Hapus record dari database
        $background->delete();

        return redirect()->route('backgrounds.index')->with('success', 'Background berhasil dihapus!');
    }
}