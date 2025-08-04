<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl">

        <!-- Hero / Welcome Section -->
        <div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 px-8 py-10 shadow-sm">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Peace Picture Studio
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                Selamat datang di sistem manajemen studio foto Anda. Platform ini dirancang untuk membantu Anda mengelola jadwal pemotretan, paket layanan, dan kebutuhan studio secara efisien dan elegan.
            </p>
        </div>

        <!-- Informational Grid -->
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Booking -->
            <div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Manajemen Booking</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                    Kelola jadwal sesi foto dengan sistem reservasi yang terorganisir. Lihat, edit, dan atur sesi dalam satu tampilan terpadu.
                </p>
            </div>

            <!-- Background & Ekstra -->
            <div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Latar & Layanan Tambahan</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                    Atur pilihan latar belakang studio dan produk tambahan seperti album, cetakan, atau properti tematik.
                </p>
            </div>

            <!-- Policy -->
            <div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Syarat & Ketentuan</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                    Tampilkan aturan studio, ketentuan pembayaran, pembatalan, dan hak penggunaan media dengan tata letak yang rapi dan jelas.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
