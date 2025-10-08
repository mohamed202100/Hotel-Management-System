<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Room') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Success Message Alert -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Header and Back Button -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                        <h3 class="text-3xl font-extrabold text-indigo-500">{{ __('Create New Room') }}</h3>
                        <a href="{{ route('admin.rooms.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                            {{ __('Back to List') }}
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.rooms.store') }}" class="mt-6 space-y-6">
                        @csrf

                        <!-- Room Number -->
                        <div>
                            <x-input-label for="room_number" :value="__('Room Number')" />
                            <x-text-input id="room_number" name="room_number" type="text" class="mt-1 block w-full"
                                :value="old('room_number')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('room_number')" />
                        </div>

                        <!-- Type -->
                        <div>
                            <x-input-label for="type" :value="__('Room Type')" />
                            <select id="type" name="type"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>
                                <option value="Single" {{ old('type') == 'Single' ? 'selected' : '' }}>
                                    {{ __('Single') }}</option>
                                <option value="Double" {{ old('type') == 'Double' ? 'selected' : '' }}>
                                    {{ __('Double') }}</option>
                                <option value="Triple" {{ old('type') == 'Triple' ? 'selected' : '' }}>
                                    {{ __('Triple') }}</option>
                                <option value="Family" {{ old('type') == 'Family' ? 'selected' : '' }}>
                                    {{ __('Family') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <!-- Capacity (READONLY) -->
                        <div>
                            <x-input-label for="capacity" :value="__('Capacity (People)')" />
                            <x-text-input id="capacity" readonly name="capacity" type="number" min="1"
                                class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed"
                                :value="old('capacity')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                        </div>

                        <!-- Base Price -->
                        <div>
                            <x-input-label for="base_price" :value="__('Base Price (Daily)')" />
                            <x-text-input id="base_price" name="base_price" type="number" step="0.01" min="0"
                                class="mt-1 block w-full" :value="old('base_price')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('base_price')" />
                        </div>

                        <!-- Status (Using string values, Default to Available) -->
                        <div>
                            <x-input-label for="status" :value="__('Room Status')" />
                            <select id="status" name="status"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>
                                @php
                                    // Define the allowed statuses
                                    $roomStatuses = ['available', 'occupied', 'cleaning', 'maintenance'];
                                    // Default status for a new room is 'available'
                                    $currentStatus = old('status', 'available');
                                @endphp

                                @foreach ($roomStatuses as $status)
                                    <option value="{{ $status }}"
                                        {{ $currentStatus == $status ? 'selected' : '' }}>
                                        {{ __(ucfirst($status)) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Room') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Map room types to their default capacity
    const capacityMap = {
        'Single': 1,
        'Double': 2,
        'Triple': 3,
        'Family': 5
    };

    const roomTypeSelect = document.getElementById('type');
    const capacityInput = document.getElementById('capacity');

    function updateCapacity() {
        const selectedType = roomTypeSelect.value;
        const defaultCapacity = capacityMap[selectedType] || 1;

        // Update the capacity field
        capacityInput.value = defaultCapacity;
    }

    // Attach event listener to update capacity when the type changes
    roomTypeSelect.addEventListener('change', updateCapacity);

    // Initial run to set capacity based on the default selected value
    updateCapacity();
</script>
