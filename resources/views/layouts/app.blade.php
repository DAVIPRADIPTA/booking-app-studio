<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Peace Picture Studio')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'playfair': ['Playfair Display', 'serif'],
                        'dancing': ['Dancing Script', 'cursive'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-in-out',
                        'fade-in-up': 'fadeInUp 1s ease-out',
                        'slide-in': 'slideIn 1s ease-out',
                        'ken-burns': 'kenBurns 20s ease-in-out infinite alternate',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideIn: {
                            '0%': { opacity: '0', transform: 'scale(1.05)' },
                            '100%': { opacity: '1', transform: 'scale(1)' }
                        },
                        kenBurns: {
                            '0%': { transform: 'scale(1) rotate(0deg)' },
                            '100%': { transform: 'scale(1.1) rotate(1deg)' }
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px rgba(251, 191, 36, 0.3)' },
                            '100%': { boxShadow: '0 0 30px rgba(251, 191, 36, 0.6)' }
                        }
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="font-inter antialiased overflow-x-hidden">
    @yield('content')
    
    @stack('scripts')
</body>
</html>
