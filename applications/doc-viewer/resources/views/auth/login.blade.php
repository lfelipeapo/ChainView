<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-red-50">
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-10 md:p-20 max-w-md w-full space-y-8">
            <div class="flex justify-center">
                <img class="h-12 w-auto object-contain" loading="lazy" src="{{ asset('uploads/images/logo-om30-1.png') }}" alt="Logo">
            </div>
            <h2 class="text-center font-semibold text-2xl text-red-600">Login</h2>
            <form method="POST" action="{{ route('login') }}" class="space-y-6 w-full">
                @csrf
                <div>
                    <label for="username" class="block font-medium text-gray-700">Username</label>
                    <input id="username" type="username" name="username" value="{{ old('username') }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="password" class="block font-medium text-gray-700">Senha</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Lembrar de mim
                        </label>
                    </div>

                    <div class="text-sm">
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-medium text-red-600 hover:text-red-500">
                            Esqueceu a senha?
                        </a>
                        @endif
                    </div>
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center bg-red-600 border border-transparent rounded-md py-2 px-4 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Entrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
