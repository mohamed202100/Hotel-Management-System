<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Header and Back Button -->
                    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                        <h3 class="text-3xl font-extrabold text-indigo-500">{{ __('Editing Role for') }}:
                            {{ $user->name }}</h3>
                        <a href="{{ route('users.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                            {{ __('Back to Users') }}
                        </a>
                    </div>

                    <p class="mb-6 text-lg">
                        {{ __('Current Role') }}:
                        <span
                            class="font-bold @if ($userRole === 'admin') text-red-500 @else text-gray-500 @endif">
                            {{ ucfirst($userRole) }}
                        </span>
                    </p>

                    <!-- Form for Role Update -->
                    <form method="POST" action="{{ route('users.update', $user->id) }}" class="mt-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="role" :value="__('Assign New Role')" />

                            <select id="role" name="role"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>

                                @foreach ($roles as $roleName => $roleLabel)
                                    <option value="{{ $roleName }}"
                                        {{ old('role', $userRole) == $roleName ? 'selected' : '' }}>
                                        {{ ucfirst($roleLabel) }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error class="mt-2" :messages="$errors->get('role')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Role') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
