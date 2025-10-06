<?php
use Carbon\Carbon;
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Invoice Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div id="invoice-print-area"
                class="bg-white dark:bg-gray-800 p-8 shadow-2xl sm:rounded-lg text-gray-900 dark:text-gray-100">

                <!-- Header & Print Button -->
                <div class="flex justify-between items-center mb-10 border-b border-gray-300 pb-4 print:hidden">
                    <h1 class="text-3xl font-extrabold text-indigo-600">{{ __('Invoice') }}
                        #{{ $reservation->invoice->id ?? 'N/A' }}</h1>
                    <button onclick="window.print()"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                        {{ __('Print Invoice') }}
                    </button>
                </div>

                <div class="text-lg font-bold mb-8 print:block">
                    <!-- Invoice Header (Visible on Print) -->
                    <h1 class="text-3xl text-indigo-600 mb-2 print:text-black">HOTEL MANAGEMENT SYSTEM</h1>
                    <p class="text-sm print:text-black">{{ __('Invoice Date') }}: {{ Carbon::now()->format('F d, Y') }}
                    </p>
                </div>

                <!-- Reservation and Customer Info -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h2 class="text-xl font-bold mb-2 border-b pb-1 text-gray-500">{{ __('Billed To') }}</h2>
                        <p class="font-semibold">{{ $reservation->customer->first_name ?? 'N/A' }}
                            {{ $reservation->customer->last_name ?? '' }}</p>
                        <p class="text-sm">{{ $reservation->customer->email ?? 'N/A' }}</p>
                        <p class="text-sm">{{ $reservation->customer->phone_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mb-2 border-b pb-1 text-gray-500">{{ __('Reservation Details') }}
                        </h2>
                        <p class="font-semibold">{{ __('Reservation ID') }}: #{{ $reservation->id }}</p>
                        <p class="text-sm">{{ __('Room No') }}: {{ $reservation->room->room_number ?? 'N/A' }}
                            ({{ $reservation->room->type ?? 'N/A' }})</p>
                        <p class="text-sm">{{ __('Check-in') }}:
                            {{ Carbon::parse($reservation->check_in_date)->format('M d, Y') }}</p>
                        <p class="text-sm">{{ __('Check-out') }}:
                            {{ Carbon::parse($reservation->check_out_date)->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <h2 class="text-xl font-bold mb-2 border-b pb-1 text-gray-500">{{ __('Charges Summary') }}</h2>
                <div class="overflow-x-auto relative sm:rounded-lg mb-8">
                    @php
                        $checkIn = Carbon::parse($reservation->check_in_date);
                        $checkOut = Carbon::parse($reservation->check_out_date);
                        $nights = $checkOut->diffInDays($checkIn);
                        $basePrice = $reservation->room->base_price ?? 0;
                        $subtotalAmount = $basePrice * $nights;
                        $taxRate = $reservation->invoice->tax_rate ?? 0.0;
                        $taxAmount = $subtotalAmount * $taxRate;
                    @endphp

                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-3 px-6">{{ __('Description') }}</th>
                                <th scope="col" class="py-3 px-6 text-right">{{ __('Quantity') }}</th>
                                <th scope="col" class="py-3 px-6 text-right">{{ __('Unit Price') }}</th>
                                <th scope="col" class="py-3 px-6 text-right">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="py-4 px-6">{{ $reservation->room->type ?? 'Room Rental' }}
                                    ({{ $reservation->room->room_number ?? 'N/A' }})</td>
                                <td class="py-4 px-6 text-right">{{ $nights }} {{ __('Nights') }}</td>
                                <td class="py-4 px-6 text-right">${{ number_format($basePrice, 2) }}</td>
                                <td class="py-4 px-6 text-right">${{ number_format($subtotalAmount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals and Payment Summary -->
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 lg:w-1/3 space-y-2">
                        <div class="flex justify-between border-b pb-1">
                            <span>{{ __('Subtotal') }}:</span>
                            <span class="font-semibold">${{ number_format($subtotalAmount, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <span>{{ __('Tax') }} ({{ $taxRate * 100 }}%):</span>
                            <span class="font-semibold">${{ number_format($taxAmount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-indigo-600 pt-2">
                            <span>{{ __('TOTAL AMOUNT') }}:</span>
                            <span>${{ number_format($reservation->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-400">
                            <span>{{ __('Amount Paid') }}:</span>
                            <span>${{ number_format($reservation->invoice->amount_paid ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-extrabold pt-2 text-red-600">
                            <span>{{ __('BALANCE DUE') }}:</span>
                            <span>${{ number_format($reservation->total_amount - ($reservation->invoice->amount_paid ?? 0), 2) }}</span>
                        </div>
                        <div class="mt-4 text-center">
                            @php
                                $paymentStatus = $reservation->invoice->payment_status ?? 'unpaid';
                                $color =
                                    $paymentStatus === 'paid'
                                        ? 'bg-green-600'
                                        : ($paymentStatus === 'unpaid'
                                            ? 'bg-red-600'
                                            : 'bg-yellow-600');
                            @endphp
                            <span
                                class="font-bold text-lg inline-block px-4 py-2 rounded-full text-white {{ $color }}">
                                {{ __('Status') }}: {{ __(ucfirst(str_replace('_', ' ', $paymentStatus))) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-10 text-center text-sm text-gray-500 print:hidden">
                    <p>{{ __('Thank you for choosing our hotel.') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<style>
    @media print {

        /* Hide everything not in the print area */
        body>*:not(#invoice-print-area) {
            display: none;
        }

        /* Make the print area full width */
        #invoice-print-area {
            max-width: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Unhide only the print content */
        .print\:hidden {
            display: none !important;
        }

        .print\:block {
            display: block !important;
        }

        /* Ensure readable text color on print */
        .print\:text-black {
            color: #000 !important;
        }

        .print\:text-black h1 {
            color: #000 !important;
        }

    }
</style>
