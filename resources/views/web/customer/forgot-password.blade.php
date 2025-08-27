<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Lupa Kata Sandi — Peace Picture Studio</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center p-6">
  <div class="max-w-md w-full bg-white/5 backdrop-blur rounded-2xl p-8 text-white shadow-lg">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-semibold">Lupa Kata Sandi</h1>
      <p class="text-sm text-white/70 mt-2">Masukkan email terdaftar, kami akan mengirimkan tautan untuk mereset kata sandi.</p>
    </div>

    @if(session('successMessage'))
      <div class="mb-4 p-3 rounded bg-green-600/20 border border-green-400/30 text-green-200">
        {{ session('successMessage') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 p-3 rounded bg-red-600/20 border border-red-400/30 text-red-200">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('customer.forgot-password.send') }}" method="POST" class="space-y-4">
      @csrf
      <label class="block text-sm">Email</label>
      <input type="email" name="email" required class="w-full rounded-lg px-4 py-3 bg-white/10 border border-white/20 focus:outline-none" placeholder="nama@email.com">

      <button type="submit" class="w-full py-3 rounded-lg bg-red-600 hover:bg-red-700 font-semibold">Kirim Link Reset</button>
    </form>

    <div class="mt-6 text-center text-white/60 text-sm">
      <a href="{{ route('customer.login') }}" class="underline">Kembali ke halaman masuk</a>
    </div>
  </div>
</body>
</html>
