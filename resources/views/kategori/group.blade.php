@extends('layouts.app')
@section('title', 'Group Photography Session - Peace Picture Studio')

@push('styles')
<style>
    /* ========================================
   DOKUMENTASI KODE HALAMAN GROUP PHOTOGRAPHY
   ========================================
   
   Halaman ini berisi sistem booking group photography dengan fitur:
   - Pemilihan paket (Plain, Grande, Royal)
   - Dynamic background berdasarkan jenis sesi
   - Extra items dengan kalkulasi harga otomatis
   - Modal syarat & ketentuan yang responsif
   - Integrasi WhatsApp untuk booking
   - Form validation lengkap
   - Responsive design untuk semua perangkat
   
   Background System:
   - Family: family/1-6
   - Maternity: maternity/1-7
   - Graduation: wisuda/1-6
   - Friends/Personal: family/1-3
   ======================================== */

    /* ========================================
   1. BASE STYLES & TYPOGRAPHY
   ======================================== */

    @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap');

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

    /* ========================================
   2. LAYOUT & CONTAINER
   ======================================== */

    .main-container {
        min-height: 100vh;
        position: relative;
        padding-bottom: 5rem;
        overflow: visible;
    }

    /* Background lebih cerah */
    .cinematic-bg {
        background-image: url('{{ asset("images/family/3.jpg") }}');
        background-position: center center;
        background-size: cover;
        background-repeat: no-repeat;
        position: absolute;
        inset: 0;
        z-index: 0;
        filter: brightness(0.7) contrast(1.0);
        /* Lebih cerah */
    }

    /* Overlay lebih ringan */
    .cinematic-overlay {
        background: linear-gradient(135deg,
                rgba(0, 0, 0, 0.6) 0%,
                rgba(0, 0, 0, 0.4) 50%,
                rgba(0, 0, 0, 0.6) 100%);
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .content-wrapper {
        position: relative;
        z-index: 20;
        padding: clamp(2rem, 4vw, 3rem) clamp(1rem, 2vw, 1.5rem) clamp(5rem, 6vw, 6rem);
        min-height: 100vh;
        overflow: visible;
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 100vw;
    }

    /* ========================================
   3. HERO SECTION
   ======================================== */

    .hero-section {
        text-align: center;
        margin-bottom: clamp(3rem, 5vw, 4rem);
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 900px;
        padding: clamp(2rem, 4vw, 3rem) clamp(1.5rem, 3vw, 2rem);
        animation: fadeInUp 0.8s ease-out;
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
        font-size: clamp(0.75rem, 1.2vw, 0.875rem);
        font-weight: 300;
        letter-spacing: 0.4em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: clamp(1rem, 2vw, 1.5rem);
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
    }

    .hero-title {
        font-size: clamp(2.8rem, 8vw, 5rem);
        line-height: 0.9;
        color: white;
        font-weight: 400;
        margin-bottom: clamp(1.5rem, 3vw, 2rem);
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.9);
    }

    .hero-description {
        font-size: clamp(0.9rem, 1.5vw, 1.1rem);
        line-height: 1.8;
        color: rgba(255, 255, 255, 0.95);
        font-weight: 300;
        max-width: 45rem;
        margin: 0 auto;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.8);
    }

    /* ========================================
   4. CONTENT GRID & PRICING
   ======================================== */

    .content-grid {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(2.5rem, 4vw, 3.5rem);
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        padding: 0 clamp(1rem, 2vw, 1.5rem);
    }

    .pricing-section {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        margin-bottom: clamp(2rem, 3vw, 2.5rem);
    }

    .pricing-image {
        max-width: 100%;
        width: 100%;
        max-width: clamp(350px, 55vw, 450px);
        height: auto;
        border-radius: clamp(1.2rem, 2.5vw, 1.8rem);
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.7);
        border: 2px solid rgba(255, 255, 255, 0.20);
        transition: transform 0.3s ease;
    }

    .pricing-image:hover {
        transform: translateY(-4px) scale(1.01);
    }

    /* ========================================
   5. PACKAGE CARDS
   ======================================== */

    .package-section {
        display: flex;
        flex-direction: column;
        gap: clamp(2rem, 3vw, 2.5rem);
        width: 100%;
        max-width: 1000px;
        align-items: center;
    }

    @media (min-width: 768px) {
        .package-section {
            flex-direction: row;
            gap: clamp(1.5rem, 3vw, 2rem);
            justify-content: center;
            flex-wrap: wrap;
        }
    }

    @media (min-width: 1024px) {
        .package-section {
            flex-wrap: nowrap;
            gap: clamp(2rem, 4vw, 2.5rem);
        }
    }

    .package-card {
        background: rgba(255, 255, 255, 0.12);
        border: 2px solid rgba(255, 255, 255, 0.25);
        border-radius: clamp(1rem, 2vw, 1.5rem);
        padding: clamp(1.8rem, 3vw, 2.5rem);
        backdrop-filter: blur(30px);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 320px;
        flex: 1;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    }

    .package-card:hover {
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.18);
        transform: translateY(-4px) scale(1.02);
    }

    .package-card.selected {
        border-color: rgba(16, 185, 129, 0.8);
        background: rgba(16, 185, 129, 0.15);
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 35px 100px rgba(16, 185, 129, 0.4);
    }

    .package-card::after {
        content: '✓';
        position: absolute;
        top: 1.2rem;
        right: 1.2rem;
        width: 2.5rem;
        height: 2.5rem;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
        opacity: 0;
        transform: scale(0);
        transition: all 0.4s ease;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        z-index: 15;
    }

    .package-card.selected::after {
        opacity: 1;
        transform: scale(1);
    }

    .package-header {
        text-align: center;
        margin-bottom: clamp(1.2rem, 2.5vw, 1.8rem);
        position: relative;
        z-index: 10;
    }

    .package-title {
        font-size: clamp(1.4rem, 2.5vw, 1.7rem);
        font-weight: 600;
        color: white;
        margin-bottom: 0.6rem;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
    }

    .package-price {
        font-size: clamp(1.2rem, 2vw, 1.4rem);
        font-weight: 700;
        color: rgba(255, 255, 255, 0.95);
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
    }

    .package-features {
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
        z-index: 10;
    }

    .package-features li {
        font-size: clamp(0.8rem, 1.5vw, 0.95rem);
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.9rem;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
    }

    .package-features li::before {
        content: '👥';
        font-size: 0.8rem;
        font-weight: bold;
    }

    /* ========================================
   6. BOOKING FORM
   ======================================== */

    .booking-section {
        width: 100%;
        max-width: 900px;
        background: rgba(255, 255, 255, 0.12);
        border: 2px solid rgba(255, 255, 255, 0.25);
        border-radius: clamp(1.5rem, 2.5vw, 2rem);
        padding: clamp(2.5rem, 4vw, 3.5rem);
        backdrop-filter: blur(35px);
        margin-top: clamp(3rem, 5vw, 4rem);
        position: relative;
        overflow: hidden;
        box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6);
    }

    .booking-title {
        font-size: clamp(1.8rem, 3.5vw, 2.3rem);
        font-weight: 600;
        color: white;
        text-align: center;
        margin-bottom: clamp(2.5rem, 4vw, 3rem);
        position: relative;
        z-index: 10;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.6);
    }

    .package-notice,
    .session-notice {
        background: linear-gradient(135deg,
                rgba(59, 130, 246, 0.18) 0%,
                rgba(59, 130, 246, 0.10) 100%);
        border: 1px solid rgba(59, 130, 246, 0.4);
        border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
        padding: clamp(1rem, 2vw, 1.3rem);
        margin-bottom: clamp(1.5rem, 3vw, 2rem);
        color: #93c5fd;
        font-size: clamp(0.8rem, 1.3vw, 0.9rem);
        text-align: center;
        backdrop-filter: blur(25px);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.15);
        position: relative;
        z-index: 10;
    }

    .package-notice.hidden,
    .session-notice.hidden {
        display: none;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: clamp(1.5rem, 3vw, 2rem);
        margin-bottom: clamp(2rem, 4vw, 2.8rem);
        position: relative;
        z-index: 10;
    }

    @media (min-width: 640px) {
        .form-grid {
            grid-template-columns: 1fr 1fr;
            gap: clamp(1.8rem, 3.5vw, 2.3rem);
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }
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
        margin-bottom: 0.9rem;
        position: relative;
        z-index: 10;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
    }

    .form-input,
    .form-select {
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
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 3rem;
    }

    .form-select option {
        background: rgba(0, 0, 0, 0.95);
        color: white;
        padding: 0.8rem;
        border: none;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.7);
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    }

    .form-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    /* ========================================
   7. BACKGROUND SELECTION - DYNAMIC
   ======================================== */

    .background-section {
        margin-bottom: clamp(2rem, 4vw, 2.8rem);
        transition: all 0.3s ease;
        padding: clamp(1.3rem, 2.5vw, 1.8rem);
        background: rgba(255, 255, 255, 0.12);
        border-radius: clamp(0.9rem, 1.8vw, 1.3rem);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.20);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 10;
    }

    .background-section.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    .background-title {
        font-size: clamp(1rem, 1.8vw, 1.15rem);
        font-weight: 500;
        color: rgba(255, 255, 255, 0.95);
        margin-bottom: clamp(1.3rem, 2.5vw, 1.8rem);
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.9rem;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
    }

    .session-indicator {
        padding: 0.3rem 0.8rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border: 1px solid;
        backdrop-filter: blur(10px);
    }

    .session-indicator.family {
        background: rgba(34, 197, 94, 0.2);
        border-color: rgba(34, 197, 94, 0.5);
        color: #86efac;
    }

    .session-indicator.maternity {
        background: rgba(245, 158, 11, 0.2);
        border-color: rgba(245, 158, 11, 0.5);
        color: #fbbf24;
    }

    .session-indicator.graduation {
        background: rgba(59, 130, 246, 0.2);
        border-color: rgba(59, 130, 246, 0.5);
        color: #93c5fd;
    }

    .session-indicator.general {
        background: rgba(168, 85, 247, 0.2);
        border-color: rgba(168, 85, 247, 0.5);
        color: #c4b5fd;
    }

    .background-counter {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.30);
        border-radius: 1.2rem;
        padding: 0.4rem 1rem;
        font-size: clamp(0.8rem, 1.3vw, 0.9rem);
        font-weight: 600;
        color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(25px);
        transition: all 0.3s ease;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
    }

    .background-counter.warning {
        background: rgba(239, 68, 68, 0.25);
        border-color: rgba(239, 68, 68, 0.5);
        color: #fca5a5;
    }

    .background-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: clamp(1rem, 2vw, 1.3rem);
        margin-bottom: clamp(1.3rem, 2.5vw, 1.8rem);
        justify-items: center;
        max-width: 650px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (min-width: 640px) {
        .background-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .background-option {
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.30);
        border-radius: clamp(0.7rem, 1.4vw, 1rem);
        padding: clamp(0.7rem, 1.4vw, 1rem);
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 180px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .background-option:hover:not(.disabled) {
        border-color: rgba(255, 255, 255, 0.55);
        background: rgba(255, 255, 255, 0.18);
        transform: translateY(-2px) scale(1.01);
    }

    .background-option.selected {
        border-color: rgba(255, 255, 255, 0.85);
        background: rgba(255, 255, 255, 0.30);
        transform: translateY(-2px) scale(1.02);
    }

    .background-option.disabled {
        opacity: 0.3;
        cursor: not-allowed;
        filter: grayscale(1);
    }

    .background-option.selected::after {
        content: '✓';
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 1.8rem;
        height: 1.8rem;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
        opacity: 0;
        transform: scale(0) rotate(-180deg);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        z-index: 2;
    }

    .background-option.selected::after {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }

    @keyframes backgroundPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .background-option.selected {
        animation: backgroundPulse 0.8s ease-out;
    }

    .background-image {
        width: 100%;
        height: clamp(100px, 18vw, 130px);
        object-fit: cover;
        border-radius: calc(clamp(0.7rem, 1.4vw, 1rem) - 4px);
        margin-bottom: clamp(0.6rem, 1.2vw, 0.9rem);
        transition: all 0.3s ease;
        filter: brightness(1.1) contrast(1.1) saturate(1.1);
    }

    .background-option:hover .background-image {
        filter: brightness(1.2) contrast(1.2) saturate(1.1);
    }

    .background-option.selected .background-image {
        filter: brightness(1.25) contrast(1.2) saturate(1.1);
    }

    .background-name {
        font-size: clamp(0.75rem, 1.2vw, 0.85rem);
        color: rgba(255, 255, 255, 0.95);
        font-weight: 500;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
    }

    /* ========================================
   8. EXTRA ITEMS SECTION
   ======================================== */

    .extras-section {
        margin-bottom: clamp(2rem, 4vw, 2.8rem);
        padding: clamp(1.3rem, 2.5vw, 1.8rem);
        background: rgba(255, 255, 255, 0.08);
        border-radius: clamp(0.9rem, 1.8vw, 1.3rem);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        position: relative;
        z-index: 10;
    }

    .extras-title {
        font-size: clamp(1rem, 1.8vw, 1.15rem);
        font-weight: 500;
        color: rgba(255, 255, 255, 0.95);
        margin-bottom: clamp(1.3rem, 2.5vw, 1.8rem);
        text-align: center;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
    }

    .extras-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: clamp(1.2rem, 2.5vw, 1.5rem);
        justify-items: center;
    }

    @media (min-width: 768px) {
        .extras-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (min-width: 1024px) {
        .extras-grid {
            grid-template-columns: 1fr 1fr 1fr;
        }
    }

    .extras-category {
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: clamp(0.7rem, 1.4vw, 1rem);
        padding: clamp(1.2rem, 2.5vw, 1.5rem);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 300px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
    }

    .extras-category:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.30);
        transform: translateY(-2px);
    }

    .category-title {
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        font-weight: 600;
        color: white;
        margin-bottom: clamp(0.9rem, 1.8vw, 1.2rem);
        text-align: center;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
    }

    .extra-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.9rem;
        padding: 0.6rem 0;
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }

    .extra-item:hover {
        background: rgba(255, 255, 255, 0.10);
        padding-left: 0.6rem;
        padding-right: 0.6rem;
    }

    .extra-name {
        font-size: clamp(0.75rem, 1.2vw, 0.85rem);
        color: rgba(255, 255, 255, 0.95);
        font-weight: 500;
        text-shadow:
            0 1px 3px rgba(0, 0, 0, 0.6);
    }

    .extra-price {
        font-size: clamp(0.75rem, 1.2vw, 0.85rem);
        color: rgba(255, 255, 255, 0.8);
        margin-right: 0.9rem;
        font-weight: 500;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
    }

    .extra-checkbox {
        width: 20px;
        height: 20px;
        accent-color: rgba(16, 185, 129, 0.9);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    /* ========================================
   9. NOTES & TEXTAREA
   ======================================== */

    .notes-section {
        margin-bottom: clamp(3rem, 5vw, 4rem);
        padding: clamp(1.5rem, 3vw, 2rem);
        background: rgba(255, 255, 255, 0.08);
        border-radius: clamp(1rem, 2vw, 1.5rem);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        position: relative;
        z-index: 10;
    }

    .form-description {
        font-size: clamp(0.8rem, 1.4vw, 0.9rem);
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: clamp(1.2rem, 2.5vw, 1.5rem);
        line-height: 1.6;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
    }

    .notes-textarea {
        width: 100%;
        min-height: clamp(100px, 18vw, 140px);
        padding: clamp(0.9rem, 2vw, 1.2rem) clamp(1.2rem, 2.5vw, 1.5rem);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
        background: rgba(255, 255, 255, 0.15);
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        color: white;
        resize: vertical;
        font-family: inherit;
        transition: all 0.3s ease;
        backdrop-filter: blur(25px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 10;
    }

    .notes-textarea:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.7);
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    }

    .notes-textarea::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    /* ========================================
   10. PRICING & TOTAL
   ======================================== */

    .total-payment-section {
        margin-bottom: clamp(2.5rem, 4vw, 3rem);
        position: relative;
        z-index: 10;
    }

    .total-payment-card {
        background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.15) 0%,
                rgba(255, 255, 255, 0.08) 100%);
        border: 2px solid rgba(255, 255, 255, 0.25);
        border-radius: clamp(1.2rem, 2.5vw, 1.8rem);
        padding: clamp(1.5rem, 3vw, 2rem);
        backdrop-filter: blur(35px);
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .total-amount {
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-weight: 800;
        color: rgba(34, 197, 94, 0.95);
        text-shadow: 0 2px 12px rgba(34, 197, 94, 0.4);
        transition: all 0.4s ease;
    }

    .total-amount.updating {
        transform: scale(1.05);
        color: rgba(59, 130, 246, 0.95);
        text-shadow: 0 2px 12px rgba(59, 130, 246, 0.4);
    }

    /* ========================================
   11. SUBMIT BUTTON & ACTIONS
   ======================================== */

    .submit-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(1rem, 2vw, 1.5rem);
        position: relative;
        z-index: 10;
    }

    .submit-btn-premium {
        width: 100%;
        max-width: min(420px, calc(100vw - 2rem));
        background: linear-gradient(135deg,
                rgba(34, 197, 94, 0.9) 0%,
                rgba(16, 185, 129, 0.9) 50%,
                rgba(5, 150, 105, 0.9) 100%);
        border: 2px solid rgba(34, 197, 94, 0.6);
        border-radius: clamp(1rem, 2vw, 1.5rem);
        padding: 0;
        color: white;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(30px);
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(34, 197, 94, 0.4);
        font-family: inherit;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .submit-btn-premium::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.2) 0%,
                transparent 50%,
                rgba(255, 255, 255, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .submit-btn-premium:hover::before {
        opacity: 1;
    }

    .submit-btn-premium:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 30px 80px rgba(34, 197, 94, 0.5);
        border-color: rgba(34, 197, 94, 0.8);
    }

    .submit-btn-premium:active {
        transform: translateY(-1px) scale(1.01);
        transition: all 0.1s ease;
    }

    .btn-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: clamp(0.8rem, 2vw, 1.5rem);
        padding: clamp(1.2rem, 2.5vw, 1.6rem) clamp(1.5rem, 3vw, 2rem);
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: clamp(2rem, 3.5vw, 2.5rem);
        height: clamp(2rem, 3.5vw, 2.5rem);
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .submit-btn-premium:hover .btn-icon {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(15deg) scale(1.1);
    }

    .btn-text-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.2rem;
        text-align: left;
    }

    .btn-text {
        font-size: clamp(0.9rem, 1.8vw, 1.1rem);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        line-height: 1.2;
    }

    .btn-subtext {
        font-size: clamp(0.7rem, 1.3vw, 0.8rem);
        font-weight: 400;
        opacity: 0.9;
        text-transform: none;
        letter-spacing: normal;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        line-height: 1.3;
    }

    .btn-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: clamp(1.2rem, 2.5vw, 1.6rem);
        position: absolute;
        inset: 0;
        background: inherit;
        z-index: 3;
        opacity: 0;
        transform: scale(0.9);
        transition: all 0.3s ease;
    }

    .btn-loading.show {
        opacity: 1;
        transform: scale(1);
    }

    .loading-spinner {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .submit-btn-premium:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }

    .submit-note {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: 0.6rem;
        font-size: clamp(0.7rem, 1.2vw, 0.8rem);
        color: rgba(255, 255, 255, 0.6);
        text-align: center;
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.4;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
    }

    .submit-note svg {
        flex-shrink: 0;
        opacity: 0.7;
        margin-top: 0.1rem;
    }

    /* ========================================
   12. SUCCESS & ERROR MESSAGES
   ======================================== */

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
    }

    .error-message {
        display: block;
        min-height: 1.2rem;
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 0.4rem;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
        font-weight: 500;
    }

    /* ========================================
   13. TERMS & CONDITIONS MODAL
   ======================================== */

    .terms-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(0.5rem, 2vw, 1rem);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .terms-modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .terms-modal {
        background: linear-gradient(135deg,
                rgba(30, 30, 30, 0.98) 0%,
                rgba(20, 20, 20, 0.99) 100%);
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-radius: clamp(1rem, 2vw, 1.5rem);
        width: 100%;
        max-width: min(95vw, 1000px);
        max-height: min(95vh, 800px);
        backdrop-filter: blur(30px);
        box-shadow: 0 40px 120px rgba(0, 0, 0, 0.8);
        position: relative;
        overflow: hidden;
        transform: scale(0.9) translateY(20px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .terms-modal-overlay.show .terms-modal {
        transform: scale(1) translateY(0);
    }

    .terms-modal-header {
        padding: clamp(1.5rem, 3vw, 2.5rem) clamp(1.5rem, 3vw, 2.5rem) clamp(1rem, 2vw, 1.5rem);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.02);
    }

    .terms-modal-title {
        font-size: clamp(1.4rem, 3vw, 2rem);
        font-weight: 700;
        color: white;
        text-align: center;
        margin: 0;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .terms-modal-subtitle {
        font-size: clamp(0.9rem, 1.8vw, 1.1rem);
        color: rgba(255, 255, 255, 0.7);
        text-align: center;
        margin: clamp(0.5rem, 1vw, 0.8rem) 0 0;
        font-weight: 400;
        line-height: 1.4;
    }

    .terms-modal-close {
        position: absolute;
        top: clamp(1rem, 2vw, 1.5rem);
        right: clamp(1rem, 2vw, 1.5rem);
        width: clamp(36px, 5vw, 44px);
        height: clamp(36px, 5vw, 44px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(20px);
    }

    .terms-modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        transform: scale(1.1);
    }

    .terms-modal-close svg {
        width: clamp(18px, 3vw, 22px);
        height: clamp(18px, 3vw, 22px);
        color: rgba(255, 255, 255, 0.8);
    }

    .terms-modal-body {
        padding: clamp(1rem, 2.5vw, 2rem) clamp(1.5rem, 3vw, 2.5rem);
        flex: 1;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
    }

    .terms-modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .terms-modal-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .terms-modal-body::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }

    .terms-modal-body::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .terms-content {
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.6;
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        columns: 1;
        column-gap: clamp(2rem, 4vw, 3rem);
    }

    @media (min-width: 768px) {
        .terms-content {
            columns: 2;
        }

        .terms-content h3 {
            break-after: avoid;
            break-inside: avoid;
        }
    }

    .terms-content h3 {
        color: white;
        font-size: clamp(1rem, 1.8vw, 1.2rem);
        font-weight: 600;
        margin: clamp(1.5rem, 2.5vw, 2rem) 0 clamp(0.8rem, 1.5vw, 1rem);
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
        break-inside: avoid;
        position: relative;
    }

    .terms-content h3:first-child {
        margin-top: 0;
    }

    .terms-content h3::before {
        content: '';
        position: absolute;
        left: -1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 1.2em;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 2px;
    }

    .terms-content ul {
        margin: clamp(0.8rem, 1.5vw, 1rem) 0 clamp(1.2rem, 2vw, 1.5rem);
        padding-left: clamp(1.2rem, 2vw, 1.5rem);
        break-inside: avoid;
    }

    .terms-content li {
        margin-bottom: clamp(0.6rem, 1vw, 0.8rem);
        color: rgba(255, 255, 255, 0.85);
        break-inside: avoid;
        position: relative;
    }

    .terms-content li::marker {
        color: rgba(16, 185, 129, 0.8);
    }

    .terms-content strong {
        color: rgba(255, 255, 255, 0.95);
        font-weight: 600;
        background: rgba(16, 185, 129, 0.1);
        padding: 0.1em 0.3em;
        border-radius: 0.2em;
    }

    .terms-content p {
        margin-bottom: clamp(0.8rem, 1.5vw, 1rem);
        break-inside: avoid;
    }

    .terms-modal-footer {
        padding: 0 clamp(1.5rem, 3vw, 2.5rem) clamp(1.5rem, 3vw, 2rem);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
    }

    .terms-agreement {
        display: flex;
        align-items: flex-start;
        gap: clamp(1rem, 2vw, 1.5rem);
        margin-bottom: clamp(1.5rem, 2.5vw, 2rem);
        padding: clamp(1.2rem, 2.5vw, 1.8rem);
        background: rgba(255, 255, 255, 0.05);
        border-radius: clamp(0.8rem, 1.5vw, 1rem);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .terms-checkbox {
        width: 22px;
        height: 22px;
        accent-color: rgba(34, 197, 94, 0.9);
        cursor: pointer;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .terms-agreement-text {
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.6;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
    }

    .terms-modal-actions {
        display: flex;
        gap: clamp(0.8rem, 1.5vw, 1rem);
        justify-content: center;
    }

    .terms-btn {
        padding: clamp(0.8rem, 1.5vw, 1rem) clamp(1.5rem, 3vw, 2rem);
        border-radius: clamp(0.6rem, 1.2vw, 0.8rem);
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        backdrop-filter: blur(20px);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        min-width: 120px;
    }

    .terms-btn-cancel {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        color: rgba(255, 255, 255, 0.9);
    }

    .terms-btn-cancel:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
    }

    .terms-btn-submit {
        background: linear-gradient(135deg,
                rgba(34, 197, 94, 0.9) 0%,
                rgba(16, 185, 129, 0.9) 100%);
        border-color: rgba(34, 197, 94, 0.6);
        color: white;
        opacity: 0.5;
        cursor: not-allowed;
    }

    .terms-btn-submit.enabled {
        opacity: 1;
        cursor: pointer;
    }

    .terms-btn-submit.enabled:hover {
        background: linear-gradient(135deg,
                rgba(34, 197, 94, 1) 0%,
                rgba(16, 185, 129, 1) 100%);
        border-color: rgba(34, 197, 94, 0.8);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
    }

    /* ========================================
   14. RESPONSIVE BREAKPOINTS
   ======================================== */

    @media (max-width: 768px) {
        .terms-modal-actions {
            flex-direction: column;
        }

        .terms-btn {
            width: 100%;
        }

        .package-section {
            flex-direction: column;
            align-items: center;
        }
    }

    @media (max-width: 640px) {
        .btn-content {
            flex-direction: column;
            gap: 0.6rem;
            text-align: center;
            padding: clamp(1.1rem, 2.2vw, 1.4rem) clamp(1.2rem, 2.5vw, 1.6rem);
        }

        .btn-text-content {
            align-items: center;
            text-align: center;
        }

        .background-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .extras-grid {
            grid-template-columns: 1fr;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .content-wrapper {
            padding: 1.5rem 1rem 5rem;
        }

        .submit-btn-premium {
            max-width: calc(100vw - 1rem);
        }

        .btn-content {
            padding: 1rem 1.2rem;
        }
    }

    /* ========================================
   15. ACCESSIBILITY & UTILITIES
   ======================================== */

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .package-card:focus-visible,
    .background-option:focus-visible,
    .submit-btn-premium:focus-visible,
    .form-input:focus-visible,
    .form-select:focus-visible,
    .notes-textarea:focus-visible,
    .terms-modal-close:focus-visible,
    .terms-btn:focus-visible {
        outline: 3px solid rgba(255, 255, 255, 0.8);
        outline-offset: 4px;
        border-color: rgba(255, 255, 255, 0.6);
    }

    /* ========================================
   16. PERFORMANCE OPTIMIZATIONS
   ======================================== */

    * {
        will-change: auto;
    }

    .package-card,
    .background-option,
    .submit-btn-premium,
    .pricing-image,
    .terms-modal {
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    @media print {
        .terms-modal-overlay {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="main-container w-full">
    {{-- Background Image dengan Overlay --}}
    <div class="cinematic-bg">
        <div class="cinematic-overlay"></div>
    </div>

    {{-- Main Content Wrapper --}}
    <div class="content-wrapper">
        {{-- Hero Section --}}
        <div class="hero-section">
            <div class="studio-brand">PEACEPHOTOSTUDIO</div>
            <h1 class="hero-title font-dancing">Group Photography</h1>
            <p class="hero-description">
                Capture Beautiful Moments Together - Create lasting memories with your friends, family, or colleagues through our professional group photography sessions that celebrate your special bonds
            </p>
        </div>

        {{-- Main Content Grid --}}
        <div class="content-grid">
            {{-- Pricing Image Section --}}
            <div class="pricing-section">
                <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Group_price.jpg-lEbtVb2LwzzPvHA7vf8EjrOWgSJ8xQ.jpeg"
                    alt="Group Photography Package Flyer"
                    class="pricing-image"
                    loading="lazy">
            </div>

            {{-- Package Cards Section --}}
            <div class="package-section">
                {{-- Paket Plain --}}
                <div class="package-card"
                    data-package="plain"
                    data-price="300000"
                    data-backgrounds
                    data-price="300000"
                    data-backgrounds="1"
                    tabindex="0"
                    role="button"
                    aria-label="Select Plain Package">
                    <div class="package-header">
                        <h3 class="package-title font-dancing">Plain</h3>
                        <div class="package-price">IDR 300k</div>
                    </div>
                    <ul class="package-features">
                        <li>20 Menit Photoshoot</li>
                        <li>Max 4 Persons</li>
                        <li>1 Background</li>
                        <li>10 Edited Photos</li>
                        <li>1 Photo Printed 12RS</li>
                        <li>Max 1 Wardrobes</li>
                        <li>Google Drive Access (1 Month)</li>
                    </ul>
                </div>

                {{-- Paket Grande --}}
                <div class="package-card"
                    data-package="grande"
                    data-price="500000"
                    data-backgrounds="2"
                    tabindex="0"
                    role="button"
                    aria-label="Select Grande Package">
                    <div class="package-header">
                        <h3 class="package-title font-dancing">Grande</h3>
                        <div class="package-price">IDR 500k</div>
                    </div>
                    <ul class="package-features">
                        <li>35 Menit Photoshoot</li>
                        <li>Max 4 Persons</li>
                        <li>2 Background</li>
                        <li>20 Edited Photos</li>
                        <li>1 Photo Printed 16RS</li>
                        <li>Max 2 Wardrobes</li>
                        <li>Google Drive Access (1 Month)</li>
                    </ul>
                </div>

                {{-- Paket Royal --}}
                <div class="package-card"
                    data-package="royal"
                    data-price="700000"
                    data-backgrounds="4"
                    tabindex="0"
                    role="button"
                    aria-label="Select Royal Package">
                    <div class="package-header">
                        <h3 class="package-title font-dancing">Royal</h3>
                        <div class="package-price">IDR 700k</div>
                    </div>
                    <ul class="package-features">
                        <li>50 Menit Photoshoot</li>
                        <li>Max 4 Persons</li>
                        <li>4 Background</li>
                        <li>30 Edited Photos</li>
                        <li>1 Photo Printed 16RS</li>
                        <li>Max 2 Wardrobes</li>
                        <li>Google Drive Access (1 Month)</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Booking Form Section --}}
        <div class="booking-section">
            <h2 class="booking-title font-dancing">Book Your Group Session</h2>

            {{-- Notice untuk pemilihan paket --}}
            <div class="package-notice" id="packageNotice" role="alert" aria-live="polite">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Silakan pilih paket terlebih dahulu untuk melihat pilihan background yang tersedia</span>
            </div>

            {{-- Notice untuk pemilihan jenis sesi --}}
            <div class="session-notice" id="sessionNotice" role="alert" aria-live="polite">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Silakan pilih jenis sesi untuk melihat background yang sesuai</span>
            </div>

            {{-- Success message --}}
            <div class="success-message" id="successMessage" role="alert" aria-live="polite">
                <svg class="inline w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Booking request sent successfully! We'll contact you soon via WhatsApp.
            </div>

            {{-- Form booking utama --}}
            <form id="bookingForm" novalidate>
                {{-- Basic Information Fields --}}
                <div class="form-grid">
                    <div class="form-group">
                        <label for="contactName" class="form-label">Nama Kontak</label>
                        <input type="text"
                            id="contactName"
                            name="contactName"
                            class="form-input"
                            placeholder="Nama lengkap"
                            required
                            aria-describedby="contactName-error">
                        <div id="contactName-error" class="error-message" role="alert" aria-live="polite"></div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor WhatsApp</label>
                        <input type="tel"
                            id="phone"
                            name="phone"
                            class="form-input"
                            placeholder="08xxxxxxxxxx"
                            required
                            pattern="[0-9]{10,15}"
                            aria-describedby="phone-error">
                        <div id="phone-error" class="error-message" role="alert" aria-live="polite"></div>
                    </div>

                    <div class="form-group">
                        <label for="groupSize" class="form-label">Jumlah Orang</label>
                        <select id="groupSize"
                            name="groupSize"
                            class="form-select"
                            required
                            aria-describedby="groupSize-error">
                            <option value="">Pilih jumlah orang</option>
                            <option value="2">2 Orang</option>
                            <option value="3">3 Orang</option>
                            <option value="4">4 Orang</option>
                        </select>
                        <div id="groupSize-error" class="error-message" role="alert" aria-live="polite"></div>
                    </div>

                    <div class="form-group">
                        <label for="sessionType" class="form-label">Jenis Sesi</label>
                        <select id="sessionType"
                            name="sessionType"
                            class="form-select"
                            required
                            aria-describedby="sessionType-error">

                            <option value="">Pilih jenis sesi</option>
                            <option value="family">Family</option>
                            {{-- <option value="friends">Friends</option> --}}
                            <option value="graduation">Graduation</option>
                            <option value="maternity">Maternity</option>
                            {{-- <option value="personal">Personal</option> --}}
                        </select>
                        <div id="sessionType-error" class="error-message" role="alert" aria-live="polite"></div>
                    </div>

                    <div class="form-group">
                        <label for="date" class="form-label">Tanggal Pemotretan</label>
                        <input type="date"
                            id="date"
                            name="date"
                            class="form-input"
                            required
                            aria-describedby="date-error">
                        <div id="date-error" class="error-message" role="alert" aria-live="polite"></div>
                    </div>

                    <div class="form-group">
                        <label for="time" class="form-label">Waktu Pemotretan</label>
                        <select id="time"
                            name="time"
                            class="form-select"
                            required
                            aria-describedby="time-error">
                            <option value="">Pilih waktu pemotreta</option>
                            <option value="sunrise">Sunrise (05:30 - 07:30)</option>
                            <option value="morning">Morning (08:00 - 10:00)</option>
                            <option value="afternoon">Afternoon (15:00 - 17:00)</option>
                            <option value="golden-hour">Golden Hour (17:30 - 19:00)</option>
                        </select>
                        <div id="time-error" class="error-message" role="alert" aria-live="polite"></div>
                    </div>
                </div>

                {{-- Dynamic Background Selection --}}
                <div class="background-section disabled" id="backgroundSection">
                    <div class="background-title">
                        <span>Pilih Background</span>
                        <div class="session-indicator hidden" id="sessionIndicator"></div>
                        <div class="background-counter" id="backgroundCounter">0/0 dipilih</div>
                    </div>
                    <div class="background-grid" role="group" aria-label="Background selection" id="backgroundGrid">
                        {{-- Background options akan di-generate oleh JavaScript --}}
                    </div>
                </div>

                {{-- Extra Items Section --}}
                <div class="extras-section">
                    <div class="extras-title">Extra Item (Tambahan Opsional)</div>
                    <div class="extras-grid">
                        {{-- Kategori Cetak Foto --}}
                        <div class="extras-category">
                            <div class="category-title">Cetak Foto</div>

                            @foreach($printItems as $item)
                            <div class="extra-item">
                                <div class="extra-info">
                                    <div class="extra-name">{{ $item['name'] }}</div>
                                </div>
                                <span class="extra-price">Rp{{ number_format($item['price']) }}</span>
                                <input type="checkbox"
                                    class="extra-checkbox"
                                    data-name="{{ $item['name'] }}"
                                    data-price="{{ $item['price'] }}"
                                    aria-label="Add {{ $item['name'] }} for Rp{{ number_format($item['price']) }}">
                            </div>
                            @endforeach
                        </div>

                        {{-- Kategori Frame Foto --}}
                        <div class="extras-category">
                            <div class="category-title">Frame Foto</div>

                            @foreach($frameItems as $item)
                            <div class="extra-item">
                                <div class="extra-info">
                                    <div class="extra-name">{{ $item['name'] }}</div>
                                </div>
                                <span class="extra-price">Rp{{ number_format($item['price']) }}</span>
                                <input type="checkbox"
                                    class="extra-checkbox"
                                    data-name="{{ $item['name'] }}"
                                    data-price="{{ $item['price'] }}"
                                    aria-label="Add {{ $item['name'] }} for Rp{{ number_format($item['price']) }}">
                            </div>
                            @endforeach
                        </div>

                        {{-- Kategori Tambahan & Layanan --}}
                        <div class="extras-category">
                            <div class="category-title">Tambahan & Layanan</div>

                            @foreach($serviceItems as $item)
                            <div class="extra-item">
                                <div class="extra-info">
                                    <div class="extra-name">{{ $item['name'] }}</div>
                                </div>
                                <span class="extra-price">Rp{{ number_format($item['price']) }}{{ $item['unit'] ?? '' }}</span>
                                <input type="checkbox"
                                    class="extra-checkbox"
                                    data-name="{{ $item['name'] }}"
                                    data-price="{{ $item['price'] }}"
                                    aria-label="Add {{ $item['name'] }} for Rp{{ number_format($item['price']) }}{{ $item['unit'] ?? '' }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Notes Section --}}
                <div class="notes-section">
                    <div class="form-group">
                        <label for="notes" class="form-label">Keterangan Tambahan</label>
                        <div class="form-description">
                            Tulis permintaan khusus, catatan tambahan, atau detail lainnya yang ingin Anda sampaikan
                        </div>
                        <textarea id="notes"
                            name="notes"
                            class="notes-textarea"
                            placeholder="Contoh: Saya ingin foto dengan tema casual, atau ada request pose tertentu..."
                            aria-describedby="notes-help"></textarea>
                        <div id="notes-help" class="sr-only">Optional field for additional requests or special notes</div>
                    </div>
                </div>

                {{-- Total Payment Section --}}
                <div class="total-payment-section">
                    <div class="total-payment-card">
                        <div class="total-amount" id="totalPrice" aria-live="polite">Total : RP.0-</div>
                    </div>
                </div>

                {{-- Submit Section --}}
                <div class="submit-section">
                    <button type="submit"
                        class="submit-btn-premium"
                        id="submitBtn"
                        aria-describedby="submit-help">
                        <div class="btn-content" id="btnContent">
                            <div class="btn-icon">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                            <div class="btn-text-content">
                                <span class="btn-text">Kirim Pemesanan</span>
                                <span class="btn-subtext">Kami akan menghubungi Anda via WhatsApp</span>
                            </div>
                        </div>
                        <div class="btn-loading" id="btnLoading" style="display: none;">
                            <div class="loading-spinner"></div>
                            <span>Mengirim...</span>
                        </div>
                    </button>
                    <div id="submit-help" class="sr-only">Submit your group photography booking request</div>

                    <div class="submit-note">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Dengan mengirim formulir ini, Anda menyetujui untuk dihubungi oleh tim kami</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bottom Navigation Component --}}
    <x-bottom-nav current-route="group" />
</div>

{{-- Terms and Conditions Modal --}}
<div class="terms-modal-overlay"
    id="termsModal"
    role="dialog"
    aria-labelledby="termsModalTitle"
    aria-modal="true">
    <div class="terms-modal">
        {{-- Header Modal --}}
        <div class="terms-modal-header">
            <h2 class="terms-modal-title" id="termsModalTitle">Syarat & Ketentuan</h2>
            <p class="terms-modal-subtitle">(Aturan & Perjanjian Booking Group Photography)</p>
            <button class="terms-modal-close"
                id="termsModalClose"
                aria-label="Close terms and conditions">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Body Modal dengan Content --}}
        @if($terms->isNotEmpty())
        <div class="terms-modal-body">
            <div class="terms-content">
                @foreach($terms as $term)
                <h3>{{ $term->content }}</h3>
                <ul>
                    @foreach(explode("\n", $term->sub_content) as $item)
                    @if (trim($item) !== '')
                    <li>{{ $item }}</li>
                    @endif
                    @endforeach
                </ul>
                @endforeach
            </div>
        </div>
        @else
        <p>Syarat & ketentuan belum tersedia.</p>
        @endif

        {{-- Footer Modal dengan Agreement --}}
        <div class="terms-modal-footer">
            <div class="terms-agreement">
                <input type="checkbox" id="termsCheckbox" class="terms-checkbox">
                <label for="termsCheckbox" class="terms-agreement-text">
                    Dengan mencentang Syarat & Ketentuan (Aturan & Perjanjian Booking Group Photography) Anda dianggap telah membaca dan menyetujui seluruh ketentuan di atas.
                </label>
            </div>

            <div class="terms-modal-actions">
                <button type="button" class="terms-btn terms-btn-cancel" id="termsCancelBtn">Batal</button>
                <button type="button" class="terms-btn terms-btn-submit" id="termsSubmitBtn">Kirim Pemesanan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /* ========================================
   JAVASCRIPT DOCUMENTATION - GROUP PHOTOGRAPHY
   ========================================
   
   File ini mengatur semua interaksi dan logika untuk halaman group photography:
   
   FITUR UTAMA:
   1. Package Selection System - Pemilihan paket Plain, Grande, Royal
   2. Dynamic Background System - Background berubah sesuai jenis sesi
   3. Extra Items Calculator - Kalkulasi harga tambahan
   4. Form Validation - Validasi lengkap semua field
   5. Terms Modal System - Modal syarat ketentuan
   6. WhatsApp Integration - Kirim data ke WhatsApp
   7. Group Size Management - Pengelolaan jumlah orang
   8. Session Type Selection - Pemilihan jenis sesi
   
   BACKGROUND SYSTEM:
   - Family: family/1-6 (6 backgrounds)
   - Maternity: maternity/1-7 (7 backgrounds)
   - Graduation: wisuda/1-6 (6 backgrounds)
   - Friends/Personal: family/1-3 (3 backgrounds)
   ======================================== */

    document.addEventListener('DOMContentLoaded', function() {
        /* ========================================
           1. CONFIGURATION & STATE MANAGEMENT
           ======================================== */

        // Konfigurasi background berdasarkan jenis sesi
        const sessionBackgrounds = {
            'family': {
                backgrounds: ['family-1', 'family-2', 'family-3', 'family-4', 'family-5', 'family-6'],
                folder: 'family',
                count: 6
            },
            'maternity': {
                backgrounds: ['maternity-1', 'maternity-2', 'maternity-3', 'maternity-4', 'maternity-5', 'maternity-6', 'maternity-7'],
                folder: 'maternity',
                count: 7
            },
            'graduation': {
                backgrounds: ['wisuda-1', 'wisuda-2', 'wisuda-3', 'wisuda-4', 'wisuda-5', 'wisuda-6'],
                folder: 'wisuda',
                count: 6
            },
            'friends': {
                backgrounds: ['family-1', 'family-2', 'family-3'],
                folder: 'family',
                count: 3
            },
            'personal': {
                backgrounds: ['family-1', 'family-2', 'family-3'],
                folder: 'family',
                count: 3
            }
        };

        // Konfigurasi paket
        const packageBackgrounds = {
            'plain': {
                maxBackgrounds: 1
            },
            'grande': {
                maxBackgrounds: 2
            },
            'royal': {
                maxBackgrounds: 4
            }
        };

        // State management untuk aplikasi
        let selectedPackage = null;
        let selectedSession = null;
        let selectedBackgrounds = [];
        let selectedExtras = [];
        let basePrice = 0;
        let isFormSubmitting = false;
        let maxBackgrounds = 0;

        /* ========================================
           2. DOM ELEMENTS SELECTION
           ======================================== */

        const packageCards = document.querySelectorAll('.package-card');
        const sessionSelect = document.getElementById('sessionType');
        const backgroundGrid = document.getElementById('backgroundGrid');
        const extraCheckboxes = document.querySelectorAll('.extra-checkbox');
        const totalPriceElement = document.getElementById('totalPrice');
        const bookingForm = document.getElementById('bookingForm');
        const submitBtn = document.getElementById('submitBtn');
        const successMessage = document.getElementById('successMessage');
        const packageNotice = document.getElementById('packageNotice');
        const sessionNotice = document.getElementById('sessionNotice');
        const backgroundSection = document.getElementById('backgroundSection');
        const backgroundCounter = document.getElementById('backgroundCounter');
        const sessionIndicator = document.getElementById('sessionIndicator');

        // Elemen Terms Modal
        const termsModal = document.getElementById('termsModal');
        const termsModalClose = document.getElementById('termsModalClose');
        const termsCheckbox = document.getElementById('termsCheckbox');
        const termsCancelBtn = document.getElementById('termsCancelBtn');
        const termsSubmitBtn = document.getElementById('termsSubmitBtn');

        /* ========================================
           3. BACKGROUND GENERATION FUNCTIONS
           ======================================== */

        /**
         * Generate background options berdasarkan jenis sesi
         * @param {string} sessionType - Jenis sesi yang dipilih
         */
        function generateBackgroundOptions(sessionType) {
            console.log('Fungsi generateBackgroundOptions() dipanggil dengan sessionType:', sessionType);
            // URL API baru Anda
            const apiUrl = `/api/backgrounds/${sessionType}`;
            console.log('Mencoba fetch URL:', apiUrl);
            // Kosongkan grid sebelum menambahkan opsi baru
            backgroundGrid.innerHTML = '';

            // Lakukan permintaan fetch ke API
            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json(); // Mengubah respons menjadi objek JSON
                })
                .then(backgrounds => {
                    if (backgrounds.length === 0) {
                        backgroundGrid.innerHTML = '<p class="text-center text-gray-500 mt-4">Tidak ada background yang tersedia untuk sesi ini.</p>';
                        return;
                    }

                    // Loop melalui data dari database
                    backgrounds.forEach(background => {
                        const backgroundDiv = document.createElement('div');
                        backgroundDiv.className = 'background-option';
                        backgroundDiv.setAttribute('data-background', background.id);
                        backgroundDiv.setAttribute('data-name', background.name);
                        backgroundDiv.setAttribute('tabindex', '0');
                        backgroundDiv.setAttribute('role', 'button');
                        backgroundDiv.setAttribute('aria-label', `Select ${background.name} background`);

                        // Gunakan path gambar dari database
                        const imagePath = `/storage/${background.image}`;

                        backgroundDiv.innerHTML = `
                    <img src="${imagePath}" 
                         alt="${background.name}" 
                         class="background-image" 
                         loading="lazy">
                    <div class="background-name">${background.name}</div>
                `;

                        // Tambahkan event listeners seperti sebelumnya
                        backgroundDiv.addEventListener('click', handleBackgroundSelection);
                        backgroundDiv.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter' || e.key === ' ') {
                                e.preventDefault();
                                handleBackgroundSelection.call(backgroundDiv);
                            }
                        });

                        backgroundGrid.appendChild(backgroundDiv);
                    });
                })
                .catch(error => {
                    console.error('Error fetching backgrounds:', error);
                    backgroundGrid.innerHTML = '<p class="text-red-500">Terjadi kesalahan saat memuat background.</p>';
                });
        }

        /**
         * Get display name untuk background
         * @param {string} backgroundId - ID background
         * @returns {string} Display name
         */
        function getBackgroundDisplayName(backgroundId) {
            const parts = backgroundId.split('-');
            const type = parts[0];
            const number = parts[1];

            const typeNames = {
                'family': 'Family',
                'maternity': 'Maternity',
                'wisuda': 'Graduation'
            };

            return `${typeNames[type] || type} ${number}`;
        }

        /* ========================================
           4. SESSION TYPE SELECTION
           ======================================== */

        sessionSelect.addEventListener('change', function() {
            const sessionType = this.value;

            if (sessionType) {
                selectedSession = sessionType;

                // Update session indicator
                updateSessionIndicator(sessionType);

                // Generate background 
                console.log('Fungsi yang memanggil generateBackgroundOptions() terpicu.');
                generateBackgroundOptions(sessionType);

                // Reset background selections
                selectedBackgrounds = [];

                // Check if package is selected
                if (selectedPackage) {
                    enableBackgroundSection();
                } else {
                    showSessionNotice();
                }

                updateBackgroundCounter();
            } else {
                selectedSession = null;
                disableBackgroundSection();
                backgroundGrid.innerHTML = '';
                sessionIndicator.classList.add('hidden');
            }
        });

        /**
         * Update session indicator
         * @param {string} sessionType - Jenis sesi
         */
        function updateSessionIndicator(sessionType) {
            const sessionNames = {
                'family': 'Family Session',
                'maternity': 'Maternity Session',
                'graduation': 'Graduation Session',
                // 'friends': 'Friends Session',
                // 'personal': 'Personal Session'
            };

            const sessionClasses = {
                'family': 'family',
                'maternity': 'maternity',
                'graduation': 'graduation',
                // 'friends': 'general',
                // 'personal': 'general'
            };

            sessionIndicator.textContent = sessionNames[sessionType] || sessionType;
            sessionIndicator.className = `session-indicator ${sessionClasses[sessionType] || 'general'}`;
            sessionIndicator.classList.remove('hidden');
        }

        /**
         * Show session notice
         */
        function showSessionNotice() {
            sessionNotice.classList.remove('hidden');
            packageNotice.classList.add('hidden');
        }

        /**
         * Enable background section
         */
        function enableBackgroundSection() {
            backgroundSection.classList.remove('disabled');
            sessionNotice.classList.add('hidden');
            packageNotice.classList.add('hidden');
        }

        /**
         * Disable background section
         */
        function disableBackgroundSection() {
            backgroundSection.classList.add('disabled');
            if (!selectedPackage) {
                packageNotice.classList.remove('hidden');
            } else if (!selectedSession) {
                showSessionNotice();
            }
        }

        /* ========================================
           5. PACKAGE SELECTION LOGIC
           ======================================== */

        packageCards.forEach((card) => {
            card.addEventListener('click', handlePackageSelection);
            card.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    handlePackageSelection.call(card);
                }
            });
        });

        /**
         * Handler untuk pemilihan paket
         */
        function handlePackageSelection() {
            // Hapus seleksi sebelumnya
            packageCards.forEach(c => {
                c.classList.remove('selected');
                c.setAttribute('aria-selected', 'false');
            });

            // Tambah seleksi ke card yang diklik
            this.classList.add('selected');
            this.setAttribute('aria-selected', 'true');

            // Update selected package data
            selectedPackage = {
                id: this.dataset.package,
                name: this.querySelector('.package-title').textContent,
                price: parseInt(this.dataset.price),
                maxBackgrounds: parseInt(this.dataset.backgrounds)
            };

            basePrice = selectedPackage.price;
            maxBackgrounds = packageBackgrounds[selectedPackage.id].maxBackgrounds;

            // Reset background selections
            selectedBackgrounds = [];
            const backgroundOptions = document.querySelectorAll('.background-option');
            backgroundOptions.forEach(option => {
                option.classList.remove('selected');
                option.setAttribute('aria-selected', 'false');
            });

            // Check if session is selected
            if (selectedSession) {
                enableBackgroundSection();
            } else {
                showSessionNotice();
            }

            updateBackgroundCounter();
            updateTotalPrice();
        }

        /* ========================================
           6. BACKGROUND SELECTION SYSTEM
           ======================================== */

        /**
         * Handler untuk pemilihan background
         */
        function handleBackgroundSelection() {
            if (this.classList.contains('disabled')) {
                showNotification('Background ini tidak tersedia', 'error');
                return;
            }

            const isSelected = this.classList.contains('selected');
            const backgroundId = this.dataset.background;
            const backgroundName = this.dataset.name;

            if (isSelected) {
                // Hapus seleksi
                this.classList.remove('selected');
                this.setAttribute('aria-selected', 'false');
                selectedBackgrounds = selectedBackgrounds.filter(bg => bg.id !== backgroundId);
            } else {
                // Cek apakah masih bisa menambah background
                if (selectedBackgrounds.length >= maxBackgrounds) {
                    showNotification(`Maksimal ${maxBackgrounds} background untuk paket ini`, 'error');
                    return;
                }

                // Tambah seleksi
                this.classList.add('selected');
                this.setAttribute('aria-selected', 'true');
                selectedBackgrounds.push({
                    id: backgroundId,
                    name: backgroundName
                });
            }

            updateBackgroundCounter();
        }

        /**
         * Update counter background yang dipilih
         */
        function updateBackgroundCounter() {
            const selectedCount = selectedBackgrounds.length;
            backgroundCounter.textContent = `${selectedCount}/${maxBackgrounds} dipilih`;

            if (selectedCount >= maxBackgrounds) {
                backgroundCounter.classList.add('warning');

                const backgroundOptions = document.querySelectorAll('.background-option');
                backgroundOptions.forEach(option => {
                    if (!option.classList.contains('selected')) {
                        option.style.opacity = '0.3';
                        option.style.pointerEvents = 'none';
                    }
                });
            } else {
                backgroundCounter.classList.remove('warning');

                const backgroundOptions = document.querySelectorAll('.background-option');
                backgroundOptions.forEach(option => {
                    option.style.opacity = '';
                    option.style.pointerEvents = '';
                });
            }
        }

        /* ========================================
           7. EXTRA ITEMS MANAGEMENT
           ======================================== */

        extraCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', handleExtraSelection);
        });

        /**
         * Handler untuk pemilihan extra items
         */
        function handleExtraSelection() {
            const extraItem = {
                name: this.dataset.name,
                price: parseInt(this.dataset.price)
            };

            if (this.checked) {
                selectedExtras.push(extraItem);
            } else {
                selectedExtras = selectedExtras.filter(item => item.name !== extraItem.name);
            }

            updateTotalPrice();
        }

        /* ========================================
           8. PRICE UPDATE SYSTEM
           ======================================== */

        /**
         * Update total harga dengan animasi
         */
        function updateTotalPrice() {
            const extrasTotal = selectedExtras.reduce((sum, item) => sum + item.price, 0);
            const total = basePrice + extrasTotal;

            // Animasi updating
            totalPriceElement.classList.add('updating');
            setTimeout(() => {
                totalPriceElement.classList.remove('updating');
            }, 300);

            totalPriceElement.textContent = `Total : ${formatPrice(total)}`;
            totalPriceElement.setAttribute('aria-label', `Total price: ${formatPrice(total)}`);
        }

        /* ========================================
           9. FORM VALIDATION SYSTEM
           ======================================== */

        const formInputs = document.querySelectorAll('.form-input, .form-select, .notes-textarea');
        formInputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearFieldError);
        });

        /**
         * Validasi individual field
         * @param {Event} e - Event object
         */
        function validateField(e) {
            const field = e.target;
            const fieldName = field.name;
            const value = field.value.trim();
            const errorElement = document.getElementById(`${fieldName}-error`);

            let errorMessage = '';

            if (field.hasAttribute('required') && !value) {
                errorMessage = 'Field ini wajib diisi';
            } else if (fieldName === 'phone' && value && !isValidPhone(value)) {
                errorMessage = 'Nomor WhatsApp tidak valid';
            } else if (fieldName === 'date' && value && new Date(value) < new Date().setHours(0, 0, 0, 0)) {
                errorMessage = 'Tanggal tidak boleh di masa lalu';
            }

            if (errorMessage && errorElement) {
                showFieldError(field, errorMessage);
            } else if (errorElement) {
                clearFieldError({
                    target: field
                });
            }
        }

        /**
         * Tampilkan error pada field
         * @param {HTMLElement} field - Field yang error
         * @param {string} message - Pesan error
         */
        function showFieldError(field, message) {
            const errorElement = document.getElementById(`${field.name}-error`);
            if (errorElement) {
                errorElement.textContent = message;
                field.style.borderColor = '#ef4444';
                field.setAttribute('aria-invalid', 'true');
            }
        }

        /**
         * Hapus error dari field
         * @param {Event} e - Event object
         */
        function clearFieldError(e) {
            const field = e.target;
            const errorElement = document.getElementById(`${field.name}-error`);
            if (errorElement) {
                errorElement.textContent = '';
                field.style.borderColor = '';
                field.setAttribute('aria-invalid', 'false');
            }
        }

        /* ========================================
           10. TERMS MODAL FUNCTIONS
           ======================================== */

        /**
         * Tampilkan terms modal
         */
        function showTermsModal() {
            termsModal.classList.add('show');
            document.body.style.overflow = 'hidden';
            termsCheckbox.checked = false;
            updateTermsSubmitButton();
            termsModalClose.focus();
        }

        /**
         * Sembunyikan terms modal
         */
        function hideTermsModal() {
            termsModal.classList.remove('show');
            document.body.style.overflow = '';
            submitBtn.focus();
        }

        /**
         * Update status tombol submit di terms modal
         */
        function updateTermsSubmitButton() {
            if (termsCheckbox.checked) {
                termsSubmitBtn.classList.add('enabled');
                termsSubmitBtn.disabled = false;
            } else {
                termsSubmitBtn.classList.remove('enabled');
                termsSubmitBtn.disabled = true;
            }
        }

        // Event listeners untuk terms modal
        termsCheckbox.addEventListener('change', updateTermsSubmitButton);
        termsModalClose.addEventListener('click', hideTermsModal);
        termsCancelBtn.addEventListener('click', hideTermsModal);

        // Close modal saat klik overlay
        termsModal.addEventListener('click', (e) => {
            if (e.target === termsModal) {
                hideTermsModal();
            }
        });

        // Close modal dengan ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && termsModal.classList.contains('show')) {
                hideTermsModal();
            }
        });

        // Terms submit button handler
        termsSubmitBtn.addEventListener('click', async () => {
            if (!termsCheckbox.checked) return;

            hideTermsModal();

            isFormSubmitting = true;
            setSubmitButtonLoading(true);

            try {
                // 1. Kumpulkan semua data yang akan dikirim ke backend
                const formData = new FormData(bookingForm);
                const data = {
                    contact_name: formData.get('contactName'),
                    whatsapp_number: formData.get('phone'),
                    booking_date: formData.get('date'),
                    booking_time: formData.get('time'),
                    session_name: formData.get('sessionType'),
                    package_name: selectedPackage.name,
                    selected_backgrounds: selectedBackgrounds, // Data dari state JS
                    selected_extra_items: selectedExtras, // Data dari state JS
                    total_price: basePrice + selectedExtras.reduce((sum, item) => sum + item.price, 0),
                    notes: formData.get('notes'),
                };

                // 2. Kirim data ke API Laravel menggunakan fetch
                const response = await fetch('/booking', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                // 3. Tangani respon dari server
                if (response.ok) {
                    // Jika sukses, tampilkan pesan sukses dan reset form
                    showSuccessMessage();
                    resetForm();

                    // Pilihan: Buka WhatsApp setelah sukses disimpan
                    await sendWhatsAppMessage(); 
                    // Jika ingin tetap kirim WA setelah data tersimpan,
                    // uncomment baris di atas.
                    // Fungsi sendWhatsAppMessage() tidak dihapus, hanya tidak dipanggil.

                } else {
                    // Jika ada error dari server (misal validasi gagal)
                    let errorMessage = result.message || 'Terjadi kesalahan saat menyimpan pesanan.';
                    if (response.status === 422) {
                        // Jika error validasi, tampilkan pesan error lebih detail
                        errorMessage = 'Mohon perbaiki kesalahan pada form.';
                    }
                    showNotification(errorMessage, 'error');
                    console.error('API Error:', result.error || result.errors);
                }
            } catch (error) {
                showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
                console.error('Form submission error:', error);
            } finally {
                isFormSubmitting = false;
                setSubmitButtonLoading(false);
            }
        });

        /* ========================================
           11. FORM SUBMISSION HANDLER
           ======================================== */

        bookingForm.addEventListener('submit', handleFormSubmission);

        /**
         * Handler untuk form submission
         * @param {Event} e - Event object
         */
        async function handleFormSubmission(e) {
            e.preventDefault();

            if (isFormSubmitting) return;

            // Validasi package selection
            if (!selectedPackage) {
                showNotification('Silakan pilih paket terlebih dahulu', 'error');
                packageCards[0].focus();
                return;
            }

            // Validasi session selection
            if (!selectedSession) {
                showNotification('Silakan pilih jenis sesi terlebih dahulu', 'error');
                sessionSelect.focus();
                return;
            }

            // Validasi background selection
            if (selectedBackgrounds.length === 0) {
                showNotification('Silakan pilih minimal 1 background', 'error');
                backgroundSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }

            // Validasi form fields
            let hasErrors = false;
            formInputs.forEach(input => {
                validateField({
                    target: input
                });
                if (input.getAttribute('aria-invalid') === 'true') {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                showNotification('Mohon perbaiki kesalahan pada form', 'error');
                return;
            }

            showTermsModal();
        }

        /**
         * Set loading state untuk submit button
         * @param {boolean} loading - Status loading
         */
        function setSubmitButtonLoading(loading) {
            const btnContent = document.getElementById('btnContent');
            const btnLoading = document.getElementById('btnLoading');

            if (loading) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                submitBtn.setAttribute('aria-busy', 'true');

                btnContent.style.opacity = '0';
                btnContent.style.transform = 'scale(0.9)';

                setTimeout(() => {
                    btnContent.style.display = 'none';
                    btnLoading.style.display = 'flex';
                    btnLoading.classList.add('show');
                }, 150);
            } else {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                submitBtn.setAttribute('aria-busy', 'false');

                btnLoading.classList.remove('show');

                setTimeout(() => {
                    btnLoading.style.display = 'none';
                    btnContent.style.display = 'flex';
                    btnContent.style.opacity = '1';
                    btnContent.style.transform = 'scale(1)';
                }, 150);
            }
        }

        /* ========================================
           12. WHATSAPP INTEGRATION
           ======================================== */

        /**
         * Simulasi form submission
         * @returns {Promise} Promise yang resolve setelah delay
         */
        async function simulateFormSubmission() {
            return new Promise(resolve => setTimeout(resolve, 1500));
        }

        /**
         * Kirim pesan ke WhatsApp
         */
        async function sendWhatsAppMessage() {
            const formData = new FormData(bookingForm);
            const message = generateWhatsAppMessage(formData);
            const whatsappUrl = `https://wa.me/6281994662990?text=${encodeURIComponent(message)}`;

            window.open(whatsappUrl, '_blank');
        }

        /**
         * Generate pesan WhatsApp dari form data
         * @param {FormData} formData - Data dari form
         * @returns {string} Formatted message untuk WhatsApp
         */
        function generateWhatsAppMessage(formData) {
            const timeNames = {
                'sunrise': 'Sunrise (05:30 - 07:30)',
                'morning': 'Morning (08:00 - 10:00)',
                'afternoon': 'Afternoon (15:00 - 17:00)',
                'golden-hour': 'Golden Hour (17:30 - 19:00)'
            };

            const sessionTypeNames = {
                'family': 'Family',
                // 'friends': 'Friends',
                'graduation': 'Graduation',
                'maternity': 'Maternity',
                // 'personal': 'Personal'
            };

            const backgroundNames = selectedBackgrounds.map(bg => bg.name).join(', ') || 'Belum dipilih';
            const extrasText = selectedExtras.length > 0 ?
                selectedExtras.map(item => `• ${item.name} - ${formatPrice(item.price)}`).join('\n') :
                'Tidak ada';

            const extrasTotal = selectedExtras.reduce((sum, item) => sum + item.price, 0);
            const totalPrice = basePrice + extrasTotal;

            return `👥 *BOOKING GROUP PHOTOGRAPHY SESSION* 👥

👤 *Nama Kontak:* ${formData.get('contactName')}
📱 *WhatsApp:* ${formData.get('phone')}
📦 *Paket:* ${selectedPackage.name} - ${formatPrice(basePrice)}
👥 *Jumlah Orang:* ${formData.get('groupSize')}
📸 *Jenis Sesi:* ${sessionTypeNames[formData.get('sessionType')]}
🎨 *Background:* ${backgroundNames} (${selectedBackgrounds.length}/${maxBackgrounds})
📅 *Tanggal:* ${formData.get('date')}
⏰ *Waktu:* ${timeNames[formData.get('time')]}

✨ *Extra Items:*
${extrasText}

💌 *Catatan Tambahan:*
${formData.get('notes') || 'Tidak ada'}

💰 *Total Harga:* ${formatPrice(totalPrice)}

✅ *Saya telah membaca dan menyetujui Syarat & Ketentuan Peace Picture Studio*

---
Terima kasih telah memilih Peace Picture Studio! Kami akan segera menghubungi Anda untuk konfirmasi detail lebih lanjut. 📸✨`;
        }

        /* ========================================
           13. SUCCESS & RESET FUNCTIONS
           ======================================== */

        /**
         * Tampilkan success message
         */
        function showSuccessMessage() {
            successMessage.classList.add('show');
            successMessage.focus();

            setTimeout(() => {
                successMessage.classList.remove('show');
            }, 8000);
        }

        /**
         * Reset form ke kondisi awal
         */
        function resetForm() {
            setTimeout(() => {
                bookingForm.reset();

                // Reset package cards
                packageCards.forEach(c => {
                    c.classList.remove('selected');
                    c.setAttribute('aria-selected', 'false');
                });

                // Reset extra checkboxes
                extraCheckboxes.forEach(c => c.checked = false);

                // Reset state variables
                selectedPackage = null;
                selectedSession = null;
                selectedBackgrounds = [];
                selectedExtras = [];
                basePrice = 0;
                maxBackgrounds = 0;

                // Reset background section
                disableBackgroundSection();
                backgroundGrid.innerHTML = '';
                backgroundCounter.textContent = '0/0 dipilih';
                backgroundCounter.classList.remove('warning');
                sessionIndicator.classList.add('hidden');

                updateTotalPrice();
            }, 3000);
        }

        /* ========================================
           14. UTILITY FUNCTIONS
           ======================================== */

        /**
         * Format harga ke format Rupiah
         * @param {number} price - Harga dalam angka
         * @returns {string} Formatted price string
         */
        function formatPrice(price) {
            return `RP.${price.toLocaleString('id-ID')}-`;
        }

        /**
         * Validasi nomor telepon
         * @param {string} phone - Nomor telepon
         * @returns {boolean} Valid atau tidak
         */
        function isValidPhone(phone) {
            return /^[0-9]{10,15}$/.test(phone.replace(/\D/g, ''));
        }

        /**
         * Tampilkan notification toast
         * @param {string} message - Pesan notification
         * @param {string} type - Tipe notification (error, warning, info)
         */
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;

            const bgColor = {
                'error': 'rgba(239, 68, 68, 0.9)',
                'warning': 'rgba(245, 158, 11, 0.9)',
                'info': 'rgba(59, 130, 246, 0.9)'
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

        /* ========================================
           15. INITIALIZATION
           ======================================== */

        // Set minimum date ke hari ini
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.setAttribute('min', today);
        }

        // Initialize total price
        updateTotalPrice();

        // Log initialization success
        console.log('✨ Dynamic Group Photography System Initialized Successfully!');
    });

    /* ========================================
       16. CSS ANIMATIONS FOR NOTIFICATIONS
       ======================================== */

    // Tambah CSS animations untuk notifications
    const style = document.createElement('style');
    style.textContent = `
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

    /* ========================================
       END OF JAVASCRIPT - GROUP PHOTOGRAPHY
       
       Total Functions: 30+
       Total Lines: ~1000+
       Features: Complete dynamic group photography booking system
       Browser Support: Modern browsers (ES6+)
       Performance: Optimized with efficient DOM manipulation
       Accessibility: Full keyboard & screen reader support
       Mobile: Touch-friendly interactions
       Dynamic: Background changes based on session type
       ======================================== */
</script>
@endpush