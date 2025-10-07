<?php
// Note: This file requires $rooms, $customers, and $reservation (the current reservation object)
// $reservation->invoice is loaded by controller
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Reservation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6"
                            role="alert">
                            <strong class="font-bold">{{ __('Validation Error!') }}</strong>
                            <ul class="mt-3 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Warning Message -->
                    @if (session('warning'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative mb-6"
                            role="alert">
                            <strong class="font-bold">{{ __('Warning!') }}</strong>
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                        <h3 class="text-3xl font-extrabold text-indigo-500">{{ __('Editing Reservation') }}
                            #{{ $reservation->id }}</h3>
                        <a href="{{ route('reservations.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                            {{ __('Back to List') }}
                        </a>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('reservations.update', $reservation->id) }}"
                        class="mt-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Customer & Room -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer -->
                            <div>
                                <label for="customer_id"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Customer') }}</label>
                                <select id="customer_id" name="customer_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('customer_id', $reservation->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->first_name }} {{ $customer->last_name }}
                                            ({{ $customer->passport_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Room -->
                            <div>
                                <label for="room_id"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Room') }}</label>
                                <select id="room_id" name="room_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}" data-price="{{ $room->base_price }}"
                                            {{ old('room_id', $reservation->room_id) == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }} ({{ $room->type }}) -
                                            ${{ number_format($room->base_price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="check_in_date"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Check-in Date') }}</label>
                                <input type="date" id="check_in_date" name="check_in_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    value="{{ old('check_in_date', $reservation->check_in_date) }}" required>
                            </div>
                            <div>
                                <label for="check_out_date"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Check-out Date') }}</label>
                                <input type="date" id="check_out_date" name="check_out_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    value="{{ old('check_out_date', $reservation->check_out_date) }}" required>
                            </div>
                        </div>

                        <!-- Status & Price -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t dark:border-gray-700">
                            <div>
                                <label for="status"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Reservation Status') }}</label>
                                <select id="status" name="status"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                    @foreach (['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $st)
                                        <option value="{{ $st }}"
                                            {{ old('status', $reservation->status) == $st ? 'selected' : '' }}>
                                            {{ ucfirst($st) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nights -->
                            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm font-semibold text-gray-400">{{ __('Nights') }}</p>
                                <p id="total_nights" class="text-xl font-bold">0</p>
                            </div>

                            <!-- Total Price -->
                            <div class="bg-indigo-100 dark:bg-indigo-700 p-3 rounded-lg">
                                <p class="text-sm font-semibold text-indigo-400">{{ __('Total Est. Price') }}</p>
                                <p id="total_amount_display"
                                    class="text-2xl font-extrabold text-indigo-900 dark:text-indigo-200">$0.00</p>

                                <input type="hidden" id="subtotal" name="subtotal"
                                    value="{{ $reservation->subtotal }}">
                                <input type="hidden" id="total_amount" name="total_amount"
                                    value="{{ $reservation->total_amount }}">
                            </div>
                        </div>

                        <!-- Payment Update -->
                        <h4 class="text-xl font-semibold border-b pb-2 mt-8">{{ __('Payment Update') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="payment_status"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Payment Status') }}</label>
                                <select id="payment_status" name="payment_status"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                    @foreach (['unpaid', 'paid', 'partially_paid', 'refunded'] as $ps)
                                        <option value="{{ $ps }}"
                                            {{ old('payment_status', $reservation->invoice->payment_status ?? 'unpaid') == $ps ? 'selected' : '' }}>
                                            {{ ucfirst($ps) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="amount_paid"
                                    class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Amount Paid Now') }}</label>
                                <input type="number" id="amount_paid" name="amount_paid" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    value="{{ old('amount_paid', $reservation->invoice->amount_paid ?? 0.0) }}">
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                                {{ __('Save Reservation Changes') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.getElementById('check_in_date');
            const checkOutInput = document.getElementById('check_out_date');
            const roomSelect = document.getElementById('room_id');
            const nightsDisplay = document.getElementById('total_nights');
            const totalAmountDisplay = document.getElementById('total_amount_display');
            const totalAmountHidden = document.getElementById('total_amount');
            const subtotalHidden = document.getElementById('subtotal');

            function calculatePrice() {
                const checkIn = moment(checkInInput.value);
                const checkOut = moment(checkOutInput.value);
                const dailyPrice = parseFloat(roomSelect.options[roomSelect.selectedIndex].dataset.price || 0);

                if (checkIn.isValid() && checkOut.isValid() && checkOut.isAfter(checkIn)) {
                    const nights = checkOut.diff(checkIn, 'days');
                    nightsDisplay.textContent = nights;

                    const subtotalValue = dailyPrice * nights;
                    totalAmountDisplay.textContent = '$' + subtotalValue.toFixed(2);
                    subtotalHidden.value = subtotalValue.toFixed(2);
                    totalAmountHidden.value = subtotalValue.toFixed(2);
                } else {
                    nightsDisplay.textContent = 0;
                    totalAmountDisplay.textContent = '$0.00';
                    subtotalHidden.value = '0.00';
                    totalAmountHidden.value = '0.00';
                }
            }

            checkInInput.min = moment().format('YYYY-MM-DD');
            checkInInput.addEventListener('change', calculatePrice);
            checkOutInput.addEventListener('change', calculatePrice);
            roomSelect.addEventListener('change', calculatePrice);

            calculatePrice();
        });
    </script>
</x-app-layout>
