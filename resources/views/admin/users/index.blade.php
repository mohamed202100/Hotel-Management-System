<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Role Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Header and Add Button -->
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-3xl font-bold text-indigo-500">{{ __('System Users') }}</h3>
                        <!-- NEW BUTTON: Add New User -->
                        <a href="{{ route('users.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-300">
                            {{ __('Add New User') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($users->isEmpty())
                        <div class="p-4 text-center text-xl text-gray-500 dark:text-gray-400">
                            {{ __('No other users found in the system.') }}
                        </div>
                    @else
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">{{ __('ID') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Name') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Email') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Current Role') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="py-4 px-6">{{ $user->id }}</td>
                                            <td class="py-4 px-6">{{ $user->name }}</td>
                                            <td class="py-4 px-6">{{ $user->email }}</td>
                                            <td class="py-4 px-6">
                                                @php
                                                    $role = $user->roles->pluck('name')->first() ?? 'None';
                                                    $color = $role === 'admin' ? 'bg-red-500' : 'bg-gray-500';
                                                @endphp
                                                <span
                                                    class="inline-block px-3 py-1 text-xs font-semibold text-white rounded-full {{ $color }}">
                                                    {{ ucfirst($role) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <a href="{{ route('users.edit', $user->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                    {{ __('Edit Role') }}
                                                </a>
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
    </div>
</x-app-layout>
