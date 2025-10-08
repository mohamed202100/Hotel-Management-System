<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Reservations') }}
        </h2>
    </x-slot>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-3xl font-bold text-indigo-500">{{ __('Your Current Bookings') }}</h3>
                    <a href="{{ route('rooms.available') }}"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                        {{ __('Book A New Room') }}
                    </a>
                </div>

                @if ($reservations->isEmpty())
                    <div
                        class="p-4 bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-300">
                        {{ __('You do not have any active reservations yet. Click "Book A New Room" to get started.') }}
                    </div>
                @else
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">{{ __('Reservation ID') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Room') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Check-in') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Check-out') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Total Amount') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Status') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservations as $reservation)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row"
                                            class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            #{{ $reservation->id }}
                                        </th>
                                        <td class="py-4 px-6">{{ $reservation->room->room_number ?? 'N/A' }}
                                            ({{ $reservation->room->type ?? 'N/A' }})
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('M d, Y') }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('M d, Y') }}
                                        </td>
                                        <td class="py-4 px-6">${{ number_format($reservation->total_amount, 2) }}</td>
                                        <td class="py-4 px-6">
                                            @php
                                                $status = $reservation->status;
                                                $color =
                                                    [
                                                        'pending' => 'bg-yellow-500',
                                                        'confirmed' => 'bg-indigo-600',
                                                        'checked_in' => 'bg-green-600',
                                                        'checked_out' => 'bg-gray-500',
                                                        'cancelled' => 'bg-red-600',
                                                    ][$status] ?? 'bg-gray-500';
                                            @endphp
                                            <span
                                                class="inline-block px-3 py-1 text-sm font-semibold text-white rounded-full {{ $color }}">
                                                {{ __(ucfirst(str_replace('_', ' ', $status))) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 space-x-2 flex">
                                            @if (in_array($reservation->status, ['pending', 'confirmed']))
                                                <form method="POST"
                                                    action="{{ route('guest.reservations.cancel', $reservation->id) }}"
                                                    onsubmit="return confirm('{{ __('Are you sure you want to cancel this reservation?') }}');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded-lg text-sm transition duration-300">
                                                        {{ __('Cancel') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
