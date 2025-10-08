<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Room Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Header and Action Buttons -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                        <h3 class="text-3xl font-extrabold text-indigo-500">{{ __('Room Number') }}:
                            {{ $room->room_number }}</h3>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.rooms.edit', $room->id) }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                                {{ __('Edit Room') }}
                            </a>
                            <a href="{{ route('admin.rooms.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                                {{ __('Back to List') }}
                            </a>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Room Number -->
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                    {{ __('Room Number') }}</dt>
                                <dd
                                    class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                    {{ $room->room_number }}</dd>
                            </div>

                            <!-- Type -->
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                    {{ __('Type') }}</dt>
                                <dd
                                    class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                    {{ $room->type }}</dd>
                            </div>

                            <!-- Capacity -->
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                    {{ __('Capacity') }}</dt>
                                <dd
                                    class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                    {{ $room->capacity }} {{ __('People') }}</dd>
                            </div>

                            <!-- Base Price -->
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                    {{ __('Base Price (Daily)') }}</dt>
                                <dd
                                    class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                    ${{ number_format($room->base_price, 2) }}</dd>
                            </div>

                            <!-- Status -->
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                    {{ __('Status') }}</dt>
                                <dd class="mt-1 text-sm leading-6 sm:col-span-2 sm:mt-0">
                                    {{ $room->status }}</dd>
                                </dd>
                            </div>

                            <!-- Timestamps (Optional) -->
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                    {{ __('Created At') }}</dt>
                                <dd
                                    class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                    {{ $room->created_at->format('Y-m-d H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
