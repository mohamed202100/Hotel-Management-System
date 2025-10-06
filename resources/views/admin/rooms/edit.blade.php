<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('EDIT ROOM') }}: {{ $room->room_number }}
            </h2>
            <a href="{{ route('rooms.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                {{ __('BACK TO ROOMS LIST') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <strong class="font-bold">{{ __('INPUT ERROR!') }}</strong>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Display Success Message -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('rooms.update', $room) }}">
                    @csrf
                    @method('PUT')

                    <!-- Room Number -->
                    <div class="mb-4">
                        <x-input-label for="room_number" :value="__('ROOM NUMBER')" />
                        <!-- Use the room object value, falling back to old() on error -->
                        <x-text-input id="room_number" class="block mt-1 w-full" type="text" name="room_number"
                            :value="old('room_number', $room->room_number)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('room_number')" />
                    </div>

                    <!-- Room Type -->
                    <div class="mb-4">
                        <x-input-label for="type" :value="__('ROOM TYPE (e.g., Single, Double)')" />
                        <x-text-input id="type" class="block mt-1 w-full" type="text" name="type"
                            :value="old('type', $room->type)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>

                    <!-- Capacity -->
                    <div class="mb-4">
                        <x-input-label for="capacity" :value="__('CAPACITY (Number of People)')" />
                        <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity"
                            :value="old('capacity', $room->capacity)" required min="1" />
                        <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <x-input-label for="price" :value="__('DAILY PRICE (SAR)')" />
                        <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01"
                            name="price" :value="old('price', $room->price)" required min="0" />
                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                    </div>

                    <!-- Is Available Status -->
                    <div class="mb-6">
                        <x-input-label for="is_available" :value="__('AVAILABILITY STATUS')" />
                        <select id="is_available" name="is_available"
                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required>
                            <option value="1"
                                {{ old('is_available', $room->is_available) == 1 ? 'selected' : '' }}>
                                {{ __('AVAILABLE') }}</option>
                            <option value="0"
                                {{ old('is_available', $room->is_available) == 0 ? 'selected' : '' }}>
                                {{ __('NOT AVAILABLE') }}</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('is_available')" />
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button class="ms-4 bg-indigo-600 hover:bg-indigo-700">
                            {{ __('UPDATE ROOM') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
