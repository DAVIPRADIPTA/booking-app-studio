<x-layouts.app :title="__('Ekstra')">
    <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Ekstra Item</h2>
        <a href="{{ route('extra-items.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
            Tambah Ekstra Item
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-5 py-3 border-b-2 border-gray-200">No</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Nama</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Harga</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Kategori</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($extraItems as $item)
                <tr class="hover:bg-gray-100">
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $loop->iteration }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $item->name }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ Str::title(str_replace('-', ' ', $item->category)) }}</td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        @if($item->is_active)
                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">Aktif</span>
                        @else
                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <a href="{{ route('extra-items.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-semibold mr-2">Edit</a>
                        <form action="{{ route('extra-items.destroy', $item) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus ekstra item ini?');">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-center text-sm text-gray-500">Belum ada ekstra item yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>