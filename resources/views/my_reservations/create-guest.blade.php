<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Book A Room') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Header and Back Button -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                        <h3 class="text-3xl font-extrabold text-indigo-500">{{ __('New Reservation Details') }}</h3>
                        <a href="{{ route('rooms.available') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                            {{ __('Back to Available Rooms') }}
                        </a>
                    </div>

                    <!-- Note: Customer data is pre-filled/derived from logged-in user -->
                    <div class="mb-6 p-4 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-700 dark:text-blue-300">
                        {{ __('Your reservation will be submitted under your registered name (') }}{{ Auth::user()->name ?? 'Guest' }}{{ __('), but please ensure your full profile is complete.') }}
                        <a href="{{ route('customer.edit-guest-profile') }}" class="font-bold underline ml-2">
                            {{ __('Complete Your Profile Details') }}
                        </a>
                    </div>

                    <form method="POST" action="{{ route('reservations.store-guest') }}" class="mt-6 space-y-6">
                        @csrf

                        <!-- Room Selection (Pre-selected if coming from 'Book Now') -->
                        <div>
                            <x-input-label for="room_id" :value="__('Selected Room')" />
                            <select id="room_id" name="room_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required onchange="calculatePrice()">
                                <option value="" disabled selected>{{ __('Select Room') }}</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" data-price="{{ $room->base_price }}"
                                        @if (isset($selectedRoomId) && $room->id == $selectedRoomId) selected @endif
                                        {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number }} ({{ $room->type }}) -
                                        ${{ number_format($room->base_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('room_id')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Check-in Date -->
                            <div>
                                <x-input-label for="check_in_date" :value="__('Check-in Date')" />
                                <x-text-input id="check_in_date" name="check_in_date" type="date"
                                    class="mt-1 block w-full" :value="old('check_in_date')" required onchange="calculatePrice()" />
                                <x-input-error class="mt-2" :messages="$errors->get('check_in_date')" />
                            </div>

                            <!-- Check-out Date -->
                            <div>
                                <x-input-label for="check_out_date" :value="__('Check-out Date')" />
                                <x-text-input id="check_out_date" name="check_out_date" type="date"
                                    class="mt-1 block w-full" :value="old('check_out_date')" required onchange="calculatePrice()" />
                                <x-input-error class="mt-2" :messages="$errors->get('check_out_date')" />
                            </div>
                        </div>

                        <!-- Price Display -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t dark:border-gray-700">
                            <!-- Nights Display -->
                            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm font-semibold text-gray-400">{{ __('Total Nights') }}</p>
                                <p id="total_nights" class="text-xl font-bold">0</p>
                            </div>

                            <!-- Total Price Display -->
                            <div class="bg-indigo-100 dark:bg-indigo-700 p-3 rounded-lg">
                                <p class="text-sm font-semibold text-indigo-400">
                                    {{ __('Total Est. Price (Inc. Tax)') }}</p>
                                <p id="total_amount_display"
                                    class="text-2xl font-extrabold text-indigo-900 dark:text-indigo-200">$0.00</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Confirm Reservation Request') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Set min date to today for check-in
    document.getElementById('check_in_date').min = new Date().toISOString().split('T')[0];
    document.getElementById('check_out_date').min = new Date().toISOString().split('T')[0];

    function calculatePrice() {
        const roomSelect = document.getElementById('room_id');
        const checkInDate = document.getElementById('check_in_date').value;
        const checkOutDate = document.getElementById('check_out_date').value;
        const nightsDisplay = document.getElementById('total_nights');
        const priceDisplay = document.getElementById('total_amount_display');

        let selectedPrice = 0;
        let nights = 0;
        const TAX_RATE = 0.15; // Assuming 15% tax as used in the controller

        // 1. Get Room Price
        if (roomSelect.value) {
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            selectedPrice = parseFloat(selectedOption.getAttribute('data-price'));
        }

        // 2. Calculate Nights
        if (checkInDate && checkOutDate) {
            const date1 = new Date(checkInDate);
            const date2 = new Date(checkOutDate);

            // Ensure Check-out is after Check-in
            if (date2 > date1) {
                // Calculate difference in milliseconds
                const diffTime = Math.abs(date2 - date1);
                // Convert to days
                nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            } else {
                // Clear out date if invalid
                document.getElementById('check_out_date').value = '';
            }
        }

        nightsDisplay.textContent = nights;

        // 3. Calculate Total Price
        if (selectedPrice > 0 && nights > 0) {
            let total = selectedPrice * nights;
            // Add tax
            total = total * (1 + TAX_RATE);
            priceDisplay.textContent = '$' + total.toFixed(2);
        } else {
            priceDisplay.textContent = '$0.00';
        }
    }

    // Initialize listeners and calculation
    document.addEventListener('DOMContentLoaded', calculatePrice);
    if (document.getElementById('room_id')) {
        document.getElementById('room_id').addEventListener('change', calculatePrice);
    }
</script>
