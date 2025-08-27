@extends('layouts.app')

@section('title', 'Kebijakan Privasi - Peace Picture Studio')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-red-600">Kebijakan Privasi</h1>
            <p class="mt-2 text-gray-600">
                Terakhir diperbarui: {{ date('d F Y') }}
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white shadow-lg rounded-2xl p-8 space-y-6">
            <h2 class="text-xl font-bold text-gray-800">1. Pengumpulan Informasi</h2>
            <p class="text-gray-600 leading-relaxed">
                Kami mengumpulkan informasi pribadi yang Anda berikan secara langsung saat melakukan pendaftaran akun,
                melakukan pemesanan, atau menggunakan layanan kami. Informasi ini dapat mencakup nama, alamat email,
                nomor telepon, serta informasi lain yang relevan dengan kebutuhan layanan.
            </p>

            <h2 class="text-xl font-bold text-gray-800">2. Penggunaan Informasi</h2>
            <p class="text-gray-600 leading-relaxed">
                Informasi yang dikumpulkan digunakan untuk:
            </p>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>Menyediakan dan meningkatkan layanan kami.</li>
                <li>Memproses transaksi dan konfirmasi pemesanan.</li>
                <li>Mengirimkan pemberitahuan terkait layanan, promosi, atau pembaruan.</li>
                <li>Menjaga keamanan akun dan sistem kami.</li>
            </ul>

            <h2 class="text-xl font-bold text-gray-800">3. Perlindungan Data</h2>
            <p class="text-gray-600 leading-relaxed">
                Kami berkomitmen untuk menjaga keamanan data pribadi Anda dan menggunakan langkah-langkah teknis serta
                administratif yang sesuai untuk melindunginya dari akses, penggunaan, atau pengungkapan yang tidak
                sah.
            </p>

            <h2 class="text-xl font-bold text-gray-800">4. Berbagi Informasi</h2>
            <p class="text-gray-600 leading-relaxed">
                Kami tidak menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak ketiga kecuali jika
                diwajibkan oleh hukum atau diperlukan untuk melaksanakan layanan tertentu yang Anda minta.
            </p>

            <h2 class="text-xl font-bold text-gray-800">5. Perubahan Kebijakan Privasi</h2>
            <p class="text-gray-600 leading-relaxed">
                Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Setiap perubahan akan dipublikasikan
                di halaman ini dengan tanggal pembaruan terbaru.
            </p>

            <h2 class="text-xl font-bold text-gray-800">6. Kontak Kami</h2>
            <p class="text-gray-600 leading-relaxed">
                Jika Anda memiliki pertanyaan terkait kebijakan privasi ini, silakan hubungi kami melalui email di
                <a href="mailto:no-peacepicturestudio@gmail.com" class="text-red-600 hover:underline">
                    no-peacepicturestudio@gmail.com
                </a>.
            </p>
        </div>

        <!-- Back Button -->
        <div class="text-center">
            <a href="{{ route('customer.login') }}"
               class="inline-flex items-center px-5 py-2 bg-red-600 text-white font-medium rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                ← Kembali ke Login
            </a>
        </div>
    </div>
</div>
@endsection