<x-layouts.app :title="__('Background')">
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Ekstra Item: {{ $extraItem->name }}</h2>
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('extra-items.update', $extraItem) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Meng-include partial form. Variabel $extraItem diisi dengan data item yang akan diedit. --}}
                @include('admin.extra._form', ['extraItem' => $extraItem])

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mt-4 transition duration-300">
                    Update Ekstra Item
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>