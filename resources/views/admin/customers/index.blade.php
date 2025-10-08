<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Customer Management') }}
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Header and Add Button -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold">{{ __('Customer List') }}</h3>
                        <a href="{{ route('admin.customers.create') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                            {{ __('Add New Customer') }}
                        </a>
                    </div>

                    <!-- Customers Table -->
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">{{ __('Full Name') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Email') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Phone') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Passport ID') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row"
                                            class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $customer->first_name . ' ' . $customer->last_name }}
                                        </th>
                                        <td class="py-4 px-6">{{ $customer->email }}</td>
                                        <td class="py-4 px-6">{{ $customer->phone_number ?? 'N/A' }}</td>
                                        <td class="py-4 px-6">{{ $customer->passport_id }}</td>
                                        <td class="py-4 px-6 flex space-x-2">
                                            <!-- View Button -->
                                            <a href="{{ route('admin.customers.show', $customer->id) }}"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-300 font-medium">
                                                {{ __('VIEW') }}
                                            </a>

                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                                class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-500 dark:hover:text-yellow-300 font-medium">
                                                {{ __('EDIT') }}
                                            </a>

                                            <!-- Delete Form -->
                                            <form method="POST"
                                                action="{{ route('admin.customers.destroy', $customer->id) }}"
                                                onsubmit="return confirm('{{ __('Are you sure you want to delete this customer?') }}');">
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
                        {{ $customers->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
