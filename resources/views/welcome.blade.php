@extends('layouts.app')

@section('title', 'Peace Picture Studio - Professional Photography')

@push('styles')
    <style>
        /* Image Assets Variables - Fixed paths to match your files */
        :root {
            --logo-image: url('{{ asset("images/white.png") }}');
            --bg-baby-smash: url('{{ asset("images/bsc.jpg") }}');
            --bg-prewedding: url('{{ asset("images/prewed.jpg") }}');
            --bg-family: url('{{ asset("images/family.jpg") }}');
            --bg-maternity: url('{{ asset("images/maternity.jpg") }}');
            --bg-graduation: url('{{ asset("images/wisuda.jpg") }}');
        }

        /* Custom Styles */
        html {
            scroll-behavior: smooth;
        }

        .carousel-slide {
            transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1), transform 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Bigger Professional Logo - Always Visible */
        .brand-logo-fixed {
            position: fixed;
            top: 2.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            transition: transform 0.3s ease;
        }

        .brand-logo-fixed:hover {
            transform: translateX(-50%) scale(1.05);
        }

        .brand-logo-fixed img {
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
        }

        /* Cinematic Ken Burns Effect */
        @keyframes kenBurns {
            0% {
                transform: scale(1.1) rotate(0deg);
                opacity: 0.9;
            }

            50% {
                transform: scale(1.15) rotate(0.5deg);
                opacity: 1;
            }

            100% {
                transform: scale(1.2) rotate(1deg);
                opacity: 0.9;
            }
        }

        .animate-ken-burns {
            animation: kenBurns 25s ease-in-out infinite alternate;
        }

        /* Professional Cinematic Overlays */
        .cinematic-overlay {
            background: linear-gradient(135deg,
                    rgba(0, 0, 0, 0.7) 0%,
                    rgba(0, 0, 0, 0.3) 30%,
                    rgba(0, 0, 0, 0.1) 50%,
                    rgba(0, 0, 0, 0.3) 70%,
                    rgba(0, 0, 0, 0.8) 100%);
        }

        .cinematic-vignette {
            background: radial-gradient(ellipse at center,
                    transparent 30%,
                    rgba(0, 0, 0, 0.3) 70%,
                    rgba(0, 0, 0, 0.7) 100%);
        }

        /* Clean Pagination Dots */
        .pagination-dot {
            width: 8px;
            height: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
        }

        .pagination-dot.active {
            width: 20px;
            border-radius: 4px;
            background: white;
        }

        .pagination-dot:not(.active) {
            background: rgba(255, 255, 255, 0.4);
        }

        .pagination-dot:hover {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.7);
        }

        /* Professional Navigation Arrows */
        .nav-arrow {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-arrow:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: scale(1.05);
        }

        /* Professional Booking Button */
        .booking-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.9rem 2rem;
            min-width: 180px;
            min-height: 48px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            color: #fff;
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(255, 255, 255, 0.06);
            cursor: pointer;
            text-align: center;
        }

        .booking-btn:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px) scale(1.03);
            color: #fff;
            box-shadow: 0 8px 24px rgba(255, 255, 255, 0.1);
        }

        .booking-btn svg {
            transition: transform 0.3s ease;
        }

        .booking-btn:hover svg {
            transform: translateX(6px);
        }




        /* Natural Elegant Typography */
        .hero-title {
            font-size: clamp(3rem, 10vw, 8rem);
            line-height: 1;
            text-shadow:
                0 2px 8px rgba(0, 0, 0, 0.6),
                0 1px 4px rgba(0, 0, 0, 0.4);
            font-weight: 400;
            letter-spacing: 0.02em;
        }

        .hero-description {
            font-size: clamp(1rem, 2vw, 1.125rem);
            line-height: 1.6;
            text-shadow:
                0 1px 4px rgba(0, 0, 0, 0.5),
                0 1px 2px rgba(0, 0, 0, 0.3);
            font-weight: 300;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 3px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 2px;
        }

        /* Accessibility & Performance */
        @media (prefers-reduced-motion: reduce) {

            .animate-ken-burns,
            .animate-fade-in-up,
            .carousel-slide {
                animation: none !important;
                transition: none !important;
            }
        }

        button:focus-visible,
        a:focus-visible {
            outline: 2px solid rgba(255, 255, 255, 0.8);
            outline-offset: 2px;
        }

        /* Mobile Optimizations */
        @media (max-width: 640px) {
            .brand-logo-fixed {
                top: 1.5rem;
            }

            .brand-logo-fixed img {
                width: 100px;
                height: auto;
            }

            .hero-content {
                padding-top: 8rem;
                padding-bottom: 8rem;
            }

            .nav-arrow {
                width: 40px;
                height: 40px;
            }

            .booking-btn {
                padding: 0.625rem 1.5rem;
                font-size: 0.75rem;
            }
        }

        @media (min-width: 768px) {
            .brand-logo-fixed img {
                width: 150px;
                height: auto;
            }
        }

        @media (min-width: 1024px) {
            .brand-logo-fixed img {
                width: 180px;
                height: auto;
            }

            .hero-content {
                padding-bottom: 10rem;
            }
        }

        @media (min-width: 1920px) {
            .brand-logo-fixed img {
                width: 200px;
                height: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="relative h-screen w-full overflow-hidden bg-black">
        <!-- Bigger Professional Logo - Always Visible -->
        <div class="brand-logo-fixed">
            <img src="{{ asset('images/white.png') }}" alt="Peace Picture Studio"
                class="w-45 sm:w-49 md:w-57 lg:w-65 xl:w-77 object-contain transform scale-110">
        </div>

        <!-- Fullscreen Hero Carousel -->
        <div id="heroCarousel" class="relative h-full w-full">

            <!-- Slide 1: Baby Smash Cake -->
            <div class="carousel-slide active absolute inset-0">
                <div class="cinematic-overlay absolute inset-0 z-10"></div>
                <div class="cinematic-vignette absolute inset-0 z-10"></div>

                <div class="absolute inset-0 scale-110 animate-ken-burns">
                    <img src="{{ asset('images/bsc.jpg') }}" alt="Baby Smash Cake Photography Session"
                        class="h-full w-full object-cover object-center" loading="eager">
                </div>

                <div
                    class="hero-content absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8 pt-20 pb-24">
                    <h2 class="hero-title text-white font-dancing mb-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                        Baby Smash Cake
                    </h2>

                    <p class="hero-description text-white/90 font-light mb-8 max-w-2xl animate-fade-in-up"
                        style="animation-delay: 0.6s;">
                        Capturing the sweetest moments of your little one's milestone
                    </p>

                    <div class="animate-fade-in-up" style="animation-delay: 0.9s;">
                        <a href="{{ route('homepage') }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 min-w-[180px] min-h-[48px] text-sm font-medium uppercase text-white bg-white/10 border border-white/20 rounded-full backdrop-blur-md hover:bg-white/20 hover:scale-105 hover:shadow-xl transition-all duration-300 group">
                            Book Session
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Slide 2: Prewedding -->
            <div class="carousel-slide absolute inset-0 opacity-0">
                <div class="cinematic-overlay absolute inset-0 z-10"></div>
                <div class="cinematic-vignette absolute inset-0 z-10"></div>

                <div class="absolute inset-0 scale-110">
                    <img src="{{ asset('images/prewed.jpg') }}" alt="Prewedding Photography Session"
                        class="h-full w-full object-cover object-center" loading="lazy">
                </div>

                <div
                    class="hero-content absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8 pt-20 pb-24">
                    <h2 class="hero-title text-white font-dancing mb-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                        Prewedding
                    </h2>

                    <p class="hero-description text-white/90 font-light mb-8 max-w-2xl animate-fade-in-up"
                        style="animation-delay: 0.6s;">
                        Eternal love stories beautifully captured before your special day
                    </p>

                    <div class="animate-fade-in-up" style="animation-delay: 0.9s;">
                        <a href="{{ route('homepage') }}" class="booking-btn group">
                            Book Session
                            <svg class="inline-block w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Family -->
            <div class="carousel-slide absolute inset-0 opacity-0">
                <div class="cinematic-overlay absolute inset-0 z-10"></div>
                <div class="cinematic-vignette absolute inset-0 z-10"></div>

                <div class="absolute inset-0 scale-110">
                    <img src="{{ asset('images/family.jpg') }}" alt="Family Photography Session"
                        class="h-full w-full object-cover object-center" loading="lazy">
                </div>

                <div
                    class="hero-content absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8 pt-20 pb-24">
                    <h2 class="hero-title text-white font-dancing mb-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                        Family
                    </h2>

                    <p class="hero-description text-white/90 font-light mb-8 max-w-2xl animate-fade-in-up"
                        style="animation-delay: 0.6s;">
                        Preserving precious family bonds and creating lasting memories
                    </p>

                    <div class="animate-fade-in-up" style="animation-delay: 0.9s;">
                        <a href="{{ route('homepage') }}" class="booking-btn group">
                            Book Session
                            <svg class="inline-block w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Slide 4: Maternity -->
            <div class="carousel-slide absolute inset-0 opacity-0">
                <div class="cinematic-overlay absolute inset-0 z-10"></div>
                <div class="cinematic-vignette absolute inset-0 z-10"></div>

                <div class="absolute inset-0 scale-110">
                    <img src="{{ asset('images/maternity.jpg') }}" alt="Maternity Photography Session"
                        class="h-full w-full object-cover object-center" loading="lazy">
                </div>

                <div
                    class="hero-content absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8 pt-20 pb-24">
                    <h2 class="hero-title text-white font-dancing mb-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                        Maternity
                    </h2>

                    <p class="hero-description text-white/90 font-light mb-8 max-w-2xl animate-fade-in-up"
                        style="animation-delay: 0.6s;">
                        Celebrating the miracle of life and the beauty of motherhood
                    </p>

                    <div class="animate-fade-in-up" style="animation-delay: 0.9s;">
                        <a href="{{ route('homepage') }}" class="booking-btn group">
                            Book Session
                            <svg class="inline-block w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Slide 5: Graduation -->
            <div class="carousel-slide absolute inset-0 opacity-0">
                <div class="cinematic-overlay absolute inset-0 z-10"></div>
                <div class="cinematic-vignette absolute inset-0 z-10"></div>

                <div class="absolute inset-0 scale-110">
                    <img src="{{ asset('images/wisuda.jpg') }}" alt="Graduation Photography Session"
                        class="h-full w-full object-cover object-center" loading="lazy">
                </div>

                <div
                    class="hero-content absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8 pt-20 pb-24">
                    <h2 class="hero-title text-white font-dancing mb-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                        Graduation
                    </h2>

                    <p class="hero-description text-white/90 font-light mb-8 max-w-2xl animate-fade-in-up"
                        style="animation-delay: 0.6s;">
                        Celebrating your academic milestone and the beginning of new adventures
                    </p>
                    <div class="relative w-full flex justify-center">
                        <div class="relative pt-[70px]">
                            <a href="{{ route('homepage') }}"
                                class="absolute top-0 left-1/2 -translate-x-1/2 inline-flex items-center justify-center gap-2 px-6 py-3 min-w-[180px] min-h-[48px] text-sm font-medium uppercase text-white bg-white/10 border border-white/20 rounded-full backdrop-blur-md hover:bg-white/20 hover:scale-105 hover:shadow-xl transition-all duration-300 group">
                                Book Session
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clean Navigation Arrows -->
            <button id="prevSlide"
                class="nav-arrow hidden md:flex absolute left-6 top-1/2 -translate-y-1/2 z-30 items-center justify-center w-12 h-12 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-white/50 group"
                aria-label="Previous slide">
                <svg class="w-5 h-5 transform group-hover:-translate-x-0.5 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path>
                </svg>
            </button>

            <button id="nextSlide"
                class="nav-arrow hidden md:flex absolute right-6 top-1/2 -translate-y-1/2 z-30 items-center justify-center w-12 h-12 rounded-full text-white focus:outline-none focus:ring-2 focus:ring-white/50 group"
                aria-label="Next slide">
                <svg class="w-5 h-5 transform group-hover:translate-x-0.5 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                </svg>
            </button>
        </div>

        <!-- Clean Pagination Dots -->
        <div class="absolute bottom-20 left-1/2 -translate-x-1/2 z-30 flex items-center space-x-3">
            <button class="pagination-dot active" data-slide="0" aria-label="Go to slide 1"></button>
            <button class="pagination-dot" data-slide="1" aria-label="Go to slide 2"></button>
            <button class="pagination-dot" data-slide="2" aria-label="Go to slide 3"></button>
            <button class="pagination-dot" data-slide="3" aria-label="Go to slide 4"></button>
            <button class="pagination-dot" data-slide="4" aria-label="Go to slide 5"></button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.pagination-dot');
            const prevBtn = document.getElementById('prevSlide');
            const nextBtn = document.getElementById('nextSlide');

            let currentSlide = 0;
            let autoplayInterval;
            let isTransitioning = false;
            const autoplayDuration = 3000;

            function showSlide(index, direction = 'next') {
                if (isTransitioning) return;
                isTransitioning = true;

                // Hide all slides
                slides.forEach((slide, i) => {
                    slide.classList.remove('active');
                    slide.style.opacity = '0';
                    slide.style.transform = direction === 'next' ? 'scale(1.05)' : 'scale(0.95)';

                    const elements = slide.querySelectorAll('.animate-fade-in-up');
                    elements.forEach(el => {
                        el.classList.remove('visible');
                    });
                });

                // Update dots
                dots.forEach((dot, i) => {
                    dot.classList.remove('active');
                });

                // Show current slide with smooth transition
                setTimeout(() => {
                    slides[index].classList.add('active');
                    slides[index].style.opacity = '1';
                    slides[index].style.transform = 'scale(1)';

                    const elements = slides[index].querySelectorAll('.animate-fade-in-up');
                    elements.forEach((el, i) => {
                        setTimeout(() => {
                            el.classList.add('visible');
                        }, i * 150);
                    });

                    // Update active dot
                    dots[index].classList.add('active');

                    currentSlide = index;
                    isTransitioning = false;
                }, 200);
            }

            function nextSlide() {
                if (isTransitioning) return;
                const next = (currentSlide + 1) % slides.length;
                showSlide(next, 'next');
            }

            function prevSlide() {
                if (isTransitioning) return;
                const prev = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(prev, 'prev');
            }

            function startAutoplay() {
                clearInterval(autoplayInterval);
                autoplayInterval = setInterval(nextSlide, autoplayDuration);
            }

            function stopAutoplay() {
                clearInterval(autoplayInterval);
            }

            // Event listeners with debouncing
            let clickTimeout;

            nextBtn.addEventListener('click', () => {
                if (clickTimeout) return;
                clickTimeout = setTimeout(() => {
                    clickTimeout = null;
                }, 300);

                nextSlide();
                stopAutoplay();
                setTimeout(startAutoplay, 2000);
            });

            prevBtn.addEventListener('click', () => {
                if (clickTimeout) return;
                clickTimeout = setTimeout(() => {
                    clickTimeout = null;
                }, 300);

                prevSlide();
                stopAutoplay();
                setTimeout(startAutoplay, 2000);
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    if (isTransitioning || index === currentSlide) return;

                    const direction = index > currentSlide ? 'next' : 'prev';
                    showSlide(index, direction);
                    stopAutoplay();
                    setTimeout(startAutoplay, 2000);
                });
            });

            // Enhanced touch/swipe support
            let startX = 0;
            let startY = 0;
            let endX = 0;
            let endY = 0;

            document.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            }, { passive: true });

            document.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].clientX;
                endY = e.changedTouches[0].clientY;

                const diffX = startX - endX;
                const diffY = Math.abs(startY - endY);

                // Only trigger if horizontal swipe is dominant and significant
                if (Math.abs(diffX) > 50 && diffY < 100) {
                    if (diffX > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                    stopAutoplay();
                    setTimeout(startAutoplay, 2000);
                }
            }, { passive: true });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    prevSlide();
                    stopAutoplay();
                    setTimeout(startAutoplay, 2000);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    nextSlide();
                    stopAutoplay();
                    setTimeout(startAutoplay, 2000);
                } else if (e.key === ' ') {
                    e.preventDefault();
                    if (autoplayInterval) {
                        stopAutoplay();
                    } else {
                        startAutoplay();
                    }
                }
            });

            // Hover controls
            const carousel = document.getElementById('heroCarousel');
            carousel.addEventListener('mouseenter', stopAutoplay);
            carousel.addEventListener('mouseleave', startAutoplay);

            // Intersection Observer for performance
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        startAutoplay();
                    } else {
                        stopAutoplay();
                    }
                });
            });
            observer.observe(carousel);

            // Initialize
            showSlide(0);
            startAutoplay();

            // Cleanup
            window.addEventListener('beforeunload', () => {
                stopAutoplay();
                observer.disconnect();
            });
        });
    </script>
@endpush