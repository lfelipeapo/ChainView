<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-red-50">
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-10 md:p-20 max-w-md w-full space-y-8 my-8">
            <div class="flex justify-center">
                <img class="h-12 w-auto object-contain" loading="lazy" src="{{ asset('uploads/images/logo-om30-1.png') }}" alt="Logo">
            </div>
            <h2 class="text-center font-semibold text-2xl text-red-600">Registro</h2>
            <form method="POST" action="{{ route('register') }}" class="space-y-6 w-full">
                @csrf

                <div>
                    <x-label for="username" :value="__('Username')" />
                    <x-input id="username" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50" type="text" name="username" :value="old('username')" required autofocus />
                </div>

                <div class="mt-4">
                    <x-label for="first_name" :value="__('First Name')" />
                    <x-input id="first_name" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50" type="text" name="first_name" :value="old('first_name')" required />
                </div>

                <div class="mt-4">
                    <x-label for="last_name" :value="__('Last Name')" />
                    <x-input id="last_name" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50" type="text" name="last_name" :value="old('last_name')" required />
                </div>

                <div class="mt-4">
                    <x-label for="email" :value="__('Email')" />
                    <x-input id="email" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50" type="email" name="email" :value="old('email')" required />
                </div>

                <div class="mt-4">
                    <x-label for="password" :value="__('Password')" />
                    <x-input id="password" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50" type="password" name="password" required autocomplete="new-password" />
                </div>

                <div class="mt-4">
                    <x-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-input id="password_confirmation" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50" type="password" name="password_confirmation" required />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                        {{ __('Login') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
