<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Room Management') }}
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
                        <h3 class="text-2xl font-bold">{{ __('Room List') }}</h3>
                        <a href="{{ route('rooms.create') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                            {{ __('Add New Room') }}
                        </a>
                    </div>

                    <!-- Filter by Status -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('rooms.index') }}" class="flex space-x-4 items-center">
                            <label for="status" class="font-semibold">{{ __('Filter by Status:') }}</label>
                            <select name="status" id="status"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700"
                                onchange="this.form.submit()">
                                <option value="">{{ __('All') }}</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>
                                    {{ __('Available') }}</option>
                                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>
                                    {{ __('Occupied') }}</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>
                                    {{ __('Maintenance') }}</option>
                            </select>
                        </form>
                    </div>


                    <!-- Rooms Table -->
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">{{ __('Room Number') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Type') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Capacity') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Base Price (Daily)') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Status') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Actions') }}</th>
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
                                        <td class="py-4 px-6">{{ $room->status }}</td>
                                        <td class="py-4 px-6 flex space-x-2">
                                            <!-- View Button -->
                                            <a href="{{ route('rooms.show', $room->id) }}"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-300 font-medium">
                                                {{ __('VIEW') }}
                                            </a>

                                            <!-- Edit Button -->
                                            <a href="{{ route('rooms.edit', $room->id) }}"
                                                class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-500 dark:hover:text-yellow-300 font-medium">
                                                {{ __('EDIT') }}
                                            </a>

                                            <!-- Delete Form -->
                                            <form method="POST" action="{{ route('rooms.destroy', $room->id) }}"
                                                onsubmit="return confirm('{{ __('Are you sure you want to delete this room?') }}');">
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
                        {{ $rooms->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
