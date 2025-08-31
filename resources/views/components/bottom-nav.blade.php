@php
    // Tidak lagi memerlukan props 'currentRoute' karena kita akan menggunakan Request::routeIs()
    use Illuminate\Support\Facades\Request;
@endphp

@push('styles')
    <style>
        /* Base Navigation Styles - No entry animation */
        .nav-container {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.05) inset;
            border-radius: 1rem;
        }

        .nav-item {
            position: relative;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .nav-item svg {
            width: 1.25rem;
            height: 1.25rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Shine effect on hover */
        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .nav-item:hover::before {
            left: 100%;
        }

        /* Active and Hover States */
        .nav-item:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.15) 100%);
            box-shadow: 0 4px 16px rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Dropdown Styles - FINAL */
        .dropdown-menu {
            position: absolute;
            bottom: calc(100% + 0.5rem);
            left: 50%;
            transform: translateX(-50%);
            min-width: 220px;
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            z-index: 50;
            /* Memastikan posisi stabil */
            max-width: calc(100vw - 1rem); 
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            gap: 0.75rem;
        }

        .dropdown-item:hover {
            background: rgba(239, 68, 68, 0.2);
            color: white;
        }

        .dropdown-item svg {
            width: 1.1rem;
            height: 1.1rem;
            color: #ef4444;
        }

        .dropdown-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 0.25rem 0;
        }

        .dropdown-header {
            padding: 0.75rem 1rem 0.25rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Responsive Breakpoints */
        @media (max-width: 640px) {
            .nav-text {
                display: none;
            }

            .nav-item {
                padding: 0.75rem !important;
                min-width: 42px;
                min-height: 42px;
            }

            .nav-item svg {
                width: 1.25rem;
                height: 1.25rem;
            }

            .dropdown-menu {
                min-width: 180px;
                font-size: 0.8rem;
            }
        }

        @media (min-width: 641px) {
            .nav-text {
                display: block !important;
            }
        }

        @media (max-width: 380px) {
            .nav-container {
                max-width: 280px;
                padding: 0.5rem !important;
                gap: 0.25rem !important;
            }

            .nav-item {
                min-width: 38px;
                min-height: 38px;
                padding: 0.625rem !important;
            }

            .nav-item svg {
                width: 1rem;
                height: 1rem;
            }
        }

        /* Focus & Disabled States */
        .nav-item:focus-visible,
        .dropdown-item:focus-visible {
            outline: 2px solid #ef4444;
            outline-offset: 2px;
        }

        .nav-item.disabled,
        .dropdown-item.disabled {
            opacity: 0.4;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
@endpush

<nav class="fixed bottom-0 left-0 right-0 z-50 px-3 sm:px-4 lg:px-8 pb-3 sm:pb-4 pt-2" role="navigation"
    aria-label="Main navigation">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-center">
            <div class="nav-container flex items-center gap-2 rounded-xl p-1.5 shadow-xl">

                <a href="#" id="backButton" class="nav-item text-white/80 hover:text-white"
                    aria-label="Go back to previous page">
                    <svg class="transition-transform hover:-translate-x-1" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    <span class="nav-text text-xs font-medium uppercase tracking-wider">Back</span>
                </a>

                <a href="{{ route('home') }}"
                    class="nav-item {{ Request::routeIs('home') ? 'active' : 'text-white/80 hover:text-white' }}"
                    aria-label="Go to home" aria-current="{{ Request::routeIs('home') ? 'page' : 'false' }}">
                    <svg class="transition-transform hover:scale-110" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="nav-text text-xs font-medium uppercase tracking-wider">Home</span>
                </a>

                <a href="{{ route('info') }}"
                    class="nav-item {{ Request::routeIs('info') ? 'active' : 'text-white/80 hover:text-white' }}"
                    aria-label="View information" aria-current="{{ Request::routeIs('info') ? 'page' : 'false' }}">
                    <svg class="transition-transform hover:scale-110" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <span class="nav-text text-xs font-medium uppercase tracking-wider">Info</span>
                </a>

                @auth('customer')
                    <div x-data="{ open: false }" class="relative group">
                        <button @click="open = !open" @click.away="open = false"
                            class="nav-item text-white/80 hover:text-white hover:bg-white/10 {{ Request::routeIs('customer.profile') ? 'active' : '' }}" 
                            aria-expanded="false"
                            aria-haspopup="true">
                            <svg class="transition-transform hover:scale-110" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span class="nav-text text-xs font-medium uppercase tracking-wider">
                                Profile
                            </span>
                        </button>

                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="dropdown-menu">
                            <div class="dropdown-header">Akun Saya</div>
                            <div class="dropdown-divider"></div>

                            <a href="{{ route('customer.profile') }}"
                                class="dropdown-item {{ Request::routeIs('customer.profile') ? 'active' : '' }}"
                                aria-current="{{ Request::routeIs('customer.profile') ? 'page' : 'false' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 11a1 1 0 100-2 1 1 0 000 2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.25 15.75l-2.25 2.25L11.25 20.25M12 12v3m0-12v3" />
                                </svg>
                                <span>Pengaturan Akun</span>
                            </a>

                            {{-- Tautan Riwayat Pemesanan aktif --}}
                            <a href="{{ route('customer.bookings') }}"
                                class="dropdown-item {{ Request::routeIs('customer.bookings') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Riwayat Pemesanan</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('customer.logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item w-full text-left">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H9" />
                                    </svg>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('customer.login') }}"
                        class="nav-item {{ Request::routeIs('customer.login') ? 'active' : 'text-white/80 hover:text-white' }}">
                        <svg class="transition-transform hover:scale-110" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9a6 6 0 00-6 6h2.25m6 0a6 6 0 01-6-6v0a6 6 0 016 6z" />
                        </svg>
                        <span class="nav-text text-xs font-medium uppercase tracking-wider">Masuk</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backButton = document.getElementById('backButton');
            if (backButton) {
                backButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.history.back();
                });
            }

            if (window.history.length <= 2) {
                backButton.style.display = 'none';
            }
        });
    </script>
@endpush