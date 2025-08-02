<?php

// app/Http/Controllers/Admin/ExtraItemController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtraItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExtraItemsController extends Controller
{
    public function index()
    {
        $extraItems = ExtraItem::latest()->get();
        return view('admin.extra.index', compact('extraItems'));
    }

    public function create()
    {
        $categories = ExtraItem::CATEGORIES;
        return view('admin.extra.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'category' => ['required', 'string', Rule::in(ExtraItem::CATEGORIES)],
            'is_active' => 'boolean',
        ]);

        ExtraItem::create([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'category' => $validatedData['category'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('extra-items.index')->with('success', 'Extra item berhasil ditambahkan!');
    }

    public function edit(ExtraItem $extraItem)
    {
        $categories = ExtraItem::CATEGORIES;
        return view('admin.extra.edit', compact('extraItem', 'categories'));
    }

    public function update(Request $request, ExtraItem $extraItem)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'category' => ['required', 'string', Rule::in(ExtraItem::CATEGORIES)],
            'is_active' => 'boolean',
        ]);

        $extraItem->update([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'category' => $validatedData['category'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('extra-items.index')->with('success', 'Extra item berhasil diupdate!');
    }

    public function destroy(ExtraItem $extraItem)
    {
        $extraItem->delete();

        return redirect()->route('extra-items.index')->with('success', 'Extra item berhasil dihapus!');
    }
}