<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- NEW: Socialite Login Section -->
    @if (Route::has('login.github'))
        <div class="mt-6 pt-4 border-t dark:border-gray-700">
            <p class="text-center text-sm mb-3 text-gray-500">{{ __('Or login with') }}</p>
            <a href="{{ route('login.github') }}"
                class="w-full inline-flex items-center justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 transition duration-150 ease-in-out">
                <!-- GitHub SVG Icon -->
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10 0C4.477 0 0 4.484 0 10.017c0 4.417 2.865 8.169 6.83 9.479.499.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.157-1.107-1.46-1.107-1.46-.908-.619.069-.607.069-.607 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.83.092-.643.35-1.088.636-1.338-2.22-.253-4.555-1.116-4.555-4.945 0-1.092.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.268 2.75 1.022A9.553 9.553 0 0110 4.041c.854.008 1.713.111 2.493.303 1.908-1.29 2.747-1.022 2.747-1.022.546 1.378.202 2.398.098 2.65.64.7 1.028 1.596 1.028 2.688 0 3.839-2.339 4.686-4.566 4.935.359.308.678.917.678 1.846 0 1.33-.013 2.398-.013 2.723 0 .269.18.577.688.479C17.135 18.188 20 14.437 20 10.017A10.005 10.005 0 0010 0z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('Login with GitHub') }}
            </a>
        </div>
    @endif
    <!-- End Socialite Section -->
</x-guest-layout>
