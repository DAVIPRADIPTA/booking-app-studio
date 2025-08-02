<x-layouts.app :title="__('Background')">
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Background Baru</h2>
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('backgrounds.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Meng-include partial form --}}
                @include('admin.backgrounds._form', ['background' => null])

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mt-4 transition duration-300">
                    Simpan Background
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>