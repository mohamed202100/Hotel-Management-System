<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Customer Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                        <h3 class="text-3xl font-extrabold text-indigo-500">
                            {{ $customer->first_name . ' ' . $customer->last_name }}</h3>
                        <div class="space-x-2 flex">
                            <!-- Edit Button -->
                            <a href="{{ route('customers.edit', $customer->id) }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                                {{ __('Edit Customer') }}
                            </a>
                            <!-- Back Button -->
                            <a href="{{ route('customers.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                                {{ __('Back to List') }}
                            </a>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">{{ __('First Name') }}</dt>
                            <dd class="mt-1 text-xl font-semibold">{{ $customer->first_name }}</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">{{ __('Last Name') }}</dt>
                            <dd class="mt-1 text-xl font-semibold">{{ $customer->last_name }}</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">{{ __('Email Address') }}</dt>
                            <dd class="mt-1 text-xl font-semibold">{{ $customer->email }}</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">{{ __('Phone Number') }}</dt>
                            <dd class="mt-1 text-xl font-semibold">{{ $customer->phone_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-400">{{ __('Passport ID / National ID') }}</dt>
                            <dd class="mt-1 text-xl font-semibold">{{ $customer->passport_id }}</dd>
                        </div>

                        <!-- Optional: Creation/Update Timestamps -->
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">{{ __('Date Created') }}</dt>
                            <dd class="mt-1 text-sm">{{ $customer->created_at->format('M d, Y H:i A') }}</dd>
                        </div>
                        <div class="col-span-1">
                            <dt class="text-sm font-medium text-gray-400">{{ __('Last Updated') }}</dt>
                            <dd class="mt-1 text-sm">{{ $customer->updated_at->format('M d, Y H:i A') }}</dd>
                        </div>
                    </dl>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
