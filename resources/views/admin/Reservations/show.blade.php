<?php
use Carbon\Carbon;
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reservation Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Header with Actions -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                        <h3 class="text-3xl font-extrabold text-indigo-500">
                            {{ __('Reservation') }} #{{ $reservation->id }}
                        </h3>
                        <div class="flex space-x-3">
                            @if ($reservation->status === 'confirmed')
                                <a href="{{ route('reservations.invoice', $reservation->id) }}" target="_blank"
                                    class="btn bg-blue-500 hover:bg-blue-600">
                                    {{ __('View/Print Invoice') }}
                                </a>
                            @endif

                            <a href="{{ route('reservations.edit', $reservation->id) }}"
                                class="btn bg-yellow-500 hover:bg-yellow-600">
                                {{ __('Edit Reservation') }}
                            </a>

                            <a href="{{ route('reservations.index') }}" class="btn bg-gray-500 hover:bg-gray-600">
                                {{ __('Back to List') }}
                            </a>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500',
                            'confirmed' => 'bg-indigo-600',
                            'checked_in' => 'bg-green-600',
                            'checked_out' => 'bg-gray-500',
                            'cancelled' => 'bg-red-600',
                        ];
                        $statusColor = $statusColors[$reservation->status] ?? 'bg-gray-500';
                    @endphp
                    <div class="mb-8">
                        <span
                            class="inline-block px-4 py-2 text-lg font-bold text-white {{ $statusColor }} rounded-lg shadow-md">
                            {{ __('Status') }}: {{ __(ucfirst(str_replace('_', ' ', $reservation->status))) }}
                        </span>
                    </div>

                    <!-- Reservation Dates & Nights -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 border p-4 rounded-lg dark:border-gray-700">
                        <div>
                            <p class="text-gray-400 font-semibold">{{ __('Check-in Date') }}</p>
                            <p class="text-lg font-bold text-indigo-400">
                                {{ Carbon::parse($reservation->check_in_date)->format('F d, Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400 font-semibold">{{ __('Check-out Date') }}</p>
                            <p class="text-lg font-bold text-indigo-400">
                                {{ Carbon::parse($reservation->check_out_date)->format('F d, Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400 font-semibold">{{ __('Total Nights') }}</p>
                            <p class="text-lg font-bold">
                                {{ Carbon::parse($reservation->check_in_date)->diffInDays(Carbon::parse($reservation->check_out_date)) }}
                            </p>
                        </div>
                    </div>

                    <!-- Customer, Room, Billing -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Customer Info -->
                        <div class="card-info">
                            <h4 class="card-title">{{ __('Customer Information') }}</h4>
                            <p><strong>{{ __('Name') }}:</strong> {{ $reservation->customer->first_name ?? 'N/A' }}
                                {{ $reservation->customer->last_name ?? '' }}</p>
                            <p><strong>{{ __('Email') }}:</strong> {{ $reservation->customer->email ?? 'N/A' }}</p>
                            <p><strong>{{ __('Phone') }}:</strong>
                                {{ $reservation->customer->phone_number ?? 'N/A' }}</p>
                            <p><strong>{{ __('Passport/ID') }}:</strong>
                                {{ $reservation->customer->passport_id ?? 'N/A' }}</p>
                        </div>

                        <!-- Room Info -->
                        <div class="card-info">
                            <h4 class="card-title">{{ __('Room Information') }}</h4>
                            <p><strong>{{ __('Room Number') }}:</strong>
                                {{ $reservation->room->room_number ?? 'N/A' }}</p>
                            <p><strong>{{ __('Type') }}:</strong> {{ $reservation->room->type ?? 'N/A' }}</p>
                            <p><strong>{{ __('Capacity') }}:</strong> {{ $reservation->room->capacity ?? 'N/A' }}
                                {{ __('People') }}</p>
                            <p><strong>{{ __('Base Price') }}:</strong>
                                ${{ number_format($reservation->room->base_price ?? 0, 2) }} {{ __('per night') }}</p>
                        </div>

                        <!-- Billing & Payment -->
                        <div class="card-info">
                            <h4 class="card-title">{{ __('Billing & Payment') }}</h4>
                            <p><strong>{{ __('Subtotal') }}:</strong> ${{ number_format($reservation->subtotal, 2) }}
                            </p>
                            <p><strong>{{ __('Total Amount') }}:</strong>
                                ${{ number_format($reservation->total_amount, 2) }}</p>

                            @if ($reservation->invoice)
                                @php
                                    $paymentColors = [
                                        'paid' => 'bg-green-600',
                                        'unpaid' => 'bg-yellow-600',
                                        'partially_paid' => 'bg-yellow-600',
                                        'refunded' => 'bg-red-600',
                                    ];
                                    $paymentColor =
                                        $paymentColors[$reservation->invoice->payment_status] ?? 'bg-gray-500';
                                @endphp
                                <p class="mt-3"><strong>{{ __('Payment Status') }}:</strong>
                                    <span
                                        class="inline-block px-3 py-1 text-white font-semibold rounded-full {{ $paymentColor }}">
                                        {{ __(ucfirst(str_replace('_', ' ', $reservation->invoice->payment_status))) }}
                                    </span>
                                </p>
                                <p><strong>{{ __('Amount Paid') }}:</strong>
                                    ${{ number_format($reservation->invoice->amount_paid, 2) }}</p>
                                <p><strong>{{ __('Amount Due') }}:</strong>
                                    ${{ number_format($reservation->invoice->amount_due - $reservation->invoice->amount_paid, 2) }}
                                </p>
                            @else
                                <p class="text-red-500">{{ __('Invoice not yet generated.') }}</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .btn {
            @apply text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md;
        }

        .card-info {
            @apply bg-gray-100 dark:bg-gray-700 p-5 rounded-lg shadow-inner;
        }

        .card-title {
            @apply text-xl font-bold mb-3 border-b pb-2 text-indigo-400;
        }
    </style>
</x-app-layout>
