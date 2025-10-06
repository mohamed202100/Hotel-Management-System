<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reservation Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Success Message Alert -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Header and Add Button -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold">{{ __('Reservation List') }}</h3>
                        <a href="{{ route('reservations.create') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                            {{ __('Add New Reservation') }}
                        </a>
                    </div>

                    <!-- Reservations Table -->
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        @if ($reservations->isEmpty())
                            <p class="p-4 text-center text-gray-500">
                                {{ __('No reservations found. Please add a new reservation.') }}</p>
                        @else
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">{{ __('ID') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Room') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Customer') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Check-in') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Check-out') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Total') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Status') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservations as $reservation)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td
                                                class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $reservation->id }}
                                            </td>
                                            <td class="py-4 px-6">{{ $reservation->room->room_number ?? 'N/A' }}
                                                ({{ $reservation->room->type ?? 'N/A' }})
                                            </td>
                                            <td class="py-4 px-6">{{ $reservation->customer->first_name ?? 'N/A' }}
                                                {{ $reservation->customer->last_name ?? 'N/A' }}</td>
                                            <td class="py-4 px-6">
                                                {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('Y-m-d') }}
                                            </td>
                                            <td class="py-4 px-6">
                                                {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('Y-m-d') }}
                                            </td>
                                            <td class="py-4 px-6">${{ number_format($reservation->total_amount, 2) }}
                                            </td>
                                            <td class="py-4 px-6">
                                                @php
                                                    $status = $reservation->status;
                                                    $color = 'bg-gray-500';
                                                @endphp
                                                <span
                                                    class="inline-block px-3 py-1 text-xs font-semibold text-white {{ $color }} rounded-full">
                                                    {{ __(ucfirst(str_replace('_', ' ', $status))) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 flex space-x-2">
                                                <!-- View Button -->
                                                <a href="{{ route('reservations.show', $reservation->id) }}"
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-300 font-medium">
                                                    {{ __('VIEW') }}
                                                </a>

                                                <!-- Edit Button -->
                                                <a href="{{ route('reservations.edit', $reservation->id) }}"
                                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-500 dark:hover:text-yellow-300 font-medium">
                                                    {{ __('EDIT') }}
                                                </a>

                                                <!-- Delete Form -->
                                                <form method="POST"
                                                    action="{{ route('reservations.destroy', $reservation->id) }}"
                                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this reservation?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-300 font-medium">
                                                        {{ __('DELETE') }}
                                                    </button>
                                                </form>
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
    </div>
</x-app-layout>
