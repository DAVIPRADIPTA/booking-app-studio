<div class="mb-4">
    <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
    <select name="category" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror">
        <option value="" disabled {{ !old('category', $extraItem->category ?? '') ? 'selected' : '' }}>Pilih Kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category }}" {{ old('category', $extraItem->category ?? '') == $category ? 'selected' : '' }}>
                {{ Str::title(str_replace('-', ' ', $category)) }}
            </option>
        @endforeach
    </select>
    @error('category')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Ekstra Item</label>
    <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name', $extraItem->name ?? '') }}">
    @error('name')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Harga</label>
    <input type="number" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror" value="{{ old('price', $extraItem->price ?? '') }}">
    @error('price')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center mb-4">
    <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" type="checkbox" name="is_active" id="is_active" value="1" 
           {{ old('is_active', $extraItem->is_active ?? true) ? 'checked' : '' }}>
    <label class="ms-2 text-sm font-medium text-gray-900" for="is_active">
        Aktif
    </label>
</div>