@extends('layouts.app')
@section('title', 'Info More - Peace Picture Studio')

@push('styles')
<style>
    /* Import Dancing Script Font */
    @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

    /* Base Styles - Clean & Optimized */
    html,
    body {
        height: 100%;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        overflow-x: hidden;
        overflow-y: auto;
        background: #000;
        margin: 0;
        padding: 0;
        scroll-behavior: smooth;
    }

    .font-dancing {
        font-family: 'Dancing Script', cursive;
    }

    /* Main Container */
    .main-container {
        min-height: 100vh;
        position: relative;
        padding-bottom: 5rem;
        overflow: visible;
    }

    /* Clean Professional Background - More Visible */
    .cinematic-bg {
        background-image: url('{{ asset("images/info.jpg") }}');
        background-position: center center;
        background-size: cover;
        background-repeat: no-repeat;
        position: absolute;
        inset: 0;
        z-index: 0;
        filter: brightness(0.6) contrast(1.1) saturate(0.9);
    }

    /* Lighter Dark Overlay - More Background Visible */
    .cinematic-overlay {
        background: linear-gradient(135deg,
                rgba(0, 0, 0, 0.65) 0%,
                rgba(0, 0, 0, 0.45) 50%,
                rgba(0, 0, 0, 0.65) 100%);
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    /* Content Wrapper - Clean */
    .content-wrapper {
        position: relative;
        z-index: 20;
        padding: clamp(0.5rem, 1vw, 1rem) clamp(1rem, 2vw, 1.5rem) clamp(5rem, 6vw, 6rem);
        min-height: 100vh;
        overflow: visible;
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 100vw;
    }

    /* Hero Section - Elegant */
    .hero-section {
        text-align: center;
        margin-bottom: clamp(2rem, 4vw, 3rem);
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 900px;
        padding: clamp(0.5rem, 1vw, 1rem) clamp(1.5rem, 3vw, 2rem);
        animation: fadeInUp 0.8s ease-out;
    }

    /* Studio Logo Image */
    .studio-logo {
        width: clamp(150px, 30vw, 220px);
        height: auto;
        margin: 0 auto clamp(1rem, 2vw, 1.5rem);
        display: block;
        filter: drop-shadow(0 4px 20px rgba(0, 0, 0, 0.9));
        transition: all 0.3s ease;
    }

    .studio-logo:hover {
        transform: scale(1.02);
        filter: drop-shadow(0 6px 25px rgba(0, 0, 0, 0.9));
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .studio-brand {
        font-size: clamp(0.9rem, 1.5vw, 1.1rem);
        font-weight: 400;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.95);
        margin-bottom: clamp(0.5rem, 1vw, 0.8rem);
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
    }

    .studio-subtitle {
        font-size: clamp(0.7rem, 1.2vw, 0.85rem);
        font-weight: 300;
        letter-spacing: 0.3em;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: clamp(2rem, 4vw, 3rem);
        text-shadow: 0 1px 6px rgba(0, 0, 0, 0.8);
    }

    /* Clean Typography */
    .hero-title {
        font-size: clamp(3.5rem, 10vw, 6rem);
        line-height: 0.9;
        color: white;
        font-weight: 400;
        margin-bottom: clamp(2rem, 4vw, 3rem);
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.9);
        position: relative;
    }

    /* Content Grid */
    .content-grid {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(2rem, 4vw, 3rem);
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        position: relative;
        padding: 0 clamp(1rem, 2vw, 1.5rem);
    }

    /* Contact Info Cards */
    .contact-info-card {
        background: rgba(0, 0, 0, 0.25);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: clamp(1.2rem, 2.5vw, 1.8rem);
        padding: clamp(1.5rem, 3vw, 2rem);
        backdrop-filter: blur(40px) saturate(1.2);
        position: relative;
        overflow: hidden;
        box-shadow: 0 30px 100px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        width: 100%;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-top: -30px;
    }

    .contact-info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 40px 120px rgba(0, 0, 0, 0.7);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: clamp(1rem, 2.5vw, 1.5rem);
        margin-bottom: clamp(1.5rem, 3vw, 2rem);
        transition: all 0.3s ease;
    }

    .contact-item:last-child {
        margin-bottom: 0;
    }

    .contact-item:hover {
        transform: translateX(8px);
    }

    .contact-icon {
        width: clamp(2.5rem, 5vw, 3rem);
        height: clamp(2.5rem, 5vw, 3rem);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: clamp(1.2rem, 2.5vw, 1.5rem);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .contact-item:hover .contact-icon {
        transform: scale(1.1);
    }

    /* Instagram */
    .instagram-icon {
        background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
    }

    /* TikTok */
    .tiktok-icon {
        background: linear-gradient(45deg, #000000 0%, #ff0050 50%, #000000 100%);
    }

    /* WhatsApp */
    .whatsapp-icon {
        background: linear-gradient(45deg, #25d366 0%, #128c7e 100%);
    }

    /* Location */
    .location-icon {
        background: linear-gradient(45deg, #dc2626 0%, #b91c1c 100%);
    }

    .contact-text {
        flex: 1;
    }

    .contact-label {
        font-size: clamp(0.8rem, 1.3vw, 0.9rem);
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.2rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 500;
    }

    .contact-value {
        font-size: clamp(1rem, 1.8vw, 1.2rem);
        color: white;
        font-weight: 600;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        line-height: 1.3;
    }

    /* Suggestions Section */
    .suggestions-section {
        background: rgba(0, 0, 0, 0.25);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: clamp(1.5rem, 2.5vw, 2rem);
        padding: clamp(2rem, 4vw, 3rem);
        backdrop-filter: blur(40px) saturate(1.2);
        position: relative;
        overflow: hidden;
        box-shadow: 0 30px 100px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        width: 100%;
        margin-top: clamp(2rem, 4vw, 3rem);
    }

    .suggestions-title {
        font-size: clamp(1.8rem, 3.5vw, 2.3rem);
        font-weight: 600;
        color: white;
        text-align: center;
        margin-bottom: clamp(1.5rem, 3vw, 2rem);
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.6);
    }

    .suggestions-description {
        font-size: clamp(0.9rem, 1.5vw, 1rem);
        color: rgba(255, 255, 255, 0.8);
        text-align: center;
        margin-bottom: clamp(2rem, 4vw, 2.5rem);
        line-height: 1.6;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
    }

    .suggestions-form {
        display: flex;
        flex-direction: column;
        gap: clamp(1.5rem, 3vw, 2rem);
    }

    .form-group {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .form-label {
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        font-weight: 500;
        color: rgba(255, 255, 255, 0.95);
        margin-bottom: 0.8rem;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
    }

    .form-input,
    .form-textarea {
        padding: clamp(0.9rem, 2vw, 1.2rem) clamp(1.2rem, 2.5vw, 1.5rem);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
        background: rgba(255, 255, 255, 0.15);
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        color: white;
        transition: all 0.3s ease;
        backdrop-filter: blur(25px);
        font-family: inherit;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .form-textarea {
        min-height: clamp(120px, 20vw, 160px);
        resize: vertical;
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.7);
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .submit-btn {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        border: 2px solid rgba(220, 38, 38, 0.6);
        border-radius: clamp(0.8rem, 1.5vw, 1rem);
        padding: clamp(1rem, 2vw, 1.3rem) clamp(1.5rem, 3vw, 2rem);
        color: white;
        font-size: clamp(0.9rem, 1.5vw, 1rem);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(30px);
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        align-self: center;
        min-width: 200px;
    }

    .submit-btn:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 12px 35px rgba(220, 38, 38, 0.5);
        border-color: rgba(220, 38, 38, 0.8);
    }

    .submit-btn:active {
        transform: translateY(0) scale(1.02);
    }

    /* Success Message */
    .success-message {
        background: linear-gradient(135deg,
                rgba(34, 197, 94, 0.30),
                rgba(34, 197, 94, 0.18));
        border: 1px solid rgba(34, 197, 94, 0.6);
        border-radius: clamp(0.7rem, 1.4vw, 1rem);
        padding: clamp(1.2rem, 2.5vw, 1.8rem);
        margin-bottom: 1.8rem;
        color: #86efac;
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        text-align: center;
        display: none;
        backdrop-filter: blur(30px);
        box-shadow: 0 20px 50px rgba(34, 197, 94, 0.3);
        position: relative;
        z-index: 10;
    }

    .success-message.show {
        display: block;
        animation: slideInDown 0.5s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Enhanced Text Shadows for Better Readability */
    .studio-brand,
    .studio-subtitle,
    .hero-title {
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.9), 
                 0 4px 24px rgba(0, 0, 0, 0.6),
                 0 1px 3px rgba(0, 0, 0, 1);
    }

    .contact-label,
    .contact-value,
    .suggestions-title,
    .suggestions-description,
    .form-label {
        text-shadow: 0 1px 6px rgba(0, 0, 0, 0.8), 
                 0 2px 12px rgba(0, 0, 0, 0.5);
    }

    /* Enhanced Card Backgrounds for Better Contrast */
    .contact-info-card,
    .suggestions-section {
        background: rgba(0, 0, 0, 0.25);
        border: 2px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(40px) saturate(1.2);
        box-shadow: 0 30px 100px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    }

    .contact-info-card:hover,
    .suggestions-section:hover {
        background: rgba(0, 0, 0, 0.35);
        border-color: rgba(255, 255, 255, 0.4);
    }

    /* Responsive Breakpoints */
    @media (max-width: 640px) {
        .contact-item {
            gap: 1rem;
        }
        
        .contact-icon {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 1.2rem;
        }
    }

    @media (max-width: 480px) {
        .content-wrapper {
            padding: 1.5rem 1rem 5rem;
        }
        
        .hero-title {
            font-size: 3rem;
        }
    }

    /* Accessibility */
    .contact-info-card:focus-visible,
    .form-input:focus-visible,
    .form-textarea:focus-visible,
    .submit-btn:focus-visible {
        outline: 3px solid rgba(255, 255, 255, 0.8);
        outline-offset: 4px;
    }

    /* Performance Optimizations */
    * {
        will-change: auto;
    }

    .contact-info-card,
    .suggestions-section {
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    /* Reduced Motion Support */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endpush

@section('content')
<div class="main-container w-full">
    <!-- Clean Background -->
    <div class="cinematic-bg">
        <div class="cinematic-overlay"></div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Hero Section -->
        <div class="hero-section">
            <img src="{{ asset('images/white.png') }}" alt="Peace Picture Studio" class="studio-logo">
            <h1 class="hero-title font-dancing">Info More</h1>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Contact Information Card -->
            <div class="contact-info-card">
                <!-- Instagram -->
                <div class="contact-item" onclick="window.open('https://instagram.com/peacephotostudio', '_blank')">
                    <div class="contact-icon instagram-icon">
                        <svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <div class="contact-text">
                        <div class="contact-label">Instagram</div>
                        <div class="contact-value">@peacephotostudio</div>
                    </div>
                </div>

                <!-- TikTok -->
                <div class="contact-item" onclick="window.open('https://tiktok.com/@peacephotostudio', '_blank')">
                    <div class="contact-icon tiktok-icon">
                        <svg fill="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                        </svg>
                    </div>
                    <div class="contact-text">
                        <div class="contact-label">TikTok</div>
                        <div class="contact-value">peacephotostudio</div>
                    </div>
                </div>

                <!-- WhatsApp -->
                <div class="contact-item" onclick="window.open('https://wa.me/6285782086279', '_blank')">
                    <div class="contact-icon whatsapp-icon">
                        <svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.488"/>
                        </svg>
                    </div>
                    <div class="contact-text">
                        <div class="contact-label">WhatsApp</div>
                        <div class="contact-value">085782086279</div>
                    </div>
                </div>

                <!-- Location -->
                <div class="contact-item" onclick="window.open('https://maps.google.com/?q=Jl.+Raya+II+SINGKIL,+Tegal,+Central+Java', '_blank')">
                    <div class="contact-icon location-icon">
                        <svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                    <div class="contact-text">
                        <div class="contact-label">Location</div>
                        <div class="contact-value">Jl. Raya II SINGKIL, Tegal, Central Java</div>
                    </div>
                </div>
            </div>

            <!-- Suggestions Section -->
            <div class="suggestions-section">
                <h2 class="suggestions-title font-dancing">Saran</h2>
                <p class="suggestions-description">
                    Kami sangat menghargai masukan dan saran dari Anda untuk terus meningkatkan kualitas layanan kami. 
                    Silakan berbagi pengalaman atau ide yang dapat membantu kami melayani Anda dengan lebih baik.
                </p>

                <div class="success-message" id="successMessage" role="alert" aria-live="polite">
                    <svg class="inline w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Terima kasih atas saran Anda! Kami akan menghubungi Anda segera via WhatsApp.
                </div>

                <form class="suggestions-form" id="suggestionsForm" novalidate>
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="form-input" 
                               placeholder="Masukkan nama lengkap Anda" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor WhatsApp</label>
                        <input type="tel" id="phone" name="phone" class="form-input" 
                               placeholder="08xxxxxxxxxx" required pattern="[0-9]{10,15}">
                    </div>

                    <div class="form-group">
                        <label for="suggestion" class="form-label">Saran & Masukan</label>
                        <textarea id="suggestion" name="suggestion" class="form-textarea" 
                                  placeholder="Tulis saran, kritik, atau masukan Anda untuk Peace Picture Studio..." 
                                  required></textarea>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Saran
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Component -->
    <x-bottom-nav current-route="info" />
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const suggestionsForm = document.getElementById('suggestionsForm');
    const submitBtn = document.getElementById('submitBtn');
    const successMessage = document.getElementById('successMessage');
    const formInputs = document.querySelectorAll('.form-input, .form-textarea');

    // Form Validation
    formInputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });

    function validateField(e) {
        const field = e.target;
        const fieldName = field.name;
        const value = field.value.trim();
        let isValid = true;

        if (field.hasAttribute('required') && !value) {
            showFieldError(field, 'Field ini wajib diisi');
            isValid = false;
        } else if (fieldName === 'phone' && value && !isValidPhone(value)) {
            showFieldError(field, 'Nomor WhatsApp tidak valid');
            isValid = false;
        } else {
            clearFieldError({ target: field });
        }

        return isValid;
    }

    function showFieldError(field, message) {
        field.style.borderColor = '#ef4444';
        field.setAttribute('aria-invalid', 'true');
        
        // Create or update error message
        let errorElement = field.parentNode.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.style.cssText = `
                color: #ef4444;
                font-size: 0.8rem;
                margin-top: 0.4rem;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
                font-weight: 500;
            `;
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    function clearFieldError(e) {
        const field = e.target;
        field.style.borderColor = '';
        field.setAttribute('aria-invalid', 'false');
        
        const errorElement = field.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // Form Submission
    suggestionsForm.addEventListener('submit', handleFormSubmission);

    async function handleFormSubmission(e) {
        e.preventDefault();

        // Validate all fields
        let hasErrors = false;
        formInputs.forEach(input => {
            if (!validateField({ target: input })) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            showNotification('Mohon perbaiki kesalahan pada form', 'error');
            return;
        }

        // Set loading state
        setSubmitButtonLoading(true);

        try {
            await simulateFormSubmission();
            await sendWhatsAppMessage();
            showSuccessMessage();
            resetForm();
        } catch (error) {
            showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
            console.error('Form submission error:', error);
        } finally {
            setSubmitButtonLoading(false);
        }
    }

    function setSubmitButtonLoading(loading) {
        if (loading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div style="width: 20px; height: 20px; border: 2px solid rgba(255, 255, 255, 0.3); border-top: 2px solid white; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                Mengirim...
            `;
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Kirim Saran
            `;
        }
    }

    async function simulateFormSubmission() {
        return new Promise(resolve => setTimeout(resolve, 1500));
    }

    async function sendWhatsAppMessage() {
        const formData = new FormData(suggestionsForm);
        const message = generateWhatsAppMessage(formData);
        const whatsappUrl = `https://wa.me/6285782086279?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    }

    function generateWhatsAppMessage(formData) {
        return `SARAN & MASUKAN – PEACE PICTURE STUDIO

Nama            : ${formData.get('name')}
No. WhatsApp    : ${formData.get('phone')}

Saran & Masukan:
${formData.get('suggestion')}

--------------------------------------------------
Terima kasih atas saran dan masukan Anda.
Kami akan terus berusaha memberikan pelayanan terbaik.`;
    }

    function showSuccessMessage() {
        successMessage.classList.add('show');
        successMessage.focus();
        setTimeout(() => {
            successMessage.classList.remove('show');
        }, 8000);
    }

    function resetForm() {
        setTimeout(() => {
            suggestionsForm.reset();
        }, 3000);
    }

    // Utility Functions
    function isValidPhone(phone) {
        return /^[0-9]{10,15}$/.test(phone.replace(/\D/g, ''));
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        const bgColor = {
            'error': 'rgba(239, 68, 68, 0.9)',
            'warning': 'rgba(245, 158, 11, 0.9)',
            'info': 'rgba(220, 38, 38, 0.9)'
        };

        notification.style.cssText = `
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: ${bgColor[type] || bgColor.info};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            backdrop-filter: blur(10px);
            z-index: 1000;
            font-weight: 500;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: slideInRight 0.3s ease-out;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-in forwards';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }

    console.log('✨ Info More Page Initialized Successfully!');
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
@endpush
