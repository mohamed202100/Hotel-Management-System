<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('ROOM DETAILS') . ': ' . $room->room_number }}
            </h2>
            <a href="{{ route('rooms.edit', $room) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                {{ __('EDIT ROOM') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">

                <div class="space-y-6 text-gray-700 dark:text-gray-300">
                    <!-- Room Number -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="font-semibold text-lg">{{ __('ROOM NUMBER') }}:</span>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $room->room_number }}</span>
                    </div>

                    <!-- Type -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="font-semibold">{{ __('TYPE') }}:</span>
                        <span>{{ $room->type }}</span>
                    </div>

                    <!-- Capacity -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="font-semibold">{{ __('CAPACITY') }}:</span>
                        <span>{{ $room->capacity }} {{ __('People') }}</span>
                    </div>

                    <!-- Price -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="font-semibold">{{ __('PRICE (PER NIGHT)') }}:</span>
                        <span
                            class="font-medium text-lg text-indigo-600 dark:text-indigo-400">{{ number_format($room->price, 2) }}</span>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="font-semibold">{{ __('STATUS') }}:</span>
                        @if ($room->is_available)
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                {{ __('Available') }}
                            </span>
                        @else
                            <span
                                class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                {{ __('Occupied') }}
                            </span>
                        @endif
                    </div>

                    <!-- Created At -->
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 pt-4">
                        <span>{{ __('Date Added') }}:</span>
                        <span>{{ $room->created_at->format('M d, Y H:i A') }}</span>
                    </div>

                </div>

                <!-- Back Button -->
                <div class="mt-8 text-center">
                    <a href="{{ route('rooms.index') }}"
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                        {{ __('Back to Rooms List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
