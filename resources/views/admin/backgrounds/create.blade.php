<x-layouts.app :title="__('Background')">
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Background Baru</h2>
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('backgrounds.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Meng-include partial form --}}
                @include('admin.backgrounds._form', ['background' => null])
<x-layouts.app :title="__('Background')">
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 font-playfair">Tambah Background Baru</h2>
        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <form action="{{ route('backgrounds.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @include('admin.backgrounds._form', ['background' => null])

                <button type="submit"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2 px-6 rounded-lg mt-6 transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105">
                    Simpan Background
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mt-4 transition duration-300">
                    Simpan Background
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>