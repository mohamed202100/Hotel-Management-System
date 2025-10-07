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

                    <div class="mb-6 p-4 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-700 dark:text-blue-300">
                        {{ __('Your reservation will be submitted under your complete profile details and set to PENDING confirmation.') }}
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
                            <!-- Button is now ALWAYS enabled -->
                            <x-primary-button type="submit">
                                {{ __('Confirm Reservation Request') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Set min date to today for check-in
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('check_in_date').min = today;
    document.getElementById('check_out_date').min = today;

    function calculatePrice() {
        const roomSelect = document.getElementById('room_id');
        const checkInDate = document.getElementById('check_in_date').value;
        const checkOutDate = document.getElementById('check_out_date').value;
        const nightsDisplay = document.getElementById('total_nights');
        const priceDisplay = document.getElementById('total_amount_display');

        let selectedPrice = 0;
        let nights = 0;
        const TAX_RATE = 0.15;

        // Get room price
        if (roomSelect && roomSelect.value) {
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            selectedPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        }

        // Calculate nights
        if (checkInDate && checkOutDate) {
            const date1 = new Date(checkInDate);
            const date2 = new Date(checkOutDate);

            if (date2 > date1) {
                const diffTime = Math.abs(date2 - date1);
                nights = Math.floor(diffTime / (1000 * 60 * 60 * 24));
            } else {
                alert('Check-out date must be after Check-in date.');
                document.getElementById('check_out_date').value = '';
                nights = 0;
            }
        }

        nightsDisplay.textContent = nights;

        // Calculate total
        if (selectedPrice > 0 && nights > 0) {
            let total = selectedPrice * nights;
            total = total * (1 + TAX_RATE);
            priceDisplay.textContent = '$' + total.toLocaleString(undefined, {
                minimumFractionDigits: 2
            });
        } else {
            priceDisplay.textContent = '$0.00';
        }
    }

    // Adjust check-out min date dynamically
    document.getElementById('check_in_date').addEventListener('change', function() {
        const checkIn = new Date(this.value);
        if (!isNaN(checkIn)) {
            const minCheckout = new Date(checkIn);
            minCheckout.setDate(checkIn.getDate() + 1);
            document.getElementById('check_out_date').min = minCheckout.toISOString().split('T')[0];
        }
        calculatePrice();
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', calculatePrice);
    if (document.getElementById('room_id')) {
        document.getElementById('room_id').addEventListener('change', calculatePrice);
    }
    document.getElementById('check_out_date').addEventListener('change', calculatePrice);
</script>
