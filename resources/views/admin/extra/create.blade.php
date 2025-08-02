<x-layouts.app :title="__('Background')">
    <div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Ekstra Item Baru</h2>
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('extra-items.store') }}" method="POST">
            @csrf
            
            {{-- Meng-include partial form. Variabel $extraItem diisi null untuk form create. --}}
            @include('admin.extra._form', ['extraItem' => null])
            
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mt-4 transition duration-300">
                Simpan Ekstra Item
            </button>
        </form>
    </div>
</div>
</x-layouts.app>