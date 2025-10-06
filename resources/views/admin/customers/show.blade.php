<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Customer Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 lg:p-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ $customer->first_name . ' ' . $customer->last_name }}</h3>

                    <div class="space-y-4 text-gray-700 dark:text-gray-300">
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                            <span class="font-semibold">{{ __('Email Address') }}:</span>
                            <span>{{ $customer->email }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                            <span class="font-semibold">{{ __('Phone Number') }}:</span>
                            <span>{{ $customer->phone_number ?? __('Not provided') }}</span>
                        </div>
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-2">
                            <span class="font-semibold block mb-1">{{ __('Address') }}:</span>
                            <p class="pl-4 italic">{{ $customer->address ?? __('No address available') }}</p>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2 text-sm">
                            <span class="font-semibold">{{ __('Member Since') }}:</span>
                            <span>{{ $customer->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 space-x-3">
                        <a href="{{ route('customers.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back to List') }}
                        </a>
                        <a href="{{ route('customers.edit', $customer) }}"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit Customer') }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
