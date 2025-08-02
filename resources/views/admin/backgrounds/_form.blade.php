<div class="mb-4">
    <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Kategori Sesi</label>
    <select name="category" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror">
        <option value="" disabled {{ !old('category', $background->category ?? '') ? 'selected' : '' }}>Pilih Kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category }}" {{ old('category', $background->category ?? '') == $category ? 'selected' : '' }}>
                {{ Str::title(str_replace('-', ' ', $category)) }}
            </option>
        @endforeach
    </select>
    @error('category')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Background</label>
    <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name', $background->name ?? '') }}">
    @error('name')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Gambar Background</label>
    <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror">
    @error('image')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
    
    {{-- Tampilkan preview gambar jika ada --}}
    @if(isset($background->image))
    <div class="mt-4">
        <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
        <img src="{{ asset('storage/' . $background->image) }}" alt="Preview" class="w-48 h-auto rounded shadow">
    </div>
    @endif
</div>

<div class="flex items-center mb-4">
    <input class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" type="checkbox" name="is_active" id="is_active" value="1" 
           {{ old('is_active', $background->is_active ?? true) ? 'checked' : '' }}>
    <label class="ms-2 text-sm font-medium text-gray-900" for="is_active">
        Aktif
    </label>
</div>