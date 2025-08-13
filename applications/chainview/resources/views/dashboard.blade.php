<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Categorias') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($categories as $category)
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <a href="{{ url('/categories/' . $category->id) }}" class="text-gray-600">
                        <h3 class="font-semibold text-lg text-red-600 mb-4">{{ $category->nmcategory }}</h3>
                        <div class="flex items-center">
                            <span class="material-icons mr-2">
                                {{ App\Helpers\IconHelper::getIconNameFromFgLogo($category->fglogo) }}
                            </span>
                            <span>Visualizar detalhes</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
