<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Available Rooms for Booking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-3xl font-bold text-indigo-500 mb-6 border-b pb-3">{{ __('Start Your Reservation') }}</h3>

                <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                    @if ($rooms->isEmpty())
                        <div class="p-4 text-center text-xl text-gray-500 dark:text-gray-400">
                            {{ __('No rooms are currently available for booking.') }}
                        </div>
                    @else
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">{{ __('Room Number') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Type') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Capacity') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Price (Daily)') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rooms as $room)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row"
                                            class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $room->room_number }}
                                        </th>
                                        <td class="py-4 px-6">{{ $room->type }}</td>
                                        <td class="py-4 px-6">{{ $room->capacity }}</td>
                                        <td class="py-4 px-6">${{ number_format($room->base_price, 2) }}</td>
                                        <td class="py-4 px-6">
                                            <!-- Guest Booking Link -->
                                            <a href="{{ route('reservations.create-guest', ['room_id' => $room->id]) }}"
                                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-1.5 px-3 rounded-lg shadow-md transition duration-300">
                                                {{ __('Book Now') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
