<x-layouts.app :title="__('Background')">

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Backgrounds</h2>
            <a href="{{ route('backgrounds.create') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-lg transition duration-300 shadow-sm hover:shadow-md">
                + Tambah Background
            </a>
        </div>

        <!-- Alert: Sukses -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-lg mb-6 text-sm flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabel (Desktop) / Cards (Mobile) -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <!-- Desktop Table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Gambar</th>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($backgrounds as $background)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-5 text-sm text-gray-700">{{ $loop->iteration }}</td>
                            <td class="px-6 py-5">
                                <img src="{{ asset('storage/' . $background->image) }}"
                                     alt="{{ $background->name }}"
                                     class="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-200">
                            </td>
                            <td class="px-6 py-5 text-sm font-medium text-gray-800">{{ $background->name }}</td>
                            <td class="px-6 py-5 text-sm text-gray-600">
                                {{ Str::title(str_replace('-', ' ', $background->category)) }}
                            </td>
                            <td class="px-6 py-5 text-sm">
                                @if($background->is_active)
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium border border-green-200">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-medium border border-red-200">
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-sm space-y-2">
                                <a href="{{ route('backgrounds.edit', $background) }}"
                                   class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold py-1.5 px-3 rounded-lg transition">
                                    Edit
                                </a>
                                <form action="{{ route('backgrounds.destroy', $background) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full text-center bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-1.5 px-3 rounded-lg transition"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus background ini?');">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm italic">
                                Belum ada background yang ditambahkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="sm:hidden space-y-4 p-4">
                @forelse($backgrounds as $background)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 transition hover:shadow-md">
                    <div class="flex gap-4 mb-3">
                        <img src="{{ asset('storage/' . $background->image) }}"
                             alt="{{ $background->name }}"
                             class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 text-sm">{{ $background->name }}</h3>
                            <p class="text-xs text-gray-600 mt-1">
                                {{ Str::title(str_replace('-', ' ', $background->category)) }}
                            </p>
                            <div class="mt-2">
                                @if($background->is_active)
                                    <span class="text-xs text-green-700 font-medium">🟢 Aktif</span>
                                @else
                                    <span class="text-xs text-red-700 font-medium">🔴 Non-Aktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 pt-3 border-t border-gray-100">
                        <a href="{{ route('backgrounds.edit', $background) }}"
                           class="flex-1 text-center bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold py-2 px-3 rounded-lg text-sm transition">
                            Edit
                        </a>
                        <form action="{{ route('backgrounds.destroy', $background) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-2 px-3 rounded-lg text-sm transition"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus background ini?');">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-gray-500 text-sm">
                    Belum ada background yang ditambahkan.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</x-layouts.app>