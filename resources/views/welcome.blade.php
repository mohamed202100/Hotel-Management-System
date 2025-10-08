<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hotel Booking System') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Scripts (Tailwind CSS and App Assets) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        /* Hero Background with Parallax Effect */
        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            overflow: hidden;
        }

        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.7), rgba(0, 0, 0, 0.6));
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {

            0%,
            100% {
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.7), rgba(0, 0, 0, 0.6));
            }

            50% {
                background: linear-gradient(135deg, rgba(139, 92, 246, 0.7), rgba(0, 0, 0, 0.5));
            }
        }

        /* Fade In Animation */
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

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-fadeInLeft {
            animation: fadeInLeft 0.6s ease-out forwards;
        }

        .animate-scaleIn {
            animation: scaleIn 0.6s ease-out forwards;
        }

        /* Stagger Animation Delays */
        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        .delay-400 {
            animation-delay: 0.4s;
        }

        .delay-500 {
            animation-delay: 0.5s;
        }

        /* Floating Animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Pulse Glow Effect */
        @keyframes pulseGlow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
            }

            50% {
                box-shadow: 0 0 40px rgba(99, 102, 241, 0.8);
            }
        }

        .animate-pulseGlow {
            animation: pulseGlow 2s ease-in-out infinite;
        }

        /* Sidebar Transitions */
        aside {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 10;
            transition: all 0.3s ease;
        }

        .nav-link {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(to bottom, #6366f1, #8b5cf6);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover::before {
            transform: scaleY(1);
        }

        /* Card Hover Effects */
        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.3);
        }

        /* Dark Mode Smooth Transitions */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Button Ripple Effect */
        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::after {
            width: 300px;
            height: 300px;
        }

        /* Scroll Reveal */
        .reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loading Animation for Images */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }

        /* Dark Mode Toggle Animation */
        .dark-toggle {
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .dark-toggle:hover {
            transform: scale(1.15) rotate(180deg);
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 text-gray-800 dark:text-gray-200"
    x-init="darkMode = localStorage.getItem('darkMode') === 'true';
    $watch('darkMode', (value) => localStorage.setItem('darkMode', value));
    // Scroll Reveal
    const reveals = document.querySelectorAll('.reveal');
    window.addEventListener('scroll', () => {
        reveals.forEach(el => {
            const windowHeight = window.innerHeight;
            const elementTop = el.getBoundingClientRect().top;
            if (elementTop < windowHeight - 100) {
                el.classList.add('active');
            }
        });
    });">

    <div class="flex min-h-screen">

        <!-- Sidebar with Animations -->
        <aside class="w-64 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm shadow-2xl flex flex-col">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 animate-fadeInLeft">
                <h1
                    class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent text-center">
                    {{ config('app.name', 'HotelEase') }}
                </h1>
            </div>

            <nav class="flex-1 p-4 space-y-3">
                @auth
                    <?php
                    $isAdmin = Auth::user()->hasRole('admin');
                    $dashboardRoute = $isAdmin ? route('admin.dashboard') : route('guest.reservations.index');
                    ?>
                    <a href="{{ $dashboardRoute }}"
                        class="nav-link block py-2.5 px-4 font-semibold rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300 animate-fadeInLeft delay-100">
                        🏠 {{ __('My Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="nav-link block py-2.5 px-4 rounded-lg hover:bg-indigo-600 hover:text-white hover:scale-105 animate-fadeInLeft delay-100">
                        🔐 {{ __('Log In') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="nav-link block py-2.5 px-4 rounded-lg hover:bg-indigo-600 hover:text-white hover:scale-105 animate-fadeInLeft delay-200">
                            🧾 {{ __('Register') }}
                        </a>
                    @endif
                @endauth

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="#about"
                        class="nav-link block py-2.5 px-4 rounded-lg hover:bg-indigo-600 hover:text-white hover:scale-105 animate-fadeInLeft delay-300">
                        ℹ️ {{ __('About Us') }}
                    </a>
                    <a href="#contact"
                        class="nav-link block py-2.5 px-4 rounded-lg hover:bg-indigo-600 hover:text-white hover:scale-105 animate-fadeInLeft delay-400">
                        📞 {{ __('Contact Us') }}
                    </a>
                </div>

                @auth
                    <form action="{{ route('logout') }}" method="POST" class="mt-6 animate-fadeInLeft delay-500">
                        @csrf
                        <button type="submit"
                            class="w-full py-2.5 px-4 text-left rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white shadow-md hover:shadow-xl hover:scale-105 transition-all duration-300">
                            🚪 {{ __('Logout') }}
                        </button>
                    </form>
                @endauth
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto ml-64">

            <!-- Hero Section with Animations -->
            <section class="hero-bg relative h-[85vh] flex items-center justify-center">
                <div class="relative z-10 text-center text-white px-6">
                    <h1 class="text-5xl md:text-6xl font-extrabold mb-4 animate-fadeInUp animate-float">
                        Welcome to {{ config('app.name', 'HotelEase') }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto animate-fadeInUp delay-200">
                        Relax, enjoy, and experience world-class hospitality at our luxury hotel.
                    </p>

                    @auth
                        @if (!$isAdmin)
                            <a href="{{ route('rooms.available') }}"
                                class="btn-primary inline-block bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-8 rounded-full shadow-2xl transition-all duration-300 transform hover:scale-110 animate-fadeInUp delay-400 animate-pulseGlow">
                                {{ __('Book Your Stay Now') }} ✨
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}"
                            class="btn-primary inline-block bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-8 rounded-full shadow-2xl transition-all duration-300 transform hover:scale-110 animate-fadeInUp delay-400 animate-pulseGlow">
                            {{ __('Register to Book') }} 🚀
                        </a>
                    @endauth
                </div>
            </section>

            <!-- About Section with Reveal Animation -->
            <section id="about"
                class="py-20 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 text-center">
                <div class="max-w-6xl mx-auto px-4">
                    <h2
                        class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-10 reveal">
                        About Our Experience
                    </h2>
                    <p class="text-lg md:text-xl max-w-3xl mx-auto leading-relaxed reveal">
                        At <span
                            class="font-semibold text-indigo-600 dark:text-indigo-400">{{ config('app.name', 'HotelEase') }}</span>,
                        we blend modern convenience with classic luxury. Our commitment is to provide a seamless,
                        enjoyable, and memorable stay for every guest.
                    </p>

                    <!-- Features Section -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                        <div
                            class="feature-card bg-white/10 dark:bg-gray-800/60 backdrop-blur-lg rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-all duration-300 border border-white/20 dark:border-gray-700/40 reveal">
                            <div class="text-5xl mb-4 animate-bounce">🏨</div>
                            <h3 class="text-2xl font-bold mb-3 text-indigo-600 dark:text-indigo-400">
                                {{ __('Modern Rooms') }}</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                {{ __('Spacious, clean, and elegantly furnished rooms featuring smart technology and stunning views.') }}
                            </p>
                        </div>

                        <div
                            class="feature-card bg-white/10 dark:bg-gray-800/60 backdrop-blur-lg rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-all duration-300 border border-white/20 dark:border-gray-700/40 reveal">
                            <div class="text-5xl mb-4 animate-pulse">🍽</div>
                            <h3 class="text-2xl font-bold mb-3 text-indigo-600 dark:text-indigo-400">
                                {{ __('Fine Dining') }}</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                {{ __('Our top-rated restaurant offers a fusion of local delicacies and gourmet international cuisine.') }}
                            </p>
                        </div>

                        <div
                            class="feature-card bg-white/10 dark:bg-gray-800/60 backdrop-blur-lg rounded-2xl p-8 shadow-lg hover:-translate-y-2 transition-all duration-300 border border-white/20 dark:border-gray-700/40 reveal">
                            <div class="text-5xl mb-4 animate-pulse">💆</div>
                            <h3 class="text-2xl font-bold mb-3 text-indigo-600 dark:text-indigo-400">
                                {{ __('Spa & Wellness') }}</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                {{ __('Rejuvenate with our luxury spa, state-of-the-art fitness center, and serene indoor pool.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Hotel Location -->
                    <div class="mt-20">
                        <h3 class="text-3xl font-semibold mb-6 text-indigo-600 dark:text-indigo-400 reveal">
                            📍 Our Location
                        </h3>
                        <p class="text-lg mb-8 text-gray-700 dark:text-gray-300 max-w-2xl mx-auto reveal">
                            Find us in the heart of Cairo — where comfort meets culture.
                            We’re located near the Nile Corniche, minutes away from the city’s most famous attractions.
                        </p>

                        <div
                            class="overflow-hidden rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 reveal hover:scale-[1.01] transition-all duration-500">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3454.118123315341!2d31.23186937539323!3d30.05853837492521!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145840d51a6cf8b5%3A0xf3cc9e9d7ad5cc5b!2sCairo%20Downtown!5e0!3m2!1sen!2seg!4v1695639400000!5m2!1sen!2seg"
                                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                class="rounded-2xl filter brightness-95 dark:brightness-75 saturate-125">
                            </iframe>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Contact Section -->
            <section id="contact"
                class="py-20 bg-gradient-to-br from-indigo-600 to-purple-700 dark:from-indigo-900 dark:to-purple-900 text-white text-center reveal">
                <div class="max-w-5xl mx-auto px-4">
                    <h2 class="text-4xl md:text-5xl font-bold mb-10">{{ __('Contact Us') }}</h2>
                    <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto">
                        {{ __("We're always here to help you with bookings, inquiries, or any special requests.") }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mt-12">
                        <!-- Phone Card -->
                        <div
                            class="feature-card bg-white/10 dark:bg-gray-800/50 backdrop-blur-lg rounded-2xl p-8 shadow-lg hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 border border-white/20 dark:border-gray-700/40">
                            <div class="text-5xl mb-4 text-blue-500 dark:text-blue-400 animate-pulse">📞</div>
                            <h3 class="text-2xl font-bold mb-3 text-gray-800 dark:text-gray-100">{{ __('Phone') }}
                            </h3>
                            <p class="text-lg text-gray-700 dark:text-gray-300">+20 100 555 7890</p>
                        </div>

                        <!-- Email Card -->
                        <div
                            class="feature-card bg-white/10 dark:bg-gray-800/50 backdrop-blur-lg rounded-2xl p-8 shadow-lg hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 border border-white/20 dark:border-gray-700/40">
                            <div class="text-5xl mb-4 text-pink-500 dark:text-pink-400 animate-bounce">📧</div>
                            <h3 class="text-2xl font-bold mb-3 text-gray-800 dark:text-gray-100">{{ __('Email') }}
                            </h3>
                            <p class="text-lg text-gray-700 dark:text-gray-300">
                                <a href="mailto:contact@hotelease.com"
                                    class="hover:underline hover:text-blue-500 dark:hover:text-blue-400 transition-colors duration-300">
                                    contact@hotelease.com
                                </a>
                            </p>
                        </div>


                        <div
                            class="feature-card bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-xl hover:scale-105 hover:shadow-2xl transition-all duration-500 text-center">
                            <div class="text-5xl mb-4 animate-float">🌐</div>
                            <h3
                                class="text-2xl font-bold mb-3 bg-gradient-to-r from-indigo-300 to-purple-400 bg-clip-text text-transparent">
                                {{ __('Follow Us') }}
                            </h3>
                            <p class="text-gray-200 text-lg mb-6">
                                {{ __('Stay connected with us for offers, updates, and unforgettable moments!') }}
                            </p>
                            <div class="flex justify-center space-x-6 mt-4">
                                <a href="https://facebook.com/hotelease" target="_blank"
                                    class="text-3xl text-white hover:text-blue-400 transition-all duration-300 transform hover:scale-125 hover:rotate-6">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://instagram.com/hotelease" target="_blank"
                                    class="text-3xl text-white hover:text-pink-400 transition-all duration-300 transform hover:scale-125 hover:-rotate-6">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="https://twitter.com/hotelease" target="_blank"
                                    class="text-3xl text-white hover:text-sky-400 transition-all duration-300 transform hover:scale-125 hover:rotate-6">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="bg-gray-900 dark:bg-black text-gray-400 text-center py-8">
                <p class="text-lg">
                    &copy; {{ date('Y') }}
                    <span
                        class="font-semibold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                        {{ config('app.name', 'HotelEase') }}
                    </span>.
                    {{ __('All rights reserved.') }}
                </p>
            </footer>

        </main>
    </div>

    <!-- Dark Mode Toggle Button with Animation -->
    <button @click="darkMode = !darkMode"
        class="dark-toggle fixed bottom-6 right-6 p-4 rounded-full bg-gradient-to-br from-gray-900 to-gray-700 dark:from-yellow-400 dark:to-orange-500 text-white dark:text-gray-900 shadow-2xl z-50 hover:shadow-3xl transition-transform hover:scale-110 duration-300">
        <span x-show="!darkMode" class="text-2xl">🌙</span>
        <span x-show="darkMode" class="text-2xl">☀️</span>
    </button>

</body>

</html>
