<x-layouts.app :title="__('Edit Syarat & Ketentuan')">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6">Edit Syarat & Ketentuan</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('terms.update', $term) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Konten Utama</label>
                    <input type="text" name="content" id="content" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('content', $term->content) }}" required>
                    @error('content')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="sub_content" class="block text-gray-700 text-sm font-bold mb-2">Isi Konten</label>
                    <textarea name="sub_content" id="sub_content" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('sub_content', $term->sub_content) }}</textarea>
                    <p class="text-gray-500 text-xs italic mt-2">Gunakan enter untuk membuat baris baru.</p>
                    @error('sub_content')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Perbarui
                    </button>
                    <a href="{{ route('terms.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>