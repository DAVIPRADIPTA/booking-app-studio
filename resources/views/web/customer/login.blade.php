<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Masuk — Peace Picture Studio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                        dancing: ['Dancing Script', 'cursive']
                    },
                    colors: {
                        studio_red: {
                            50: '#fff1f1', 100: '#ffd9d9', 200: '#ffb3b3', 300: '#ff8a8a', 400: '#ff6262',
                            500: '#ef4444', 600: '#dc2626', 700: '#b91c1c', 800: '#991b1b', 900: '#7f1d1d'
                        },
                        studio_gold: {
                            50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcbf49', 400: '#fbbf24',
                            500: '#d4af37', 600: '#b48c00', 700: '#926e00', 800: '#785a00', 900: '#634a00'
                        }
                    },
                    dropShadow: {
                        glow: '0 0 20px rgba(239, 68, 68, 0.3)',
                        strong: '0 0 30px rgba(239, 68, 68, 0.4)'
                    },
                    boxShadow: {
                        glass: '0 20px 60px rgba(0, 0, 0, 0.45)',
                        card: '0 12px 40px rgba(0, 0, 0, 0.2)'
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': { opacity: 0, transform: 'translateY(20px)' },
                            '100%': { opacity: 1, transform: 'translateY(0)' }
                        },
                        pulseRing: {
                            '0%': { boxShadow: '0 0 0 0 rgba(239, 68, 68, 0.4)' },
                            '80%': { boxShadow: '0 0 0 16px rgba(239, 68, 68, 0)' },
                            '100%': { boxShadow: '0 0 0 0 rgba(239, 68, 68, 0)' }
                        }
                    },
                    animation: {
                        fadeUp: 'fadeUp 0.8s ease-out',
                        pulseRing: 'pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite'
                    }
                }
            },
            // Bagian plugins sekarang sudah tidak diperlukan
        }
    </script>

    <style>
        :root {
            --bg-image: url("{{ asset('images/prewed.jpg') }}");
        }

        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-image);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            overflow: hidden;
        }

        .bg-cinematic-overlay {
            background: linear-gradient(135deg,
                rgba(0, 0, 0, 0.85) 0%,
                rgba(0, 0, 0, 0.6) 30%,
                rgba(0, 0, 0, 0.4) 60%,
                rgba(0, 0, 0, 0.8) 100%
            );
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 24px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }

        .form-input {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            color: #333333;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input::placeholder {
            color: #999999;
            opacity: 1;
        }

        .form-input:focus {
            outline: none;
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: 1.25rem 4rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(239, 68, 68, 0.5);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(45deg);
            transition: left 0.8s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .logo {
            font-family: 'Dancing Script', cursive;
            font-size: 2.5rem;
            color: white;
            text-align: center;
            margin-bottom: 1rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
        }

        .alert {
            padding: 1.25rem 2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.3);
            color: #6ee7b7;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .form-footer {
            margin-top: 1.5rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        .form-footer a {
            font-weight: 600;
            color: #ef4444;
            text-decoration: underline;
        }

        .btn-loading {
            cursor: not-allowed;
            opacity: 0.75;
            transition: none;
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        /* New styles for floating alert */
        .floating-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 50;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            background-color: rgba(34, 197, 94, 0.9);
            color: white;
            font-size: 0.875rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .floating-alert.show {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>
<body class="h-full relative">

    <div class="absolute inset-0 bg-cinematic-overlay z-0 pointer-events-none"></div>

<header class="absolute top-6 left-6 z-50">
    <a href="{{ url('/') }}" 
       class="inline-flex items-center gap-2 rounded-lg bg-studio_red-500 hover:bg-studio_red-600 text-white px-4 py-2 text-sm font-medium shadow transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali
    </a>
</header>



    @if(session('successMessage'))
    <div id="floating-alert" class="floating-alert flex items-center gap-2">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('successMessage') }}</span>
    </div>
    @endif

    <main class="flex flex-col items-center justify-start h-screen w-full relative z-10 px-2 pt-20 pb-40">
        <div class="text-center">
            <img src="{{ asset('images/white.png') }}" alt="Peace Picture Studio" class="w-56 sm:w-72 mx-auto mb-8 mt-2">
        </div>

        <div class="form-container glass-panel rounded-2xl p-8 w-full max-w-md animate-fadeUp shadow-lg">

            @if(session('errorMessage'))
            <div class="alert alert-danger">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('errorMessage') }}</span>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                    <div><strong>{{ $error }}</strong></div>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('customer.store_login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="form-input w-full" placeholder="contoh@email.com" />
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required class="form-input w-full" placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-white/80 text-sm cursor-pointer">
                        <input type="checkbox" name="remember" class="mr-2 rounded focus:ring-studio-red-500 text-studio_red-600 border-gray-300" />
                        Ingat saya
                    </label>
                    <a href="{{ route('customer.forgot-password') }}" class="text-white/80 hover:text-white transition-colors duration-300 text-sm">Lupa kata sandi?</a>
                </div>

                <button id="submitBtn" type="submit" class="btn-primary w-full">
                    <span>MASUK</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
            </form>

            <div class="form-footer">
                Belum punya akun?
                <a href="{{ route('customer.register') }}" class="text-white hover:text-studio-red-500 transition-colors duration-300 font-medium">
                    Daftar sekarang
                </a>
            </div>

            <div class="mt-6 text-center text-[11px] text-white/50">
                Dengan masuk, Anda menyetujui
                <a href="{{ route('customer.terms') }}" class="underline hover:text-white">Syarat & Ketentuan</a>
                serta
                <a href="{{ route('customer.privacy') }}" class="underline hover:text-white">Kebijakan Privasi</a>.
            </div>
        </div>
    </main>

    <script>
        // Back by ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.location.href = "{{ url()->previous() ?? url('/') }}";
            }
        });

        // Form submission with loading state
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    return;
                }
                e.preventDefault();
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.classList.add('btn-loading');
                submitBtn.innerHTML = `
                    <svg class="h-5 w-5 spinner text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9" stroke-opacity=".25"></circle>
                        <path d="M21 12a9 9 0 00-9-9" stroke-linecap="round"></path>
                    </svg>
                    <span>Memproses...</span>
                `;

                setTimeout(() => {
                    form.submit();
                }, 100);
            });
        }

        // Show floating alert and hide after a delay
        document.addEventListener('DOMContentLoaded', function() {
            const floatingAlert = document.getElementById('floating-alert');
            if (floatingAlert) {
                // Show the alert
                setTimeout(() => {
                    floatingAlert.classList.add('show');
                }, 100);

                // Hide the alert after 5 seconds
                setTimeout(() => {
                    floatingAlert.classList.remove('show');
                }, 5000);
            }

            // Auto-focus email field
            const emailInput = document.getElementById('email');
            if (emailInput && !document.querySelector('.alert')) {
                emailInput.focus();
            }
        });
    </script>
</body>
</html>