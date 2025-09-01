@extends('layouts.app')
@section('title', 'Pre-Wedding Session - Peace Picture Studio')

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
            background-image: url('{{ asset("images/prewed/3.jpg") }}');
            background-position: center center;
            background-size: cover;
            background-repeat: no-repeat;
            position: absolute;
            inset: 0;
            z-index: 0;
            filter: brightness(0.5) contrast(1.1);
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

        /* Package Cards - Deep Red Theme */
        .package-section {
            display: flex;
            flex-direction: column;
            gap: clamp(2rem, 3vw, 2.5rem);
            width: 100%;
            max-width: 850px;
            align-items: center;
        }

        @media (min-width: 768px) {
            .package-section {
                flex-direction: row;
                gap: clamp(2.5rem, 5vw, 3.5rem);
                justify-content: center;
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
            max-width: 380px;
            flex: 1;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .package-card:hover {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.18);
            transform: translateY(-4px) scale(1.02);
        }

        .package-card.selected {
            border-color: rgba(220, 38, 38, 0.8);
            background: rgba(220, 38, 38, 0.15);
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 35px 100px rgba(220, 38, 38, 0.4);
        }

        /* Deep Red Selection Checkmark */
        .package-card::after {
            content: '✓';
            position: absolute;
            top: 1.2rem;
            right: 1.2rem;
            width: 2.5rem;
            height: 2.5rem;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
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
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
            z-index: 15;
        }

        .package-card.selected::after {
            opacity: 1;
            transform: scale(1);
        }

        /* Package Card Content */
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
            content: '♥';
            color: rgba(220, 38, 38, 0.9);
            font-size: 0.8rem;
            font-weight: bold;
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

        /* Package Notice */
        .package-notice {
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

        .package-notice.hidden {
            display: none;
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

        /* Ikon kalender jadi putih dan responsif */
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
        @supports
        (-moz - appearance)
            {
            .form-input[type="date"] {
                -moz-appearance: none;
                appearance: none;
            }
        }


        /* Background Selection - Deep Red Theme */
        .background-section {
            margin-bottom: clamp(2rem, 4vw, 2.8rem);
            transition: all 0.3s ease;
            padding: clamp(1.3rem, 2.5vw, 1.8rem);
            background: rgba(255, 255, 255, 0.08);
            border-radius: clamp(0.9rem, 1.8vw, 1.3rem);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
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
            background: rgba(220, 38, 38, 0.25);
            border-color: rgba(220, 38, 38, 0.5);
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

        /* Enhanced Background Selection Animations - Deep Red */
        .background-option {
            position: relative;
            overflow: hidden;
        }

        .background-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.2),
                    transparent);
            transition: left 0.6s ease;
            z-index: 1;
        }

        .background-option:hover::before {
            left: 100%;
        }

        .background-option.selected::after {
            content: '✓';
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 1.8rem;
            height: 1.8rem;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
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
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
            z-index: 2;
        }

        .background-option.selected::after {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }

        .background-option.selecting {
            transform: scale(0.95);
            transition: transform 0.2s ease;
        }

        .background-option.selecting .background-image {
            filter: brightness(1.3) contrast(1.2);
        }

        /* Pulse animation for newly selected backgrounds - Deep Red */
        @keyframes backgroundPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }

        .background-option.selected {
            animation: backgroundPulse 0.8s ease-out;
        }

        .background-option {
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-radius: clamp(0.7rem, 1.4vw, 1rem);
            padding: clamp(0.7rem, 1.4vw, 1rem);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 180px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .background-option:hover:not(.disabled) {
            border-color: rgba(255, 255, 255, 0.55);
            background: rgba(255, 255, 255, 0.18);
            transform: translateY(-2px) scale(1.01);
        }

        .background-option.selected {
            border-color: rgba(220, 38, 38, 0.85);
            background: rgba(220, 38, 38, 0.15);
            transform: translateY(-2px) scale(1.02);
        }

        .background-option.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            filter: grayscale(1);
        }

        /* Natural Background Images */
        .background-image {
            width: 100%;
            height: clamp(100px, 18vw, 130px);
            object-fit: cover;
            border-radius: calc(clamp(0.7rem, 1.4vw, 1rem) - 4px);
            margin-bottom: clamp(0.6rem, 1.2vw, 0.9rem);
            transition: all 0.3s ease;
            filter: brightness(1.0) contrast(1.0) saturate(1.0);
        }

        .background-option:hover .background-image {
            filter: brightness(1.1) contrast(1.1) saturate(1.0);
        }

        .background-option.selected .background-image {
            filter: brightness(1.15) contrast(1.1) saturate(1.0);
        }

        .background-name {
            font-size: clamp(0.75rem, 1.2vw, 0.85rem);
            color: rgba(255, 255, 255, 0.95);
            font-weight: 500;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
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
            accent-color: rgba(220, 38, 38, 0.9);
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

        /* TOTAL PEMBAYARAN - Deep Red Theme */
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
            color: rgba(220, 38, 38, 0.95);
            text-shadow: 0 2px 12px rgba(220, 38, 38, 0.4);
            transition: all 0.4s ease;
        }

        .total-amount.updating {
            transform: scale(1.05);
            color: rgba(239, 68, 68, 0.95);
            text-shadow: 0 2px 12px rgba(239, 68, 68, 0.4);
        }

        /* SUBMIT SECTION - Deep Red Theme */
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
                    rgba(220, 38, 38, 0.9) 0%,
                    rgba(185, 28, 28, 0.9) 50%,
                    rgba(153, 27, 27, 0.9) 100%);
            border: 2px solid rgba(220, 38, 38, 0.6);
            border-radius: clamp(1rem, 2vw, 1.5rem);
            padding: 0;
            color: white;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(30px);
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(220, 38, 38, 0.4);
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
            box-shadow: 0 30px 80px rgba(220, 38, 38, 0.5);
            border-color: rgba(220, 38, 38, 0.8);
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

        /* Success Message - Deep Red Theme */
        .success-message {
            background: linear-gradient(135deg,
                    rgba(220, 38, 38, 0.30),
                    rgba(220, 38, 38, 0.18));
            border: 1px solid rgba(220, 38, 38, 0.6);
            border-radius: clamp(0.7rem, 1.4vw, 1rem);
            padding: clamp(1.2rem, 2.5vw, 1.8rem);
            margin-bottom: 1.8rem;
            color: #fca5a5;
            font-size: clamp(0.85rem, 1.5vw, 0.95rem);
            text-align: center;
            display: none;
            backdrop-filter: blur(30px);
            box-shadow: 0 20px 50px rgba(220, 38, 38, 0.3);
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

        /* TERMS AND CONDITIONS MODAL - Deep Red Theme */
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
            background: linear-gradient(135deg, #dc2626, #b91c1c);
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
            color: rgba(220, 38, 38, 0.8);
        }

        .terms-content strong {
            color: rgba(255, 255, 255, 0.95);
            font-weight: 600;
            background: rgba(220, 38, 38, 0.1);
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
            accent-color: rgba(220, 38, 38, 0.9);
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
                    rgba(220, 38, 38, 0.9) 0%,
                    rgba(185, 28, 28, 0.9) 100%);
            border-color: rgba(220, 38, 38, 0.6);
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
                    rgba(220, 38, 38, 1) 0%,
                    rgba(185, 28, 28, 1) 100%);
            border-color: rgba(220, 38, 38, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
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

            .background-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .extras-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .package-section {
                flex-direction: column;
            }
        }

        /* Accessibility */
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

        /* Performance Optimizations */
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
                <div class="studio-brand">PEACEPHOTOSTUDIO</div>
                <h1 class="hero-title font-dancing">Pre-Wedding</h1>
                <p class="hero-description">
                    Beautiful and Full Histories - Capture your eternal love story with romantic and elegant pre-wedding
                    photography sessions that will create memories to last a lifetime
                </p>
            </div>
            <!-- Main Content Grid -->
            <div class="content-grid">
                <!-- Pricing Image Section -->
                <div class="pricing-section">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Prewed_price.jpg-DV6Cgo0Ug7wR0yMXl2Ok7Igf4oA7GB.jpeg"
                        alt="Pre-Wedding Package Flyer" class="pricing-image" loading="lazy">
                </div>
                <!-- Package Cards Section -->
                <div class="package-section">
                    <!-- Prewed I Package -->
                    <div class="package-card" data-package="prewed1" data-price="700000" data-backgrounds="2" tabindex="0"
                        role="button" aria-label="Select Prewed I Package">
                        <div class="package-header">
                            <h3 class="package-title font-dancing">Prewed I</h3>
                            <div class="package-price">IDR 700k</div>
                        </div>
                        <ul class="package-features">
                            <li>50 Minutes Photosession</li>
                            <li>2 Background</li>
                            <li>20 Edited Photo</li>
                            <li>Max 1 Wardrobe</li>
                            <li>1 Photo Printed 12rs + Frame</li>
                            <li>Google Drive Expired 1 Month</li>
                        </ul>
                    </div>
                    <!-- Prewed II Package -->
                    <div class="package-card" data-package="prewed2" data-price="1000000" data-backgrounds="3" tabindex="0"
                        role="button" aria-label="Select Prewed II Package">
                        <div class="package-header">
                            <h3 class="package-title font-dancing">Prewed II</h3>
                            <div class="package-price">IDR 1000k</div>
                        </div>
                        <ul class="package-features">
                            <li>70 Minutes Photosession</li>
                            <li>3 Background</li>
                            <li>40 Edited Photo</li>
                            <li>Max 2 Wardrobe</li>
                            <li>2 Photo Printed 16rs + Frame</li>
                            <li>Google Drive Expired 1 Month</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Booking Form Section -->
            <div class="booking-section">
                <h2 class="booking-title font-dancing">Book Your Dream Session</h2>
                <!-- Package Selection Notice -->
                <div class="package-notice" id="packageNotice" role="alert" aria-live="polite">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Silakan pilih paket terlebih dahulu untuk melihat pilihan background yang tersedia</span>
                </div>
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
                        <div class="form-group full-width">
                            <div id="time-availability-info" class="package-notice hidden">
                                <span id="availability-message">Informasi ketersediaan akan muncul di sini.</span>
                            </div>
                        </div>
                    </div>
                    <div class="background-section disabled" id="backgroundSection">
                        <div class="background-title">
                            <span>Pilih Background</span>
                            <div class="background-counter" id="backgroundCounter">0/0 dipilih</div>
                        </div>
                        <div class="background-grid" role="group" aria-label="Background selection">
                            @foreach ($backgroundItems as $item)
                                <div class="background-option" data-background="{{ $item->id }}" data-name="{{ $item->name }}"
                                    tabindex="0" role="button" aria-label="Select {{ $item->name }} background">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                        class="background-image" loading="lazy">
                                    <div class="background-name">{{ $item->name }}</div>
                                </div>
                            @endforeach
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
                                Tulis permintaan khusus, catatan tambahan, atau detail lainnya yang ingin Anda sampaikan
                            </div>
                            <textarea id="notes" name="notes" class="notes-textarea"
                                placeholder="Contoh: Saya ingin foto dengan tema vintage, atau ada request pose tertentu..."
                                aria-describedby="notes-help"></textarea>
                            <div id="notes-help" class="sr-only">Optional field for additional requests or special notes
                            </div>
                        </div>
                    </div>
                    <!-- TOTAL PEMBAYARAN -->
                    <div class="total-payment-section">
                        <div class="total-payment-card">
                            <div class="total-amount" id="totalPrice" aria-live="polite">Total : RP.0-</div>
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
                        <div id="submit-help" class="sr-only">Submit your pre-wedding booking request</div>
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
        <!-- Dynamic Background Selection -->

    </div>
    <!-- Bottom Navigation Component -->
    <x-bottom-nav current-route="prewed" />
    </div>
    <!-- TERMS AND CONDITIONS MODAL -->
    <div class="terms-modal-overlay" id="termsModal" role="dialog" aria-labelledby="termsModalTitle" aria-modal="true">
        <div class="terms-modal">
            <div class="terms-modal-header">
                <h2 class="terms-modal-title" id="termsModalTitle">Syarat & Ketentuan</h2>
                <p class="terms-modal-subtitle">(Aturan & Perjanjian Booking Photoshoot)</p>
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
                        Dengan mencentang Syarat & Ketentuan (Aturan & Perjanjian Booking Photoshoot) Anda dianggap telah
                        membaca dan menyetujui seluruh ketentuan di atas.
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
/*
  Prewed booking — single, robust script
  - Exposes window.fetchAvailableTimes (fixes ReferenceError)
  - Extras: supports data-price, qty, subtotal UI
  - Sends selected_backgrounds as IDs and selected_extra_items as IDs
  - Normalizes WA number to +62
  - Handles server responses (redirect_url expected)
*/
(function () {
    'use strict';

    // ----------------------
    // Helper utils
    // ----------------------
    const parsePrice = v => {
        if (typeof v === 'number') return v;
        if (!v && v !== 0) return 0;
        const s = String(v);
        const digits = s.replace(/[^\d]/g, '');
        return digits ? Number(digits) : 0;
    };
    const formatPrice = p => `Rp${(Number(p)||0).toLocaleString('id-ID')}`;
    const normalizePhone = raw => {
        if (!raw) return '';
        let s = String(raw).trim().replace(/[^\d+]/g, '');
        if (s.startsWith('+')) return s;
        if (s.startsWith('62')) return '+' + s;
        if (s.startsWith('0')) return '+62' + s.slice(1);
        return '+62' + s;
    };
    const showNotification = (msg, type='info') => {
        const n = document.createElement('div');
        n.className = `notification notification-${type}`;
        n.textContent = msg;
        const bg = (type==='error') ? 'rgba(239,68,68,.95)' : (type==='warning' ? 'rgba(245,158,11,.95)' : 'rgba(59,130,246,.95)');
        n.style.cssText = `position:fixed; top:1.2rem; right:1.2rem; background:${bg}; color:#fff; padding:.6rem .9rem; border-radius:.5rem; z-index:9999; box-shadow:0 8px 24px rgba(0,0,0,.18);`;
        document.body.appendChild(n);
        setTimeout(()=>{ n.style.transition='opacity .2s, transform .2s'; n.style.opacity='0'; n.style.transform='translateX(6px)'; setTimeout(()=> n.remove(), 220); }, 3200);
    };

    // ----------------------
    // Blade-provided config
    // ----------------------
    const prewedBackgroundIds = [
        @foreach($backgroundItems as $item)
            '{{ $item->id }}',
        @endforeach
    ];

    const packageBackgrounds = {
        'prewed1': { maxBackgrounds: 2, availableBackgrounds: prewedBackgroundIds },
        'prewed2': { maxBackgrounds: 3, availableBackgrounds: prewedBackgroundIds }
    };

    // ----------------------
    // State & DOM refs
    // ----------------------
    let selectedPackage = null; // {id, name, price, maxBackgrounds}
    let selectedBackgrounds = []; // [{id, name}]
    let selectedExtras = []; // [{id, name, unitPrice, qty, totalPrice}]
    let basePrice = 0;
    let maxBackgrounds = 0;
    let isFormSubmitting = false;

    // DOM (guarded)
    const getEl = (sel) => document.getElementById(sel);
    const packageCards = document.querySelectorAll('.package-card');
    const backgroundOptions = document.querySelectorAll('.background-option');
    const extraCheckboxes = document.querySelectorAll('.extra-checkbox');
    const totalPriceElement = getEl('totalPrice');
    const bookingForm = getEl('bookingForm');
    const submitBtn = getEl('submitBtn');
    const packageNotice = getEl('packageNotice');
    const backgroundSection = getEl('backgroundSection');
    const backgroundCounter = getEl('backgroundCounter');
    const successMessage = getEl('successMessage');
    const termsModal = getEl('termsModal');
    const termsCheckbox = getEl('termsCheckbox');
    const termsSubmitBtn = getEl('termsSubmitBtn');
    const termsCancelBtn = getEl('termsCancelBtn');
    const termsModalClose = getEl('termsModalClose');
    const dateInput = getEl('date');
    const timeSelect = getEl('time');
    const timeInfoBox = getEl('time-availability-info');
    const availabilityMessage = getEl('availability-message');

    // ----------------------
    // fetchAvailableTimes (exposed on window)
    // ----------------------
    window.fetchAvailableTimes = async function fetchAvailableTimes() {
        if (!dateInput || !timeSelect) return;
        const selectedDate = (dateInput.value || '').trim();
        if (!selectedDate) return;

        timeSelect.disabled = true;
        timeSelect.innerHTML = '<option value="">Memuat...</option>';
        if (timeInfoBox) { timeInfoBox.classList.add('hidden'); timeInfoBox.style.background=''; timeInfoBox.style.borderColor=''; }

        try {
            const resp = await fetch(`/api/available-times?booking_date=${encodeURIComponent(selectedDate)}`, { headers:{ 'Accept':'application/json' }});
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            const data = await resp.json();

            timeSelect.innerHTML = '';
            if (data.status === 'full') {
                const o = document.createElement('option'); o.value=''; o.textContent='Hari ini full booked'; o.disabled=true; timeSelect.appendChild(o); timeSelect.disabled=true;
                if (availabilityMessage) availabilityMessage.textContent = 'Maaf, tidak ada slot tersedia di tanggal ini. Silakan pilih tanggal lain.';
                if (timeInfoBox) { timeInfoBox.classList.remove('hidden'); timeInfoBox.style.background='linear-gradient(135deg, rgba(239,68,68,.18), rgba(239,68,68,.1))'; timeInfoBox.style.borderColor='rgba(239,68,68,.4)'; }
            } else {
                (data.available_times || []).forEach(t => {
                    const o = document.createElement('option'); o.value=t; o.textContent = `${t} WIB`; timeSelect.appendChild(o);
                });
                timeSelect.disabled = false;
                if (availabilityMessage) {
                    if (data.status === 'limited') {
                        availabilityMessage.textContent = `Hanya tersisa ${data.available_times.length} slot. Segera booking!`;
                        if (timeInfoBox) timeInfoBox.style.background = 'linear-gradient(135deg, rgba(234,179,8,.18), rgba(234,179,8,.1))';
                    } else {
                        availabilityMessage.textContent = `Ada ${data.available_times.length} slot (sesi waktu) yang tersedia.`;
                        if (timeInfoBox) timeInfoBox.style.background = 'linear-gradient(135deg, rgba(59,130,246,.18), rgba(59,130,246,.1))';
                    }
                    if (timeInfoBox) { timeInfoBox.classList.remove('hidden'); setTimeout(()=> timeInfoBox.classList.add('show'), 50); }
                }
            }
        } catch (err) {
            console.error('Gagal memuat ketersediaan waktu:', err);
            timeSelect.innerHTML = '<option value="">Gagal muat</option>';
            timeSelect.disabled = true;
            if (timeInfoBox && availabilityMessage) {
                availabilityMessage.textContent = 'Gagal memuat ketersediaan waktu. Silakan coba beberapa saat lagi.';
                timeInfoBox.classList.remove('hidden');
            }
        }
    };

    // Attach change listener in JS (avoid inline onchange)
    if (dateInput) {
        dateInput.addEventListener('change', () => window.fetchAvailableTimes());
        if (dateInput.value) window.fetchAvailableTimes();
    }

    // ----------------------
    // Background & package logic
    // ----------------------
    function updateBackgroundAvailability(pkgId) {
        const cfg = packageBackgrounds[pkgId];
        if (!cfg) return;
        maxBackgrounds = Number(cfg.maxBackgrounds) || 0;
        if (backgroundSection) backgroundSection.classList.remove('disabled');
        if (packageNotice) packageNotice.classList.add('hidden');
        const avail = cfg.availableBackgrounds || [];
        backgroundOptions.forEach(o => {
            const id = o.dataset.background;
            if (avail.includes(id)) {
                o.classList.remove('disabled'); o.setAttribute('tabindex','0'); o.setAttribute('aria-disabled','false');
            } else {
                o.classList.add('disabled'); o.setAttribute('tabindex','-1'); o.setAttribute('aria-disabled','true'); o.classList.remove('selected');
                selectedBackgrounds = selectedBackgrounds.filter(b => String(b.id) !== String(id));
            }
        });
        updateBackgroundCounter();
    }
    function updateBackgroundCounter() {
        const sel = selectedBackgrounds.length;
        if (backgroundCounter) backgroundCounter.textContent = `${sel}/${maxBackgrounds} dipilih`;
        if (sel >= maxBackgrounds && maxBackgrounds > 0) {
            if (backgroundCounter) backgroundCounter.classList.add('warning');
            backgroundOptions.forEach(o => { if (!o.classList.contains('selected') && !o.classList.contains('disabled')) { o.style.opacity='0.3'; o.style.pointerEvents='none'; }});
        } else {
            if (backgroundCounter) backgroundCounter.classList.remove('warning');
            backgroundOptions.forEach(o => { if (!o.classList.contains('disabled')) { o.style.opacity=''; o.style.pointerEvents=''; }});
        }
    }

    packageCards.forEach(card => {
        card.addEventListener('click', function () {
            packageCards.forEach(c => { c.classList.remove('selected'); c.setAttribute('aria-selected','false'); });
            this.classList.add('selected'); this.setAttribute('aria-selected','true');

            selectedPackage = {
                id: this.dataset.package,
                name: (this.querySelector('.package-title') ? this.querySelector('.package-title').textContent : (this.dataset.package || 'Paket')),
                price: Number(this.dataset.price || 0),
                maxBackgrounds: Number(this.dataset.backgrounds || 0)
            };
            basePrice = Number(selectedPackage.price) || 0;
            maxBackgrounds = Number(selectedPackage.maxBackgrounds) || 0;

            // reset backgrounds
            backgroundOptions.forEach(o => { o.classList.remove('selected'); o.setAttribute('aria-selected','false'); });
            selectedBackgrounds = [];

            updateBackgroundAvailability(selectedPackage.id);
            updateTotalPrice();
        });
    });

    // auto select default (if dataset.default or prewed2 present)
    (function autoSelectDefault() {
        const auto = Array.from(packageCards).find(c => c.dataset.default === 'true' || (c.dataset.package && c.dataset.package.toLowerCase()==='prewed2'));
        if (auto && !Array.from(packageCards).some(c=>c.classList.contains('selected'))) auto.click();
    })();

    backgroundOptions.forEach(opt => {
        opt.addEventListener('click', function () {
            if (this.classList.contains('disabled')) { showNotification('Background ini tidak tersedia untuk paket yang dipilih','error'); return; }
            const id = this.dataset.background;
            const name = this.dataset.name || id;
            const found = selectedBackgrounds.find(b => String(b.id) === String(id));
            if (found) {
                this.classList.remove('selected'); this.setAttribute('aria-selected','false');
                selectedBackgrounds = selectedBackgrounds.filter(b => String(b.id) !== String(id));
            } else {
                if (selectedBackgrounds.length >= maxBackgrounds) { showNotification(`Maksimal ${maxBackgrounds} background untuk paket ini`,'error'); return; }
                this.classList.add('selected'); this.setAttribute('aria-selected','true');
                selectedBackgrounds.push({ id, name });
            }
            updateBackgroundCounter();
        });
        opt.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); opt.click(); }});
    });

    // ----------------------
    // Extras: consistent pricing & qty
    // Expected markup hints:
    // .extra-checkbox (value/id, data-name, data-price)
    // inside same container: .extra-qty (number input), .extra-price-input (optional), [data-extra-subtotal]
    // ----------------------
    const findRelated = checkbox => {
        const container = checkbox.closest('.extra-row') || checkbox.parentElement || checkbox;
        const qty = container ? container.querySelector('.extra-qty') : null;
        const priceInput = container ? container.querySelector('.extra-price-input') : null;
        const subtotalEl = container ? container.querySelector('[data-extra-subtotal]') : null;
        return { container, qty, priceInput, subtotalEl };
    };

    const extraInitData = (cb, idx) => {
        const id = (cb.value && !isNaN(Number(cb.value))) ? Number(cb.value) : (cb.dataset.id ? (isNaN(Number(cb.dataset.id)) ? cb.dataset.id : Number(cb.dataset.id)) : `extra-${idx}`);
        const name = cb.dataset.name || cb.dataset.label || (cb.nextElementSibling ? cb.nextElementSibling.textContent.trim() : `Extra ${id}`);
        const dataPrice = parsePrice(cb.dataset.price);
        const related = findRelated(cb);
        let unit = dataPrice;
        if (related.priceInput) unit = parsePrice(related.priceInput.value || related.priceInput.getAttribute('value') || unit);
        let qty = 1;
        if (related.qty) {
            const qv = parseInt(related.qty.value || related.qty.getAttribute('value') || '1', 10);
            qty = isNaN(qv) || qv < 1 ? 1 : qv;
        }
        return { id, name, unitPrice: Number(unit)||0, qty: Number(qty)||1, totalPrice: (Number(unit)||0) * (Number(qty)||1), related };
    };

    function updateExtrasState(cb, idx) {
        const d = extraInitData(cb, idx);
        if (cb.checked) {
            const exist = selectedExtras.find(x => String(x.id) === String(d.id));
            if (exist) { exist.unitPrice = d.unitPrice; exist.qty = d.qty; exist.totalPrice = d.totalPrice; }
            else selectedExtras.push({ id:d.id, name:d.name, unitPrice:d.unitPrice, qty:d.qty, totalPrice:d.totalPrice });
        } else {
            selectedExtras = selectedExtras.filter(x => String(x.id) !== String(d.id));
        }
        if (d.related && d.related.subtotalEl) d.related.subtotalEl.textContent = formatPrice(d.totalPrice);
        updateTotalPrice();
    }

    function updateExtrasOnInput(cb, idx) {
        if (!cb.checked) return;
        const d = extraInitData(cb, idx);
        const exist = selectedExtras.find(x => String(x.id) === String(d.id));
        if (exist) { exist.unitPrice = d.unitPrice; exist.qty = d.qty; exist.totalPrice = d.totalPrice; }
        else selectedExtras.push({ id:d.id, name:d.name, unitPrice:d.unitPrice, qty:d.qty, totalPrice:d.totalPrice });
        if (d.related && d.related.subtotalEl) d.related.subtotalEl.textContent = formatPrice(d.totalPrice);
        updateTotalPrice();
    }

    extraCheckboxes.forEach((cb, idx) => {
        // initial if checked
        const init = extraInitData(cb, idx);
        if (cb.checked) selectedExtras.push({ id:init.id, name:init.name, unitPrice:init.unitPrice, qty:init.qty, totalPrice:init.totalPrice });
        // change handler
        cb.addEventListener('change', () => updateExtrasState(cb, idx));
        // wire qty/price inputs if present
        const rel = findRelated(cb);
        if (rel.qty) {
            rel.qty.addEventListener('input', () => { rel.qty.value = rel.qty.value.replace(/[^\d]/g,'') || '1'; updateExtrasOnInput(cb, idx); });
            rel.qty.addEventListener('blur', () => { if (!rel.qty.value || Number(rel.qty.value) < 1) rel.qty.value = '1'; updateExtrasOnInput(cb, idx); });
        }
        if (rel.priceInput) {
            rel.priceInput.addEventListener('input', () => updateExtrasOnInput(cb, idx));
            rel.priceInput.addEventListener('blur', () => updateExtrasOnInput(cb, idx));
        }
        if (init.related && init.related.subtotalEl) init.related.subtotalEl.textContent = formatPrice(init.totalPrice);
    });

    // ----------------------
    // Price total
    // ----------------------
    function updateTotalPrice() {
        const extrasTotal = selectedExtras.reduce((s,e) => s + (Number(e.totalPrice) || (Number(e.unitPrice||0) * Number(e.qty||1))), 0);
        const total = (Number(basePrice) || 0) + extrasTotal;
        if (totalPriceElement) {
            totalPriceElement.classList.add('updating');
            setTimeout(()=> totalPriceElement.classList.remove('updating'), 220);
            totalPriceElement.textContent = `Total : ${formatPrice(total)}`;
            totalPriceElement.setAttribute('data-total', String(total));
            totalPriceElement.setAttribute('aria-label', `Total price: ${formatPrice(total)}`);
        }
    }

    // ----------------------
    // Validation helpers
    // ----------------------
    const formInputs = document.querySelectorAll('.form-input, .form-select, .notes-textarea');
    formInputs.forEach(i => { i.addEventListener('blur', validateField); i.addEventListener('input', clearFieldError); });
    function validateField(e){
        const field = e.target;
        if (!field) return true;
        const name = field.name;
        const val = (field.value || '').toString().trim();
        const errEl = document.getElementById(`${name}-error`);
        let msg = '';
        if (field.hasAttribute('required') && !val) msg = 'Field ini wajib diisi';
        else if ((name === 'phone' || name === 'whatsapp_number') && val && !/^\+?[\d]{9,15}$/.test(val.replace(/\s+/g,''))) msg = 'Nomor WhatsApp tidak valid';
        else if (name === 'date' && val) {
            const ch = new Date(val);
            const t = new Date(); t.setHours(0,0,0,0);
            if (isNaN(ch.getTime()) || ch < t) msg = 'Tanggal tidak boleh di masa lalu';
        }
        if (errEl) { errEl.textContent = msg; field.setAttribute('aria-invalid', msg ? 'true' : 'false'); field.style.borderColor = msg ? '#ef4444' : ''; }
        return !msg;
    }
    function clearFieldError(e) { const f = e.target; const el = document.getElementById(`${f.name}-error`); if (el) { el.textContent=''; f.setAttribute('aria-invalid','false'); f.style.borderColor=''; } }

    // ----------------------
    // Terms modal handlers
    // ----------------------
    function showTermsModal(){ if (!termsModal) return; termsModal.classList.add('show'); document.body.style.overflow='hidden'; if (termsCheckbox) termsCheckbox.checked=false; updateTermsButton(); }
    function hideTermsModal(){ if (!termsModal) return; termsModal.classList.remove('show'); document.body.style.overflow=''; if (submitBtn) submitBtn.focus(); }
    function updateTermsButton(){ if (!termsSubmitBtn || !termsCheckbox) return; termsSubmitBtn.disabled = !termsCheckbox.checked; termsCheckbox.checked ? termsSubmitBtn.classList.add('enabled') : termsSubmitBtn.classList.remove('enabled'); }
    if (termsCheckbox) termsCheckbox.addEventListener('change', updateTermsButton);
    if (termsModalClose) termsModalClose.addEventListener('click', hideTermsModal);
    if (termsCancelBtn) termsCancelBtn.addEventListener('click', hideTermsModal);
    if (termsModal) termsModal.addEventListener('click', (e)=> { if (e.target === termsModal) hideTermsModal(); });
    document.addEventListener('keydown', (e)=> { if (e.key === 'Escape' && termsModal && termsModal.classList.contains('show')) hideTermsModal(); });

    // ----------------------
    // Submit flow -> send to /booking (payment)
    // ----------------------
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (ev) {
            ev.preventDefault();
            if (isFormSubmitting) return;

            // quick checks
            if (!selectedPackage) { showNotification('Silakan pilih paket terlebih dahulu','error'); if (packageCards[0]) packageCards[0].focus(); return; }
            if (selectedBackgrounds.length === 0) { showNotification('Silakan pilih minimal 1 background','error'); if (backgroundSection) backgroundSection.scrollIntoView({behavior:'smooth', block:'center'}); return; }

            let hasErr = false;
            formInputs.forEach(inp => { const ok = validateField({ target: inp }); if (!ok || inp.getAttribute('aria-invalid') === 'true') hasErr = true; });
            if (hasErr) { showNotification('Mohon perbaiki kesalahan pada form','error'); return; }

            // show terms
            showTermsModal();
        });
    }

    // Terms submit sends final payload
    if (termsSubmitBtn) {
        termsSubmitBtn.addEventListener('click', async function () {
            if (!(termsCheckbox && termsCheckbox.checked)) return;
            hideTermsModal();
            isFormSubmitting = true;
            setSubmitLoading(true);

            try {
                if (!bookingForm) throw new Error('Form tidak ditemukan');
                const fd = new FormData(bookingForm);
                const payload = {
                    contact_name: fd.get('contactName') || fd.get('contact_name') || '',
                    whatsapp_number: normalizePhone(fd.get('phone') || fd.get('whatsapp_number') || ''),
                    booking_date: fd.get('date') || '',
                    booking_time: fd.get('time') || '',
                    session_name: 'prewed',
                    package_name: selectedPackage ? selectedPackage.name : '',
                    package_id: selectedPackage ? selectedPackage.id : '',
                    package_price: Number(selectedPackage ? selectedPackage.price : basePrice),
                    selected_backgrounds: selectedBackgrounds.map(b => b.id), // ids only
                    selected_extra_items: selectedExtras.map(e => e.id),     // ids only
                    total_price: Number(totalPriceElement ? totalPriceElement.getAttribute('data-total') : (basePrice + selectedExtras.reduce((s,i)=>s + (i.totalPrice || i.unitPrice * i.qty || 0),0))),
                    notes: fd.get('notes') || null,
                    status: 'waiting_payment'
                };

                const resp = await fetch('/booking', {
                    method:'POST',
                    headers:{
                        'Content-Type':'application/json',
                        'Accept':'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(payload)
                });

                let body = {};
                try { body = await resp.json(); } catch (jerr) { try { body = { __raw: await resp.text() }; } catch(e){ body = { __err: String(e) }; } }

                if (resp.ok) {
                    if (body.redirect_url) {
                        showNotification('Berhasil. Mengarahkan ke pembayaran...','info');
                        setTimeout(()=> window.location.href = body.redirect_url, 400);
                        return;
                    } else {
                        showNotification(body.message || 'Pesanan dibuat. Lanjutkan ke pembayaran.','info');
                        setTimeout(()=> window.location.href = body.redirect_url || '/', 400);
                        return;
                    }
                } else {
                    if (resp.status === 409) { showNotification(body.message || 'Slot sudah diambil','error'); return; }
                    if (resp.status === 422) {
                        showNotification('Mohon perbaiki kesalahan pada form.','error');
                        if (body.errors && typeof body.errors === 'object') {
                            Object.keys(body.errors).forEach(k => {
                                const el = document.getElementById(`${k}-error`);
                                if (el) el.textContent = Array.isArray(body.errors[k]) ? body.errors[k].join(', ') : body.errors[k];
                            });
                        }
                        const first = document.querySelector('[aria-invalid="true"]');
                        if (first) first.scrollIntoView({ behavior:'smooth', block:'center' });
                        console.error('Validation errors:', body.errors || body);
                        return;
                    }
                    const serverMsg = body.message || body.__raw || `Server error ${resp.status}`;
                    showNotification(String(serverMsg).substring(0,200), 'error');
                    console.error('Booking API error:', { status: resp.status, body });
                    return;
                }

            } catch (err) {
                console.error('Form submission error:', err);
                showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                isFormSubmitting = false;
                setSubmitLoading(false);
            }
        });
    }

    function setSubmitLoading(on) {
        const btnContent = document.getElementById('btnContent');
        const btnLoading = document.getElementById('btnLoading');
        if (!submitBtn) return;
        if (on) {
            submitBtn.classList.add('loading'); submitBtn.disabled = true; submitBtn.setAttribute('aria-busy','true');
            if (btnContent) btnContent.style.display='none';
            if (btnLoading) btnLoading.style.display='flex';
        } else {
            submitBtn.classList.remove('loading'); submitBtn.disabled = false; submitBtn.setAttribute('aria-busy','false');
            if (btnLoading) btnLoading.style.display='none';
            if (btnContent) btnContent.style.display='flex';
        }
    }

    // success/reset utilities
    function showSuccessMessage() { if (!successMessage) return; successMessage.classList.add('show'); successMessage.focus(); setTimeout(()=> successMessage.classList.remove('show'), 8000); }
    function resetForm() {
        try {
            if (!bookingForm) return;
            bookingForm.reset();
            packageCards.forEach(c=>{ c.classList.remove('selected'); c.setAttribute('aria-selected','false'); });
            backgroundOptions.forEach(o=>{ o.classList.remove('selected','disabled'); o.setAttribute('aria-selected','false'); o.style.opacity=''; o.style.pointerEvents=''; });
            extraCheckboxes.forEach(cb=>cb.checked=false);
            selectedExtras = []; selectedBackgrounds = []; selectedPackage = null; basePrice = 0; maxBackgrounds = 0;
            if (backgroundSection) backgroundSection.classList.add('disabled');
            if (packageNotice) packageNotice.classList.remove('hidden');
            if (backgroundCounter) { backgroundCounter.textContent='0/0 dipilih'; backgroundCounter.classList.remove('warning'); }
            updateTotalPrice();
        } catch(e) { console.warn('reset error', e); }
    }

    // init
    (function init() {
        if (dateInput) dateInput.setAttribute('min', new Date().toISOString().split('T')[0]);
        updateTotalPrice();
        console.log('✨ Prewed booking script initialized (stable).');
    })();

    // expose for debug
    window.prewed = {
        fetchAvailableTimes,
        resetForm,
        getSelectedExtras: ()=> selectedExtras.map(e=> ({...e})),
        getSelectedBackgrounds: ()=> selectedBackgrounds.map(b=> ({...b}))
    };

})(); // IIFE
</script>
@endpush
