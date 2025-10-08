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

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                        <h3 class="text-3xl font-extrabold text-indigo-500">{{ __('New Reservation') }}</h3>
                    </div>

                    <form method="POST" action="{{ route('reservations.store-guest') }}" class="mt-6 space-y-6">
                        @csrf

                        <!-- Room Selection -->
                        <div>
                            <x-input-label for="room_id" :value="__('Select Room')" />
                            <select id="room_id" name="room_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                                required onchange="calculatePrice()">
                                <option value="" disabled selected>{{ __('Select Room') }}</option>
                                @foreach ($rooms as $room)
                                    @if ($room->status === 'available')
                                        <option value="{{ $room->id }}" data-price="{{ $room->base_price }}">
                                            {{ $room->room_number }} ({{ $room->type }}) -
                                            ${{ number_format($room->base_price, 2) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('room_id')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Check-in -->
                            <div>
                                <x-input-label for="check_in_date" :value="__('Check-in Date')" />
                                <x-text-input id="check_in_date" name="check_in_date" type="date"
                                    class="mt-1 block w-full" required onchange="calculatePrice()" />
                                <x-input-error class="mt-2" :messages="$errors->get('check_in_date')" />
                            </div>

                            <!-- Check-out -->
                            <div>
                                <x-input-label for="check_out_date" :value="__('Check-out Date')" />
                                <x-text-input id="check_out_date" name="check_out_date" type="date"
                                    class="mt-1 block w-full" required onchange="calculatePrice()" />
                                <x-input-error class="mt-2" :messages="$errors->get('check_out_date')" />
                            </div>
                        </div>

                        <!-- Price Display -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t dark:border-gray-700">
                            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm font-semibold text-gray-400">{{ __('Total Nights') }}</p>
                                <p id="total_nights" class="text-xl font-bold">0</p>
                            </div>

                            <div class="bg-indigo-100 dark:bg-indigo-700 p-3 rounded-lg">
                                <p class="text-sm font-semibold text-indigo-400">
                                    {{ __('Total Est. Price (Inc. Tax)') }}</p>
                                <p id="total_amount_display"
                                    class="text-2xl font-extrabold text-indigo-900 dark:text-indigo-200">$0.00</p>
                            </div>
                        </div>

                        <x-primary-button type="submit">{{ __('Confirm Reservation') }}</x-primary-button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    const TAX_RATE = 0.15;

    // Min date today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('check_in_date').min = today;
    document.getElementById('check_out_date').min = today;

    function calculatePrice() {
        const roomSelect = document.getElementById('room_id');
        const checkIn = document.getElementById('check_in_date').value;
        const checkOut = document.getElementById('check_out_date').value;
        const nightsDisplay = document.getElementById('total_nights');
        const priceDisplay = document.getElementById('total_amount_display');

        let price = 0;
        let nights = 0;

        if (roomSelect && roomSelect.value) {
            const selected = roomSelect.options[roomSelect.selectedIndex];
            price = parseFloat(selected.dataset.price) || 0;
        }

        if (checkIn && checkOut) {
            const d1 = new Date(checkIn);
            const d2 = new Date(checkOut);
            if (d2 > d1) {
                nights = Math.floor((d2 - d1) / (1000 * 60 * 60 * 24));
            } else {
                nights = 0;
            }
        }

        nightsDisplay.textContent = nights;

        if (price > 0 && nights > 0) {
            let total = price * nights * (1 + TAX_RATE);
            priceDisplay.textContent = '$' + total.toFixed(2);
        } else {
            priceDisplay.textContent = '$0.00';
        }
    }

    // Update checkout min date
    document.getElementById('check_in_date').addEventListener('change', function() {
        const minCheckout = new Date(this.value);
        minCheckout.setDate(minCheckout.getDate() + 1);
        document.getElementById('check_out_date').min = minCheckout.toISOString().split('T')[0];
        calculatePrice();
    });

    document.getElementById('check_out_date').addEventListener('change', calculatePrice);
    document.getElementById('room_id').addEventListener('change', calculatePrice);

    document.addEventListener('DOMContentLoaded', calculatePrice);
</script>
