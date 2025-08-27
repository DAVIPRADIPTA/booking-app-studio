@extends('layouts.app')

@section('content')
<!-- Kontainer utama dengan latar belakang gelap -->
<div class="flex flex-col items-center justify-center min-h-screen p-4 sm:p-6 md:p-10 lg:p-16 bg-gray-900 font-sans">

    <!-- Card utama dengan efek glassmorphism modern -->
    <div class="w-full max-w-4xl bg-gray-800/70 backdrop-blur-lg rounded-3xl shadow-2xl overflow-hidden
        border border-gray-700/50 transform transition-all duration-500 relative">

        <!-- Tombol kembali yang estetik di sudut kiri atas -->
        <a href="/homepage" class="absolute top-6 left-6 z-10 p-3 text-gray-400 hover:text-white
            bg-gray-700/50 rounded-full transition-colors duration-200 hover:bg-gray-600/50"
            aria-label="Kembali ke halaman beranda">
            <svg class="h-5 w-5 sm:h-6 sm:w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>

        <!-- Konten card dengan padding responsif -->
        <div class="px-6 py-10 sm:px-8 md:px-12 md:py-16">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-2 text-center drop-shadow-md tracking-tight">
                Pengaturan Akun
            </h2>
            <p class="text-center text-gray-400 mb-12 max-w-xl mx-auto text-sm sm:text-base">
                Kelola informasi profil, perbarui detail pribadi, dan pastikan akun Anda tetap aman.
            </p>

            <!-- Bagian Notifikasi (Toasts) dengan Alpine.js -->
            <div x-data="{ show: false, message: '', type: '' }"
                x-init="
                    @if (session('status') === 'profile-updated')
                        show = true;
                        message = 'Profil Anda berhasil diperbarui.';
                        type = 'profile';
                        setTimeout(() => show = false, 3000);
                    @elseif (session('status') === 'password-updated')
                        show = true;
                        message = 'Kata sandi Anda berhasil diperbarui.';
                        type = 'password';
                        setTimeout(() => show = false, 3000);
                    @endif
                "
                x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50 p-4 rounded-xl shadow-lg
                text-white backdrop-blur-sm text-center text-sm"
                :class="{'bg-green-600/90 border border-green-500': type === 'profile', 'bg-red-600/90 border border-red-500': type === 'password'}"
                role="alert">
                <p x-text="message"></p>
            </div>

            <!-- Bagian Pembaruan Profil & Password dalam satu grid -->
            <div class="grid md:grid-cols-2 gap-8 items-start">
                
                <!-- Kolom Kiri - Informasi Profil -->
                <div class="p-6 rounded-2xl bg-gray-800/50 border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Informasi Profil</h3>
                    <form method="POST" action="{{ route('customer.profile.update') }}">
                        @csrf
                        @method('put')
                        <div class="space-y-5">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.716.793H4.467a.75.75 0 01-.716-.793z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}"
                                        class="w-full pl-10 pr-4 py-2 bg-gray-700/50 text-gray-200 border border-gray-600 rounded-lg shadow-sm
                                            focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200"
                                        placeholder="Nama Lengkap">
                                    @error('name')
                                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.683 6.643a1.5 1.5 0 01-1.634 0L1.5 8.67z" />
                                            <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 7.436a1.5 1.5 0 001.572 0L22.5 6.908z" />
                                        </svg>
                                    </div>
                                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}"
                                        class="w-full pl-10 pr-4 py-2 bg-gray-700/50 text-gray-200 border border-gray-600 rounded-lg shadow-sm
                                            focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200"
                                        placeholder="Alamat Email">
                                    @error('email')
                                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="whatsapp_number" class="block text-sm font-medium text-gray-300 mb-1">Nomor WhatsApp (opsional)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M6.25 6.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75H10A.75.75 0 0110.75 8v.75h-.75a.75.75 0 01-.75-.75H6.25zM12.5 6.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75h-.75a.75.75 0 01-.75-.75H12.5zM18.5 6.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75h-.75a.75.75 0 01-.75-.75H18.5zM6.25 12.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75H6.25zM12.5 12.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75h-.75a.75.75 0 01-.75-.75H12.5zM18.5 12.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75h-.75a.75.75 0 01-.75-.75H18.5zM6.25 18.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75H6.25zM12.5 18.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75h-.75a.75.75 0 01-.75-.75H12.5zM18.5 18.5a.75.75 0 01.75-.75h.75a.75.75 0 01.75.75v.75h-.75a.75.75 0 01-.75-.75H18.5z" />
                                        </svg>
                                    </div>
                                    <input type="tel" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $customer->whatsapp_number) }}"
                                        class="w-full pl-10 pr-4 py-2 bg-gray-700/50 text-gray-200 border border-gray-600 rounded-lg shadow-sm
                                            focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200"
                                        placeholder="Nomor WhatsApp">
                                    @error('whatsapp_number')
                                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Tombol Simpan Perubahan Profil dengan gradien merah-emas -->
                        <div class="flex justify-center mt-8">
                            <button type="submit"
                                class="inline-flex items-center px-8 py-3 rounded-xl shadow-lg font-bold text-white
                                    bg-gradient-to-r from-red-500 to-rose-700 hover:from-red-600 hover:to-rose-800
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Kolom Kanan - Pembaruan Password -->
                <div class="p-6 rounded-2xl bg-gray-800/50 border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Pembaruan Kata Sandi</h3>
                    <form method="POST" action="{{ route('customer.password.update') }}">
                        @csrf
                        @method('put')
                        <div class="space-y-5">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1">Kata Sandi Saat Ini</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zM11.25 10.5v-3a.75.75 0 011.5 0v3h-1.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                                        class="w-full pl-10 pr-4 py-2 bg-gray-700/50 text-gray-200 border border-gray-600 rounded-lg shadow-sm
                                            focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200"
                                        placeholder="Kata Sandi Saat Ini">
                                    @error('current_password')
                                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Kata Sandi Baru</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zM11.25 10.5v-3a.75.75 0 011.5 0v3h-1.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="password" id="password" name="password" autocomplete="new-password"
                                        class="w-full pl-10 pr-4 py-2 bg-gray-700/50 text-gray-200 border border-gray-600 rounded-lg shadow-sm
                                            focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200"
                                        placeholder="Kata Sandi Baru">
                                    @error('password')
                                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Konfirmasi Kata Sandi Baru</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zM11.25 10.5v-3a.75.75 0 011.5 0v3h-1.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                                        class="w-full pl-10 pr-4 py-2 bg-gray-700/50 text-gray-200 border border-gray-600 rounded-lg shadow-sm
                                            focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all duration-200"
                                        placeholder="Konfirmasi Kata Sandi Baru">
                                    @error('password_confirmation')
                                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Tombol Simpan Perubahan Password dengan gradien merah-emas -->
                        <div class="flex justify-center mt-8">
                            <button type="submit"
                                class="inline-flex items-center px-8 py-3 rounded-xl shadow-lg font-bold text-white
                                    bg-gradient-to-r from-red-500 to-rose-700 hover:from-red-600 hover:to-rose-800
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Simpan Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection