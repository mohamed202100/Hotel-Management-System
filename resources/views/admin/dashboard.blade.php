<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        {{ __('Welcome to the Hotel Management System') }}
                    </h1>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                        {{ __('Use the navigation below to manage rooms, customers, and reservations.') }}
                    </p>
                </div>

                <!-- Quick Management Links/Cards -->
                <div class="p-6 lg:p-8 grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Rooms Card -->
                    <a href="{{ route('rooms.index') }}"
                        class="block p-6 bg-indigo-500 hover:bg-indigo-600 rounded-lg shadow-lg transition duration-150 ease-in-out transform hover:scale-[1.02]">
                        <h2 class="text-2xl font-bold text-white mb-2">{{ __('Manage Rooms') }}</h2>
                        <p class="text-indigo-100">{{ __('Create, view, edit, and delete all room types and units.') }}
                        </p>
                        <div class="mt-4 text-right text-sm font-semibold text-indigo-200">{{ __('Go to Rooms') }}
                            &rarr;</div>
                    </a>

                    <!-- Customers Card -->
                    <a href="{{ route('customers.index') }}"
                        class="block p-6 bg-green-500 hover:bg-green-600 rounded-lg shadow-lg transition duration-150 ease-in-out transform hover:scale-[1.02]">
                        <h2 class="text-2xl font-bold text-white mb-2">{{ __('Manage Customers') }}</h2>
                        <p class="text-green-100">{{ __('Maintain detailed records of all hotel customers.') }}</p>
                        <div class="mt-4 text-right text-sm font-semibold text-green-200">{{ __('Go to Customers') }}
                            &rarr;</div>
                    </a>

                    <!-- Reservations Card (Placeholder for next step) -->
                    <a href="{{ route('reservations.index') }}"
                        class="block p-6 bg-red-500 hover:bg-red-600 rounded-lg shadow-lg transition duration-150 ease-in-out transform hover:scale-[1.02]">
                        <h2 class="text-2xl font-bold text-white mb-2">{{ __('Manage Reservations') }}</h2>
                        <p class="text-red-100">{{ __('View and create new bookings and check-in/out guests.') }}</p>
                        <div class="mt-4 text-right text-sm font-semibold text-red-200">{{ __('Go to Reservations') }}
                            &rarr;</div>
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
