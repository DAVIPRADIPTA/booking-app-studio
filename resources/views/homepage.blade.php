@extends('layouts.app')
@section('title', 'Choose Your Session - Peace Picture Studio')

@push('styles')
<style>
    /* Import Dancing Script Font */
    @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');
    
    /* Base Styles - Minimalist & Precise */
    html, body {
        height: 100%;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        overflow-x: hidden;
        background: #000;
        margin: 0;
        padding: 0;
    }
    
    .font-dancing {
        font-family: 'Dancing Script', cursive;
    }
    
    /* Precise Container - Fixed Desktop */
    .main-container {
        height: 100vh;
        position: relative;
        overflow: hidden;
    }
    
    @media (max-width: 1023px) {
        .main-container {
            min-height: 100vh;
            height: auto;
            overflow-y: auto;
            padding-bottom: 5rem;
        }
    }
    
    /* Elegant Background */
    .cinematic-bg {
        background-position: center;
        background-size: cover;
        position: fixed;
        inset: 0;
        z-index: 0;
        transform: translateZ(0);
    }
    
    .cinematic-overlay {
        background: linear-gradient(135deg, 
            rgba(0, 0, 0, 0.72) 0%, 
            rgba(0, 0, 0, 0.58) 25%, 
            rgba(0, 0, 0, 0.45) 50%, 
            rgba(0, 0, 0, 0.58) 75%, 
            rgba(0, 0, 0, 0.72) 100%);
        position: absolute;
        inset: 0;
        z-index: 1;
    }
    
    .cinematic-vignette {
        background: radial-gradient(ellipse at center, 
            rgba(0, 0, 0, 0.0) 0%, 
            rgba(0, 0, 0, 0.12) 50%, 
            rgba(0, 0, 0, 0.35) 100%);
        position: absolute;
        inset: 0;
        z-index: 2;
    }
    
    /* Tambahkan media query untuk mobile */
    @media (max-width: 1023px) {
        .cinematic-bg {
            position: absolute; /* Ubah menjadi absolute di mobile */
            height: 100%; /* Pastikan mengisi tinggi parent */
        }
        .cinematic-overlay,
        .cinematic-vignette {
            position: absolute; /* Pastikan overlay juga absolute */
            height: 100%;
        }
    }
    
    /* Refined Side Galleries */
    .side-panel {
        position: fixed;
        top: 0;
        bottom: 0;
        width: 6rem;
        background: rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(6px);
        z-index: 3;
        display: none;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
        transform: translateZ(0);
    }
    
    @media (min-width: 1280px) {
        .side-panel {
            display: block;
        }
        .main-content {
            margin-left: 6rem;
            margin-right: 6rem;
        }
    }
    
    @media (min-width: 1440px) {
        .side-panel {
            width: 7rem;
        }
        .main-content {
            margin-left: 7rem;
            margin-right: 7rem;
        }
    }
    
    @media (min-width: 1920px) {
        .side-panel {
            width: 8rem;
        }
        .main-content {
            margin-left: 8rem;
            margin-right: 8rem;
        }
    }
    
    /* Minimalist Gallery System */
    .gallery-container {
        height: 100%;
        overflow: hidden;
        position: relative;
    }
    
    .gallery-scroll {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 1rem 0.75rem;
        position: absolute;
        width: 100%;
        transform: translateZ(0);
        backface-visibility: hidden;
        /* Animation duration set by JS */
    }
    
    .gallery-scroll.up {
        animation: scrollUpSeamless var(--scroll-duration, 100s) linear infinite;
    }
    
    .gallery-scroll.down {
        animation: scrollDownSeamless var(--scroll-duration, 110s) linear infinite;
    }
    
    .gallery-item {
        flex-shrink: 0;
        padding: 0.25rem;
        transform: translateZ(0);
    }
    
    .gallery-item img {
        width: 100%;
        height: 4rem;
        object-fit: cover;
        border-radius: 0.5rem;
        opacity: 0.3;
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: opacity 0.3s ease;
        transform: translateZ(0);
    }
    
    @media (min-width: 1440px) {
        .gallery-item img {
            height: 4.5rem;
        }
    }
    
    @media (min-width: 1920px) {
        .gallery-item img {
            height: 5rem;
        }
    }
    
    .gallery-item:hover img {
        opacity: 0.6;
    }
    
    /* Precise Service Cards */
    .service-card {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 1rem;
        backdrop-filter: blur(10px);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        transform: translateZ(0);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Precise Desktop Heights */
    @media (min-width: 1024px) {
        .service-card {
            height: 200px;
            padding: 1.5rem;
        }
    }
    
    /* Mobile Responsive Heights */
    @media (max-width: 1023px) {
        .service-card {
            min-height: 160px;
            padding: 1.25rem;
        }
    }
    
    @media (max-width: 640px) {
        .service-card {
            min-height: 140px;
            padding: 1rem;
        }
    }
    
    .service-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, 
            rgba(255, 255, 255, 0.08) 0%, 
            rgba(255, 255, 255, 0.04) 100%);
        opacity: 0;
        transition: opacity 0.35s ease;
        z-index: 1;
    }
    
    .service-card:hover::before,
    .service-card.selected::before {
        opacity: 1;
    }
    
    .service-card:hover {
        transform: translateZ(0) translateY(-3px) scale(1.01);
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
    }
    
    .service-card.selected {
        transform: translateZ(0) translateY(-3px) scale(1.01);
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.25);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
    }
    
    /* Card Content Layout */
    .card-content {
        position: relative;
        z-index: 10;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 0.75rem;
        text-align: center;
    }
    
    /* Elegant Category Names */
    .service-title {
        font-size: 1.5rem;
        font-weight: 500;
        color: white;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.6);
        letter-spacing: -0.01em;
        transition: all 0.3s ease;
    }
    
    @media (min-width: 768px) {
        .service-title {
            font-size: 1.75rem;
        }
    }
    
    @media (min-width: 1024px) {
        .service-title {
            font-size: 2rem;
        }
    }
    
    .service-card:hover .service-title {
        transform: scale(1.02);
        text-shadow: 0 3px 15px rgba(0, 0, 0, 0.7);
    }
    
    .service-description {
        color: rgba(255, 255, 255, 0.75);
        font-weight: 300;
        font-size: 0.8rem;
        line-height: 1.4;
        max-width: 14rem;
        margin: 0;
        text-shadow: 0 1px 6px rgba(0, 0, 0, 0.5);
    }
    
    @media (min-width: 768px) {
        .service-description {
            font-size: 0.875rem;
            max-width: 16rem;
        }
    }
    
    @media (min-width: 1024px) {
        .service-description {
            font-size: 0.9rem;
            max-width: 18rem;
        }
    }
    
    /* Minimalist Booking Button */
    .booking-btn-perfect {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #fff;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1.5rem;
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        min-width: 110px;
        height: 2rem;
        flex-shrink: 0;
        transform: translateZ(0);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        margin-top: 0.5rem;
    }
    
    @media (min-width: 768px) {
        .booking-btn-perfect {
            font-size: 0.8rem;
            padding: 0.6rem 1.5rem;
            height: 2.25rem;
            min-width: 120px;
        }
    }
    
    @media (min-width: 1024px) {
        .booking-btn-perfect {
            font-size: 0.85rem;
            padding: 0.65rem 1.75rem;
            height: 2.5rem;
            min-width: 130px;
        }
    }
    
    .booking-btn-perfect::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.4s ease;
    }
    
    .booking-btn-perfect:hover::before {
        left: 100%;
    }
    
    .booking-btn-perfect:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateZ(0) translateY(-1px) scale(1.02);
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.2);
    }
    
    .booking-btn-perfect svg {
        transition: transform 0.3s ease;
        position: relative;
        z-index: 10;
        width: 0.875rem;
        height: 0.875rem;
    }
    
    .booking-btn-perfect:hover svg {
        transform: translateX(2px);
    }
    
    .booking-btn-perfect span {
        position: relative;
        z-index: 10;
    }
    
    /* Refined Header Section */
    .studio-brand {
        font-size: 0.8rem;
        font-weight: 300;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.75rem;
        filter: drop-shadow(0 1px 6px rgba(0, 0, 0, 0.6));
        animation: brandSlideIn 1s ease-out;
    }
    
    @media (min-width: 768px) {
        .studio-brand {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    }
    
    .hero-title {
        font-size: 2.5rem;
        line-height: 1.1;
        background: linear-gradient(135deg, 
            #ffffff 0%, 
            #f8fafc 40%, 
            #e2e8f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 400;
        letter-spacing: -0.02em;
        margin-bottom: 1rem;
        filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.7));
        animation: titleSlideIn 1.2s ease-out 0.3s both;
    }
    
    @media (min-width: 768px) {
        .hero-title {
            font-size: 3.5rem;
            margin-bottom: 1.25rem;
        }
    }
    
    @media (min-width: 1024px) {
        .hero-title {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
    }
    
    .hero-description {
        font-size: 0.9rem;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 300;
        max-width: 28rem;
        filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.5));
        animation: descriptionFadeIn 1s ease-out 0.6s both;
    }
    
    @media (min-width: 768px) {
        .hero-description {
            font-size: 1rem;
            max-width: 32rem;
        }
    }
    
    @media (min-width: 1024px) {
        .hero-description {
            font-size: 1.1rem;
            max-width: 36rem;
        }
    }
    
    /* Precise Content Layout */
    .content-wrapper {
        position: relative;
        z-index: 20;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1.5rem;
        overflow: hidden;
    }
    
    @media (max-width: 1023px) {
        .content-wrapper {
            height: auto;
            min-height: 100vh;
            justify-content: flex-start;
            padding: 2rem 1rem 6rem;
            overflow: visible;
        }
    }
    
    .hero-section {
        margin-bottom: 2rem;
        text-align: center;
    }
    
    @media (min-width: 768px) {
        .hero-section {
            margin-bottom: 2.5rem;
        }
    }
    
    @media (min-width: 1024px) {
        .hero-section {
            margin-bottom: 3rem;
        }
    }
    
    .services-grid {
        gap: 1.25rem;
        max-width: 60rem;
        width: 100%;
        margin: 0 auto;
    }
    
    @media (min-width: 768px) {
        .services-grid {
            gap: 1.5rem;
        }
    }
    
    @media (min-width: 1024px) {
        .services-grid {
            gap: 1.75rem;
            max-width: 64rem;
        }
    }
    
    /* Elegant Welcome Message */
    .welcome-hint {
        margin-top: 1.5rem;
        text-align: center;
    }
    
    @media (min-width: 768px) {
        .welcome-hint {
            margin-top: 2rem;
        }
    }
    
    .welcome-hint p {
        color: rgba(255, 255, 255, 0.45);
        font-size: 0.7rem;
        font-weight: 300;
        letter-spacing: 0.05em;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
    }
    
    @media (min-width: 768px) {
        .welcome-hint p {
            font-size: 0.75rem;
        }
    }
    
    /* Smooth Animations */
    @keyframes brandSlideIn {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes titleSlideIn {
        from { opacity: 0; transform: translateY(-20px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    @keyframes descriptionFadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    .animate-fade-in:nth-child(1) { animation-delay: 0.8s; }
    .animate-fade-in:nth-child(2) { animation-delay: 0.95s; }
    .animate-fade-in:nth-child(3) { animation-delay: 1.1s; }
    
    /* Gallery Animations - Adjusted for true seamless loop */
    @keyframes scrollUpSeamless {
        0% { transform: translateY(0); }
        100% { transform: translateY(-100%); } /* Animate full height of one set */
    }
    
    @keyframes scrollDownSeamless {
        0% { transform: translateY(-100%); } /* Start from end of duplicated content */
        100% { transform: translateY(0); } /* Animate back to start */
    }
    
    /* Loading State */
    .loading {
        position: relative;
        pointer-events: none;
        opacity: 0.7;
    }
    
    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 12px;
        height: 12px;
        border: 1.5px solid rgba(255, 255, 255, 0.25);
        border-top: 1.5px solid white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    /* Performance & Accessibility */
    @media (prefers-reduced-motion: reduce) {
        .animate-fade-in,
        .gallery-scroll,
        .studio-brand,
        .hero-title,
        .hero-description {
            animation: none !important;
        }
        
        .service-card,
        .booking-btn-perfect {
            transition: none !important;
        }
    }
    
    .service-card:focus-visible,
    .booking-btn-perfect:focus-visible {
        outline: 2px solid rgba(255, 255, 255, 0.6);
        outline-offset: 2px;
    }
    
    /* Gallery Pause on Hover */
    .side-panel:hover .gallery-scroll {
        animation-play-state: paused;
    }
    
    /* Mobile Touch Optimization */
    @media (hover: none) and (pointer: coarse) {
        .service-card:hover {
            transform: none;
        }
        
        .service-card:active {
            transform: translateZ(0) translateY(-2px) scale(1.01);
        }
        
        .gallery-scroll {
            animation-duration: 140s !important;
        }
    }
</style>
@endpush

@section('content')
<div class="main-container w-full">
    <!-- Cinematic Background -->
    <div class="cinematic-bg" style="background-image: url('{{ asset('images/prewed.jpg') }}');">
        <div class="cinematic-overlay"></div>
        <div class="cinematic-vignette"></div>
    </div>
    
    <!-- Side Galleries -->
    <div class="side-panel" style="left: 0;">
        <div class="gallery-container">
            <div class="gallery-scroll up" id="leftGallery"></div>
        </div>
    </div>
    <div class="side-panel" style="right: 0;">
        <div class="gallery-container">
            <div class="gallery-scroll down" id="rightGallery"></div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="content-wrapper main-content">
        <div class="w-full max-w-5xl mx-auto">
            <!-- Hero Section -->
            <div class="hero-section">
                <div class="studio-brand">peacephotostudio</div>
                <h1 class="hero-title font-dancing text-white">
                    Choose Your Session
                </h1>
                <p class="hero-description text-white/80 mx-auto">
                    Professional photography experiences crafted for life's most precious moments
                </p>
            </div>
            
            <!-- Services Grid -->
            <div class="services-grid grid grid-cols-1 lg:grid-cols-3">
                <!-- Pre-Wedding Package -->
                <div class="service-card animate-fade-in" data-service="prewed">
                    <div class="card-content">
                        <h3 class="service-title font-dancing">Pre-Wedding</h3>
                        <p class="service-description">
                            Romantic sessions capturing your love story with timeless elegance
                        </p>
                        <a href="/kategori/prewed" class="booking-btn-perfect" data-url="/kategori/prewed">
                            <span>Book Session</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Group Session Package -->
                <div class="service-card animate-fade-in" data-service="group">
                    <div class="card-content">
                        <h3 class="service-title font-dancing">Group Session</h3>
                        <p class="service-description">
                            Family and group portraits with perfect composition and natural moments
                        </p>
                        <a href="/kategori/group" class="booking-btn-perfect" data-url="/kategori/group">
                            <span>Book Session</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Baby Smash Cake Package -->
                <div class="service-card animate-fade-in" data-service="bsc">
                    <div class="card-content">
                        <h3 class="service-title font-dancing">Baby Smash Cake</h3>
                        <p class="service-description">
                            Adorable first birthday celebrations with playful cake smashing
                        </p>
                        <a href="/kategori/baby" class="booking-btn-perfect" data-url="/kategori/baby">
                            <span>Book Session</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Welcome Hint -->
            <div class="welcome-hint">
                <p>Press 1, 2, or 3 for quick access • Welcome to Peace Picture Studio</p>
            </div>
        </div>
    </div>
    
    <!-- Bottom Navigation Component -->
    <x-bottom-nav current-route="homepage" />
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Optimized Gallery Images - Reverted to original full list for visual consistency
    const galleryImages = [
        '{{ asset("images/prewed/1.jpg") }}',
        '{{ asset("images/prewed/2.jpg") }}',
        '{{ asset("images/prewed/3.jpg") }}',
        '{{ asset("images/prewed/4.jpg") }}',
        '{{ asset("images/prewed/5.jpg") }}',
        '{{ asset("images/family/1.jpg") }}',
        '{{ asset("images/family/2.jpg") }}',
        '{{ asset("images/family/3.jpg") }}',
        '{{ asset("images/bsc/1.jpg") }}',
        '{{ asset("images/bsc/2.jpg") }}',
        '{{ asset("images/maternity/1.jpg") }}',
        '{{ asset("images/maternity/2.jpg") }}',
        '{{ asset("images/wisuda/1.jpg") }}',
        '{{ asset("images/wisuda/2.jpg") }}'
    ];
    
    // Refined Gallery System for true seamless loop
    function createRefinedGallery() {
        const leftGallery = document.getElementById('leftGallery');
        const rightGallery = document.getElementById('rightGallery');
        
        if (!leftGallery || !rightGallery) return;
        
        // Duplicate the images array ONCE for seamless loop
        const duplicatedImages = [...galleryImages, ...galleryImages];
        
        const createItems = (container, images) => {
            container.innerHTML = ''; // Clear existing content
            images.forEach((img, index) => {
                const item = document.createElement('div');
                item.className = 'gallery-item';
                item.innerHTML = `<img src="${img}" alt="Gallery ${index + 1}" loading="lazy" decoding="async">`;
                container.appendChild(item);
            });
        };
        
        createItems(leftGallery, duplicatedImages);
        createItems(rightGallery, [...duplicatedImages].reverse());
        
        // Calculate height of a SINGLE set of images for animation
        // This is crucial for the -100% translateY animation to work correctly
        // 4rem (64px) img height + 0.25rem (4px) top/bottom padding on gallery-item = 72px item content height
        // 0.5rem (8px) gap between items
        // 1rem (16px) top/bottom padding on gallery-scroll container
        // So, each item effectively takes up 72px + 8px = 80px in vertical space within the scroll container.
        // The 1rem padding on gallery-scroll itself is handled by the container's overall height.
        const itemEffectiveHeight = 64 + (0.25 * 16 * 2) + (0.5 * 16); // 64px (img) + 8px (item padding) + 8px (gap) = 80px
        const singleSetHeight = galleryImages.length * itemEffectiveHeight;
        
        // Set the height of the gallery-scroll container to the height of ONE set of images
        leftGallery.style.height = `${singleSetHeight}px`;
        rightGallery.style.height = `${singleSetHeight}px`;

        // Refined animation durations based on single set height
        const baseSpeed = 8; // Adjust this value to control overall scroll speed
        leftGallery.style.setProperty('--scroll-duration', `${singleSetHeight / baseSpeed}s`);
        rightGallery.style.setProperty('--scroll-duration', `${singleSetHeight / baseSpeed * 1.1}s`); // Slightly different speed for variety
    }
    
    // Initialize gallery
    createRefinedGallery();
    
    // State Management
    let selectedService = null;
    
    // DOM Elements
    const serviceCards = document.querySelectorAll('.service-card');
    const bookingButtons = document.querySelectorAll('.booking-btn-perfect');
    
    // Package URLs
    const packageUrls = {
        'prewed': '/kategori/prewed',
        'group': '/kategori/group',
        'bsc': '/kategori/baby'
    };
    
    // Button Handlers
    bookingButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const url = this.getAttribute('data-url');
            this.classList.add('loading');
            
            // Haptic feedback
            if (navigator.vibrate) {
                navigator.vibrate(30);
            }
            
            // Navigate
            setTimeout(() => {
                window.location.href = url;
            }, 250);
        });
    });
    
    // Service Card Selection
    serviceCards.forEach(card => {
        card.addEventListener('click', function() {
            const service = this.getAttribute('data-service');
            
            // Remove selection from all cards
            serviceCards.forEach(c => c.classList.remove('selected'));
            
            // Add selection to clicked card
            this.classList.add('selected');
            
            // Update state
            selectedService = service;
        });
    });
    
    // Keyboard Navigation
    document.addEventListener('keydown', function(e) {
        if (e.target.matches('input, textarea, select')) return;
        
        if (e.key >= '1' && e.key <= '3') {
            e.preventDefault();
            const index = parseInt(e.key) - 1;
            if (serviceCards[index]) {
                const button = serviceCards[index].querySelector('.booking-btn-perfect');
                if (button) {
                    button.click();
                }
            }
        } else if (e.key === 'Enter' && selectedService) {
            e.preventDefault();
            window.location.href = packageUrls[selectedService];
        }
    });
    
    // Performance optimizations
    function optimizePerformance() {
        const isMobile = window.innerWidth < 1280;
        const galleries = document.querySelectorAll('.gallery-scroll');
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        if (isMobile || prefersReducedMotion) {
            galleries.forEach(gallery => {
                gallery.style.animationPlayState = 'paused';
            });
        } else {
            galleries.forEach(gallery => {
                gallery.style.animationPlayState = 'running';
            });
        }
    }
    
    // Throttled resize handler
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            optimizePerformance();
            createRefinedGallery(); // Re-create gallery on resize to adjust heights
        }, 200);
    });
    
    // Initialize optimizations
    optimizePerformance();
    
    // Intersection Observer
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.willChange = 'transform';
                } else {
                    entry.target.style.willChange = 'auto';
                }
            });
        }, { 
            threshold: 0.1,
            rootMargin: '30px'
        });
        
        serviceCards.forEach(card => observer.observe(card));
    }
    
    // Preload critical images
    const preloadImages = [
        '{{ asset("images/prewed/1.jpg") }}',
        '{{ asset("images/family/1.jpg") }}',
        '{{ asset("images/bsc/1.jpg") }}'
    ];
    
    preloadImages.forEach(src => {
        const img = new Image();
        img.src = src;
    });
    
    console.log('✨ Elegant & Minimalist Homepage Initialized!');
});
</script>
@endpush
