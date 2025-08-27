<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Syarat & Ketentuan — Peace Picture Studio</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white antialiased">
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="p-6 flex justify-between items-center bg-black/40 backdrop-blur">
      <a href="{{ url()->previous() ?? route('home') }}" class="inline-flex items-center gap-2 text-sm bg-white/10 px-3 py-2 rounded hover:bg-white/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
      </a>
      <div class="text-sm text-white/60">Peace Picture Studio</div>
    </header>

    <!-- Main -->
    <main class="flex-1 max-w-4xl mx-auto w-full p-6">
      <div class="bg-white/5 backdrop-blur rounded-2xl p-8 border border-white/10">
        <h1 class="text-2xl font-bold text-red-400 mb-4">Syarat & Ketentuan</h1>

        @if($terms->isNotEmpty())
          @foreach($terms as $item)
            <div class="mb-6">
              <h2 class="text-lg font-semibold">{{ $item->title }}</h2>
              <p class="text-sm text-white/70 whitespace-pre-line">{{ $item->content }}</p>
            </div>
          @endforeach
        @else
          <div class="space-y-4 text-sm text-white/70">
            <p>1. Semua pemesanan harus dilakukan melalui sistem resmi Peace Picture Studio.</p>
            <p>2. Pembatalan kurang dari 24 jam sebelum jadwal tidak dapat dikembalikan.</p>
            <p>3. Hak cipta foto berada pada Peace Picture Studio, penggunaan komersial memerlukan izin tertulis.</p>
            <p>4. Customer wajib mengikuti instruksi tim untuk keselamatan & kelancaran sesi.</p>
            <p>5. Untuk informasi lebih lanjut, hubungi: <a href="mailto:no-peacepicturestudio@gmail.com" class="underline text-red-300">no-peacepicturestudio@gmail.com</a></p>
          </div>
        @endif

        <div class="mt-8 text-xs text-white/50">
          Terakhir diperbarui: {{ now()->format('d M Y') }}
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="text-center text-xs text-white/50 p-6">
      © {{ date('Y') }} Peace Picture Studio — Semua hak dilindungi.
    </footer>
  </div>
</body>
</html>