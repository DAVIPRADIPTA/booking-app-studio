@extends('layouts.app')
@section('title', 'Baby Smash Cake Session - Peace Picture Studio')

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

        /* Clean Professional Background */
        .cinematic-bg {
            background-image: url('{{ asset("images/bsc/1.jpg") }}');
            background-position: center center;
            background-size: cover;
            background-repeat: no-repeat;
            position: absolute;
            inset: 0;
            z-index: 0;
            filter: brightness(0.5) contrast(1.1);
        }

        /* Alternative backgrounds for variety */
        .cinematic-bg.bg-2 {
            background-image: url('{{ asset("images/bsc/2.jpg") }}');
        }

        .cinematic-bg.bg-3 {
            background-image: url('{{ asset("images/bsc/3.jpg") }}');
        }

        /* Clean Dark Overlay */
        .cinematic-overlay {
            background: linear-gradient(135deg,
                    rgba(0, 0, 0, 0.8) 0%,
                    rgba(0, 0, 0, 0.6) 50%,
                    rgba(0, 0, 0, 0.8) 100%);
            position: absolute;
            inset: 0;
            z-index: 1;
        }


        /* Content Wrapper - Clean */
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

        /* Hero Section - Elegant */
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

        /* Clean Typography */
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

        /* Content Grid */
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

        /* Pricing Image - Clean */
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
            max-width: clamp(400px, 65vw, 550px);
            height: auto;
            border-radius: clamp(1.2rem, 2.5vw, 1.8rem);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.7);
            border: 2px solid rgba(255, 255, 255, 0.20);
            transition: transform 0.3s ease;
        }

        .pricing-image:hover {
            transform: translateY(-4px) scale(1.01);
        }

        /* Package Info Card - Single Package Display */
        .package-info-section {
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 500px;
            margin-bottom: clamp(2rem, 3vw, 2.5rem);
        }

        .package-info-card {
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-radius: clamp(1rem, 2vw, 1.5rem);
            padding: clamp(1.8rem, 3vw, 2.5rem);
            backdrop-filter: blur(30px);
            position: relative;
            overflow: hidden;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .package-header {
            margin-bottom: clamp(1.2rem, 2.5vw, 1.8rem);
        }

        .package-title {
            font-size: clamp(1.6rem, 3vw, 2rem);
            font-weight: 600;
            color: white;
            margin-bottom: 0.6rem;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .package-price {
            font-size: clamp(1.4rem, 2.5vw, 1.8rem);
            font-weight: 700;
            color: rgba(255, 182, 193, 0.95);
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .package-features {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
        }

        .package-features li {
            font-size: clamp(0.85rem, 1.5vw, 1rem);
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
        }

        .package-features li::before {
            content: '🎂';
            font-size: 0.9rem;
        }

        .package-note {
            background: linear-gradient(135deg,
                    rgba(255, 182, 193, 0.18) 0%,
                    rgba(255, 182, 193, 0.10) 100%);
            border: 1px solid rgba(255, 182, 193, 0.4);
            border-radius: clamp(0.6rem, 1.2vw, 0.9rem);
            padding: clamp(0.8rem, 1.5vw, 1rem);
            margin-top: clamp(1rem, 2vw, 1.3rem);
            color: #ffb6c1;
            font-size: clamp(0.75rem, 1.2vw, 0.85rem);
            text-align: center;
            backdrop-filter: blur(25px);
            font-weight: 500;
        }

        /* Booking Form - Clean */
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

        /* Form Elements */
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

        /* Clean Input Styling */
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

        .form-input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            /* jadikan ikon putih */
            opacity: 0.9;
            cursor: pointer;
            transition: filter 0.3s ease;
        }

        /* Hilangkan spin & clear button (khusus Chrome) */
        .form-input[type="date"]::-webkit-inner-spin-button,
        .form-input[type="date"]::-webkit-clear-button {
            display: none;
        }

        /* Firefox fallback - hilangkan tampilan default */
        @supports (-moz - appearance) {
            .form-input[type="date"] {
                -moz-appearance: none;
                appearance: none;
            }
        }


        /* Extra Items - Clean */
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
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
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
            accent-color: rgba(255, 182, 193, 0.9);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        /* Notes Section */
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

        /* Notes Section - Improved Spacing */
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

        /* TOTAL PEMBAYARAN */
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
            color: rgba(255, 182, 193, 0.95);
            text-shadow: 0 2px 12px rgba(255, 182, 193, 0.4);
            transition: all 0.4s ease;
        }

        .total-amount.updating {
            transform: scale(1.05);
            color: rgba(255, 192, 203, 0.95);
            text-shadow: 0 2px 12px rgba(255, 192, 203, 0.4);
        }

        /* SUBMIT SECTION */
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
                    rgba(255, 182, 193, 0.9) 0%,
                    rgba(255, 192, 203, 0.9) 50%,
                    rgba(255, 160, 180, 0.9) 100%);
            border: 2px solid rgba(255, 182, 193, 0.6);
            border-radius: clamp(1rem, 2vw, 1.5rem);
            padding: 0;
            color: white;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(30px);
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(255, 182, 193, 0.4);
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
            box-shadow: 0 30px 80px rgba(255, 182, 193, 0.5);
            border-color: rgba(255, 182, 193, 0.8);
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

        /* Success Message */
        .success-message {
            background: linear-gradient(135deg,
                    rgba(255, 182, 193, 0.30),
                    rgba(255, 182, 193, 0.18));
            border: 1px solid rgba(255, 182, 193, 0.6);
            border-radius: clamp(0.7rem, 1.4vw, 1rem);
            padding: clamp(1.2rem, 2.5vw, 1.8rem);
            margin-bottom: 1.8rem;
            color: #ffb6c1;
            font-size: clamp(0.85rem, 1.5vw, 0.95rem);
            text-align: center;
            display: none;
            backdrop-filter: blur(30px);
            box-shadow: 0 20px 50px rgba(255, 182, 193, 0.3);
            position: relative;
            z-index: 10;
        }

        .success-message.show {
            display: block;
        }

        /* Error Messages */
        .error-message {
            display: block;
            min-height: 1.2rem;
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.4rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
            font-weight: 500;
        }

        /* Screen Reader Only */
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

        /* TERMS AND CONDITIONS MODAL */
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
            background: linear-gradient(135deg, #ffb6c1, #ffc0cb);
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
            color: rgba(255, 182, 193, 0.8);
        }

        .terms-content strong {
            color: rgba(255, 255, 255, 0.95);
            font-weight: 600;
            background: rgba(255, 182, 193, 0.1);
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
            accent-color: rgba(255, 182, 193, 0.9);
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
                    rgba(255, 182, 193, 0.9) 0%,
                    rgba(255, 192, 203, 0.9) 100%);
            border-color: rgba(255, 182, 193, 0.6);
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
                    rgba(255, 182, 193, 1) 0%,
                    rgba(255, 192, 203, 1) 100%);
            border-color: rgba(255, 182, 193, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 182, 193, 0.4);
        }

        /* Responsive Breakpoints */
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

            .terms-modal-actions {
                flex-direction: column;
            }

            .terms-btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .content-wrapper {
                padding: 1.5rem 1rem 5rem;
            }

            .extras-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Accessibility */
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

        /* Performance Optimizations */
        * {
            will-change: auto;
        }

        .pricing-image,
        .submit-btn-premium,
        .terms-modal {
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

        /* ========================
           NOTIFIKASI KETERSEDIAAN WAKTU - PREMIUM (TEKS PUTIH)
           ======================== */
        .availability-notice {
            @apply relative overflow-hidden rounded-xl border px-5 py-4 text-sm font-medium shadow-sm transition-all duration-300;
            @apply backdrop-blur-lg;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.06));
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
            transform: translateY(10px);
            opacity: 0;
            visibility: hidden;
        }

        .availability-notice.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
            animation: fadeInUp 0.4s ease-out;
        }

        .availability-notice::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.02));
            pointer-events: none;
            z-index: 0;
        }

        .availability-notice span {
            position: relative;
            z-index: 1;
            display: block;
            text-align: center;
            line-height: 1.5;
            color: white;
            /* ✅ Teks PUTIH */
            font-weight: 500;
            text-shadow: 0 1px 6px rgba(0, 0, 0, 0.8);
            /* Bayangan agar lebih tajam */
        }

        /* Status: Tersedia */
        .availability-notice.available {
            @apply border-blue-300/50;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(37, 99, 235, 0.15));
        }

        /* Status: Terbatas (Limited) */
        .availability-notice.limited {
            @apply border-yellow-300/50;
            background: linear-gradient(135deg, rgba(234, 179, 8, 0.25), rgba(202, 138, 4, 0.15));
        }

        /* Status: Penuh (Full) */
        .availability-notice.full {
            @apply border-red-300/50;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(185, 28, 28, 0.15));
        }

        /* Animasi Masuk */
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

        /* Responsif */
        @media (max-width: 640px) {
            .availability-notice {
                @apply px-4 py-3 text-xs;
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
                <div class="studio-brand">PEACEPHOTOSTUDIO</div>
                <h1 class="hero-title font-dancing">Baby Smash Cake</h1>
                <p class="hero-description">
                    Capture your little one's precious milestone with our adorable Baby Smash Cake photoshoot session.
                    Create magical memories of their first birthday celebration that will last a lifetime.
                </p>
            </div>

            <!-- Main Content Grid -->
            <div class="content-grid">
                <!-- Pricing Image Section -->
                <div class="pricing-section">
                    <img src="{{ asset('images/Bsc_price.jpg') }}" alt="Baby Smash Cake Package Flyer" class="pricing-image"
                        loading="lazy">
                </div>

                <!-- Package Info Card -->
                <div class="package-info-section">
                    <div class="package-info-card">
                        <div class="package-header">
                            <h3 class="package-title font-dancing">Baby Smash Cake</h3>
                            <div class="package-price">IDR 550k</div>
                        </div>
                        <ul class="package-features">
                            <li>30 Minutes Photoshoot</li>
                            <li>1 Concept Background</li>
                            <li>10 Edited Photos</li>
                            <li>1 Printed + Frame 12Rs</li>
                            <li>Max 2 Wardrobes</li>
                            <li>Google Drive Access (1 Month)</li>
                        </ul>
                        <div class="package-note">
                            <strong>Note:</strong> Cake Not From Studio
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Form Section -->
            <div class="booking-section">
                <h2 class="booking-title font-dancing">Book Your Baby's Special Session</h2>

                <div class="success-message" id="successMessage" role="alert" aria-live="polite">
                    <svg class="inline w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Booking request sent successfully! We'll contact you soon via WhatsApp.
                </div>

                <form id="bookingForm" novalidate>
                    <!-- Basic Information -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="contactName" class="form-label">Nama Kontak</label>
                            <input type="text" id="contactName" name="contactName" class="form-input"
                                placeholder="Nama lengkap" required aria-describedby="contactName-error">
                            <div id="contactName-error" class="error-message" role="alert" aria-live="polite"></div>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Nomor WhatsApp</label>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="+62xxxxxxxxxx"
                                required pattern="[0-9]{10,15}" aria-describedby="phone-error">
                            <div id="phone-error" class="error-message" role="alert" aria-live="polite"></div>
                        </div>

                        <div class="form-group">
                            <label for="babyName" class="form-label">Nama Bayi</label>
                            <input type="text" id="babyName" name="babyName" class="form-input" placeholder="Nama si kecil"
                                required aria-describedby="babyName-error">
                            <div id="babyName-error" class="error-message" role="alert" aria-live="polite"></div>
                        </div>

                        <div class="form-group">
                            <label for="babyAge" class="form-label">Usia Bayi</label>
                            <select id="babyAge" name="babyAge" class="form-select" required>
                                <option value="">Pilih usia anak</option>
                                @for ($age = 1; $age <= 6; $age++)
                                    <option value="{{ $age }}-year{{ $age > 1 ? 's' : '' }}">{{ $age }} Tahun</option>
                                @endfor
                            </select>
                            <div id="babyAge-error" class="error-message" role="alert" aria-live="polite"></div>
                        </div>

                        <!-- Input Tanggal -->
                        <div class="form-group">
                            <label for="date" class="form-label">Tanggal Pemotretan</label>
                            <input type="date" id="date" name="date" required class="form-input"
                                min="{{ now()->format('Y-m-d') }}" onchange="fetchAvailableTimes()"
                                aria-describedby="date-error">
                            <div id="date-error" class="error-message" role="alert" aria-live="polite"></div>
                        </div>

                        <!-- Input Waktu -->
                        <div class="form-group">
                            <label for="time" class="form-label">Waktu Pemotretan</label>
                            <select id="time" name="time" required class="form-select" disabled
                                aria-describedby="time-error">
                                <option value="">Pilih tanggal dulu...</option>
                            </select>
                            <div id="time-error" class="error-message" role="alert" aria-live="polite"></div>
                        </div>

                        <!-- Info Ketersediaan Waktu -->
                        <!-- Info Ketersediaan Waktu -->
                        <div class="form-group full-width">
                            <div id="time-availability-info" class="availability-notice hidden">
                                <span id="availability-message">Informasi ketersediaan akan muncul di sini.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Extra Items -->
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
                                        <input type="checkbox" class="extra-checkbox" data-name="{{ $item['name'] }}"
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
                                        <input type="checkbox" class="extra-checkbox" data-name="{{ $item['name'] }}"
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
                                        <span
                                            class="extra-price">Rp{{ number_format($item['price']) }}{{ $item['unit'] ?? '' }}</span>
                                        <input type="checkbox" class="extra-checkbox" data-name="{{ $item['name'] }}"
                                            data-price="{{ $item['price'] }}"
                                            aria-label="Add {{ $item['name'] }} for Rp{{ number_format($item['price']) }}{{ $item['unit'] ?? '' }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="notes-section">
                        <div class="form-group">
                            <label for="notes" class="form-label">Keterangan Tambahan</label>
                            <div class="form-description">
                                Tulis permintaan khusus, tema yang diinginkan, atau detail lainnya tentang sesi foto bayi
                                Anda
                            </div>
                            <textarea id="notes" name="notes" class="notes-textarea"
                                placeholder="Contoh: Saya ingin tema princess pink, atau ada request pose tertentu untuk bayi..."
                                aria-describedby="notes-help"></textarea>
                            <div id="notes-help" class="sr-only">Optional field for additional requests or special notes
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL PEMBAYARAN -->
                    <div class="total-payment-section">
                        <div class="total-payment-card">
                            <div class="total-amount" id="totalPrice" aria-live="polite">Total : RP.550.000-</div>
                        </div>
                    </div>

                    <!-- SUBMIT SECTION -->
                    <div class="submit-section">
                        <button type="submit" class="submit-btn-premium" id="submitBtn" aria-describedby="submit-help">
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
                        <div id="submit-help" class="sr-only">Submit your baby smash cake booking request</div>
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

        <!-- Bottom Navigation Component -->
        <x-bottom-nav current-route="baby" />
    </div>

    <!-- TERMS AND CONDITIONS MODAL -->
    <div class="terms-modal-overlay" id="termsModal" role="dialog" aria-labelledby="termsModalTitle" aria-modal="true">
        <div class="terms-modal">
            <div class="terms-modal-header">
                <h2 class="terms-modal-title" id="termsModalTitle">Syarat & Ketentuan</h2>
                <p class="terms-modal-subtitle">(Aturan & Perjanjian Booking Baby Smash Cake)</p>
                <button class="terms-modal-close" id="termsModalClose" aria-label="Close terms and conditions">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
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
            <div class="terms-modal-footer">
                <div class="terms-agreement">
                    <input type="checkbox" id="termsCheckbox" class="terms-checkbox">
                    <label for="termsCheckbox" class="terms-agreement-text">
                        Dengan mencentang Syarat & Ketentuan (Aturan & Perjanjian Booking Baby Smash Cake) Anda dianggap
                        telah membaca dan menyetujui seluruh ketentuan di atas.
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
        // ========================
        // STATE MANAGEMENT (GLOBAL)
        // ========================
        // Karena hanya satu sesi, kita hardcode
        const sessionData = {
            session_name: 'baby-smash-cake',
            package_name: 'Baby Smash Cake',
            base_price: 550000
        };

        let selectedExtras = [];
        let isFormSubmitting = false;

        // DOM Elements
        const bookingForm = document.getElementById('bookingForm');
        const termsModal = document.getElementById('termsModal');
        const termsCheckbox = document.getElementById('termsCheckbox');
        const termsSubmitBtn = document.getElementById('termsSubmitBtn');
        const termsCancelBtn = document.getElementById('termsCancelBtn');
        const termsModalClose = document.getElementById('termsModalClose');
        const submitBtn = document.getElementById('submitBtn');
        const successMessage = document.getElementById('successMessage');
        const extraCheckboxes = document.querySelectorAll('.extra-checkbox');
        const formInputs = document.querySelectorAll('.form-input, .form-select, .notes-textarea');
        const totalPriceElement = document.getElementById('totalPrice');

        // ========================
        // HELPER FUNCTIONS
        // ========================
        function formatPrice(price) {
            return 'Rp' + price.toLocaleString('id-ID');
        }

        function formatPhoneNumber(number) {
            if (!number) return '';
            const cleaned = number.replace(/\D/g, '');
            if (cleaned.startsWith('0')) {
                return '62' + cleaned.substring(1);
            } else if (cleaned.startsWith('62')) {
                return cleaned;
            } else {
                return '62' + cleaned;
            }
        }

        function isValidPhone(phone) {
            const cleaned = phone.replace(/\D/g, '');
            return /^(62|0)8[1-9][0-9]{6,11}$/.test(cleaned);
        }

        function showNotification(message, type = 'error') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                    position: fixed; top: 2rem; right: 2rem; 
                    background: ${type === 'error' ? 'rgba(239, 68, 68, 0.9)' : 'rgba(255, 182, 193, 0.9)'}; 
                    color: white; padding: 1rem 1.5rem; border-radius: 0.75rem;
                    backdrop-filter: blur(10px); z-index: 1000; font-weight: 500;
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

        function showSuccessMessage() {
            successMessage.classList.add('show');
            successMessage.focus();
            setTimeout(() => {
                successMessage.classList.remove('show');
            }, 8000);
        }

        function resetForm() {
            setTimeout(() => {
                bookingForm.reset();
                extraCheckboxes.forEach(c => c.checked = false);
                selectedExtras = [];
                updateTotalPrice();
            }, 3000);
        }

        // ========================
        // VALIDATION
        // ========================
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
            } else if (fieldName === 'date' && value) {
                const selectedDate = new Date(value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate < today) {
                    errorMessage = 'Tanggal tidak boleh di masa lalu';
                }
            }

            if (errorMessage && errorElement) {
                errorElement.textContent = errorMessage;
                field.style.borderColor = '#ef4444';
                field.setAttribute('aria-invalid', 'true');
            } else if (errorElement) {
                errorElement.textContent = '';
                field.style.borderColor = '';
                field.setAttribute('aria-invalid', 'false');
            }
        }

        function clearFieldError(e) {
            const field = e.target;
            const errorElement = document.getElementById(`${field.name}-error`);
            if (errorElement) {
                errorElement.textContent = '';
                field.style.borderColor = '';
                field.setAttribute('aria-invalid', 'false');
            }
        }

        // ========================
        // EXTRA ITEMS SELECTION
        // ========================
        extraCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
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
            });
        });

        // ========================
        // UPDATE TOTAL PRICE
        // ========================
        function updateTotalPrice() {
            const extrasTotal = selectedExtras.reduce((sum, item) => sum + item.price, 0);
            const totalPrice = sessionData.base_price + extrasTotal;
            if (totalPriceElement) {
                totalPriceElement.textContent = `Total: ${formatPrice(totalPrice)}`;
            }
        }

        // ========================
        // REAL-TIME TIME AVAILABILITY
        // ========================
        async function fetchAvailableTimes() {
            const dateInput = document.getElementById('date');
            const timeSelect = document.getElementById('time');
            const infoBox = document.getElementById('time-availability-info');
            const messageSpan = document.getElementById('availability-message');

            const selectedDate = dateInput.value;

            if (!selectedDate) return;

            // Reset
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">Memuat...</option>';
            infoBox.classList.add('hidden');
            infoBox.classList.remove('show', 'available', 'limited', 'full');

            try {
                const response = await fetch(`/api/available-times?booking_date=${selectedDate}`);
                const data = await response.json();

                // Kosongkan dropdown
                timeSelect.innerHTML = '';

                if (data.status === 'full') {
                    // Tidak ada slot
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Hari ini full booked';
                    option.disabled = true;
                    timeSelect.appendChild(option);
                    timeSelect.disabled = true;

                    messageSpan.textContent = '❌ Maaf, semua slot sudah penuh di tanggal ini. Silakan pilih tanggal lain.';
                    infoBox.classList.add('full');
                } else {
                    // Tampilkan waktu yang tersedia
                    data.available_times.forEach(time => {
                        const option = document.createElement('option');
                        option.value = time;
                        option.textContent = `${time} WIB`;
                        timeSelect.appendChild(option);
                    });
                    timeSelect.disabled = false;

                    if (data.status === 'limited') {
                        messageSpan.textContent = `⚠️ Hanya tersisa ${data.available_times.length} slot. Segera booking!`;
                        infoBox.classList.add('limited');
                    } else {
                        messageSpan.textContent = `✅ Ada ${data.available_times.length} slot yang tersedia.`;
                        infoBox.classList.add('available');
                    }
                }

                // Tampilkan notifikasi dengan animasi
                infoBox.classList.remove('hidden');
                setTimeout(() => infoBox.classList.add('show'), 50);

            } catch (error) {
                console.error('Gagal memuat ketersediaan waktu:', error);
                timeSelect.innerHTML = '<option value="">Gagal muat</option>';
                timeSelect.disabled = true;
            }
        }

        // ========================
        // FORM SUBMISSION
        // ========================
        document.addEventListener('DOMContentLoaded', function () {
            // Set today as min date
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('date');
            if (dateInput) {
                dateInput.min = today;
            }

            // Validasi input
            formInputs.forEach(input => {
                input.addEventListener('blur', validateField);
                input.addEventListener('input', clearFieldError);
            });

            // Cek ketersediaan waktu saat tanggal berubah
            if (dateInput) {
                dateInput.addEventListener('change', fetchAvailableTimes);
                if (dateInput.value) fetchAvailableTimes();
            }

            // Update total price awal
            updateTotalPrice();
        });

        // Terms Modal
        function showTermsModal() {
            termsModal.classList.add('show');
            document.body.style.overflow = 'hidden';
            termsModal.focus();
        }

        function hideTermsModal() {
            termsModal.classList.remove('show');
            document.body.style.overflow = '';
            submitBtn.focus();
        }

        function updateTermsSubmitButton() {
            if (termsCheckbox.checked) {
                termsSubmitBtn.classList.add('enabled');
                termsSubmitBtn.disabled = false;
            } else {
                termsSubmitBtn.classList.remove('enabled');
                termsSubmitBtn.disabled = true;
            }
        }

        // Event Listeners
        termsCheckbox.addEventListener('change', updateTermsSubmitButton);
        termsModalClose.addEventListener('click', hideTermsModal);
        termsCancelBtn.addEventListener('click', hideTermsModal);
        termsModal.addEventListener('click', (e) => {
            if (e.target === termsModal) hideTermsModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && termsModal.classList.contains('show')) {
                hideTermsModal();
            }
        });

        // Submit Form
        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (isFormSubmitting) return;

            let hasErrors = false;
            formInputs.forEach(input => {
                validateField({ target: input });
                if (input.getAttribute('aria-invalid') === 'true') {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                showNotification('Mohon perbaiki kesalahan pada form', 'error');
                return;
            }

            showTermsModal();
        });

        // Terms Submit
        termsSubmitBtn.addEventListener('click', async () => {
            if (!termsCheckbox.checked) return;

            hideTermsModal();
            isFormSubmitting = true;
            setSubmitButtonLoading(true);

            try {
                const formData = new FormData(bookingForm);

                const data = {
                    contact_name: formData.get('contactName'),
                    whatsapp_number: formatPhoneNumber(formData.get('phone')),
                    booking_date: formData.get('date'),
                    booking_time: formData.get('time'),
                    session_name: sessionData.session_name,
                    package_name: sessionData.package_name,
                    selected_backgrounds: [], // tidak ada background
                    selected_extra_items: selectedExtras,
                    total_price: sessionData.base_price + selectedExtras.reduce((sum, item) => sum + item.price, 0),
                    notes: formData.get('notes'),
                    baby_name: formData.get('babyName') || '-',
                    baby_age: formData.get('babyAge') || null,
                };

                const response = await fetch('/booking', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data),
                });

                if (!response.ok) {
                    const result = await response.json();
                    throw new Error(result.message || 'Gagal menyimpan pesanan');
                }

                const result = await response.json();
                showSuccessMessage();
                resetForm();
                sendWhatsAppMessage();

            } catch (error) {
                showNotification('Terjadi kesalahan: ' + error.message, 'error');
                console.error('Form submission error:', error);
            } finally {
                isFormSubmitting = false;
                setSubmitButtonLoading(false);
            }
        });

        // Loading Button
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

        // WhatsApp Message
        function sendWhatsAppMessage() {
            const formData = new FormData(bookingForm);
            const contactName = formData.get('contactName');
            const rawPhone = formData.get('phone');
            const phone = formatPhoneNumber(rawPhone);
            const babyName = formData.get('babyName') || '-';
            const ageNames = {
                '1-year': '1 Tahun',
                '2-years': '2 Tahun',
                '3-years': '3 Tahun',
                '4-years': '4 Tahun',
                '5-years': '5 Tahun',
                '6-years': '6 Tahun'
            };
            const babyAge = ageNames[formData.get('babyAge')] || '-';
            const date = formData.get('date');
            const timeNames = {
                '10:00': '10.00 WIB', '11:00': '11.00 WIB', '12:00': '12.00 WIB',
                '13:00': '13.00 WIB', '14:00': '14.00 WIB', '15:00': '15.00 WIB', '16:00': '16.00 WIB'
            };
            const time = timeNames[formData.get('time')] || '-';
            const notes = formData.get('notes') || 'Tidak ada';
            const extrasText = selectedExtras.length > 0
                ? selectedExtras.map(item => `- ${item.name} – ${formatPrice(item.price)}`).join('\n')
                : 'Tidak ada tambahan';
            const totalPrice = sessionData.base_price + selectedExtras.reduce((sum, item) => sum + item.price, 0);

            const message = `BOOKING BABY SMASH CAKE – PEACE PICTURE STUDIO

        Nama Kontak      : ${contactName}
        No. WhatsApp     : +${phone}
        Nama Bayi        : ${babyName}
        Usia Bayi        : ${babyAge}

        Paket            : Baby Smash Cake
        Harga Paket      : ${formatPrice(sessionData.base_price)}

        Tanggal          : ${date}
        Waktu            : ${time}

        Tambahan Item    :
        ${extrasText}

        Catatan Tambahan :
        ${notes}

        Total Harga      : ${formatPrice(totalPrice)}

        Saya telah membaca dan menyetujui syarat & ketentuan dari Peace Picture Studio.

        --------------------------------------------------
        Terima kasih telah memilih Peace Picture Studio.
        Kami akan segera menghubungi Anda untuk konfirmasi lebih lanjut.

        *Note: Kue tidak disediakan oleh studio, mohon dibawa sendiri.*`;

            const whatsappUrl = `https://wa.me/6285782086279?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }
    </script>
@endpush