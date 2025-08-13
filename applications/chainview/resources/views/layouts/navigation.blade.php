<div x-data="{ isOpen: false }" class="flex justify-between items-center p-4 bg-red-800 lg:p-8">
    <!-- Logo and App name -->
    <div class="flex items-center">
        <div class="shrink-0 flex items-center">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-10 w-auto fill-current text-black" />
            </a>
        </div>
        <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
            <a href="/" class="flex-shrink-0 flex items-center">
                <span class="text-white font-semibold text-xl ml-2">{{ config('app.name', 'DocViewer') }}</span>
            </a>
        </div>
    </div>

    <!-- Mobile menu button -->
    <button @click="isOpen = !isOpen" type="submit" class="inline-flex items-center justify-center w-12 h-12 text-white bg-red-700 rounded-full hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 lg:hidden">
        <span class="sr-only">Open main menu</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Desktop menu items -->
    <div class="hidden lg:flex items-center justify-center space-x-4">
        <a href="{{ route('dashboard') }}" class="text-base text-white hover:bg-red-700 px-3 py-2 rounded-md font-medium">Categorias</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-base text-white hover:bg-red-700 px-3 py-2 rounded-md font-medium">Sair</button>
        </form>
    </div>

    <!-- Mobile menu -->
    <div class="fixed left-0 top-0 w-1/4 h-full bg-white z-50 transform transition-all duration-300 ease-in-out" :class="{ 'translate-x-0': isOpen, '-translate-x-full': !isOpen }">
        <div class="flex flex-col h-full justify-start space-y-6 p-5">
            <a href="{{ route('dashboard') }}" class="text-base text-black hover:bg-gray-100 hover:text-red-700 px-3 py-2 rounded-md font-medium">Categorias</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-base text-black hover:bg-gray-100 hover:text-red-700 px-3 py-2 rounded-md font-medium">Sair</button>
            </form>
        </div>
    </div>
</div>
