@extends('layouts.main')
@section('content')
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
@endsection
