<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes da Categoria') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <!-- Botão Retornar para categorias -->
                    <div class="flex items-center mb-4 justify-between">
                        <h3 class="font-semibold text-lg text-red-600 mb-4 gap-4">{{ $category->nmcategory }} - {{ $category->idcategory }}</h3>
                        <a href="{{ route('dashboard') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <span class="material-icons mr-2">
                                arrow_back
                            </span>
                            <span>Categorias</span>
                        </a>
                    </div>
                    <div class="text-gray-600">
                        <div class="flex items-center">
                            <span class="material-icons mr-2">
                                {{ App\Helpers\IconHelper::getIconNameFromFgLogo($category->fglogo) }}
                            </span>
                            <span>Informações detalhadas</span>
                        </div>

                        <!-- Início dos botões de filtragem -->
                        <div class="mt-4">
                            <!-- Botão Todos -->
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded mr-2 mb-2" onclick="filterAttribute('all')">
                                Todos
                            </button>

                            @foreach ($attributes as $index => $attribute)
                            @if ($index == 0 || $attributes[$index - 1]->nmattribute != $attribute->nmattribute)
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded mr-2 mb-2" onclick="filterAttribute('{{ $attribute->nmattribute }}')">
                                {{ $attribute->nmattribute }}
                            </button>
                            @endif
                            @endforeach
                        </div>
                        <!-- Fim dos botões de filtragem -->

                        <!-- Campo de pesquisa -->
                        <div class="mt-4 mb-4">
                            <input type="text" id="search" placeholder="Pesquisar documentos..." class="w-full py-2 px-4 rounded border border-gray-300 focus:border-red-300 focus:outline-none">
                        </div>

                        <!-- Início do acordeão -->
                        <div class="mt-4" id="accordion">
                            @foreach ($attributes as $index => $attribute)
                            @if ($index == 0 || $attributes[$index - 1]->nmattribute != $attribute->nmattribute)
                            <div class="border-b border-gray-200 mb-4 attribute-group" data-nmattribute="{{ $attribute->nmattribute }}">
                                <button class="w-full py-1 flex items-center justify-between">
                                    <span><strong>{{ $attribute->nmlabel }}</strong></span>
                                    <span class="material-icons">
                                        chevron_right
                                    </span>
                                </button>
                                <div class="hidden">
                                    @foreach ($attributes as $doc)
                                    @if ($doc->nmattribute == $attribute->nmattribute)
                                    <div class="my-4">
                                        <p><strong>Número do documento:</strong> {{ $doc->numero_doc }}</p>
                                        <p><strong>Nome do documento:</strong> {{ $doc->nmuserupd }}</p>
                                        <p><strong>Código de Revisão:</strong> {{ $doc->cdrevision }}</p>
                                        <div class="bg-white rounded-lg p-4 px-6">
                                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center" onclick="loadPDF()">
                                                <span class="material-icons mr-2">
                                                    visibility
                                                </span>
                                                <span>Visualizar Documento</span>
                                            </button>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        <!-- Fim do acordeão -->
                    </div>
                </div>
                <div id="pdf-container" class="bg-white rounded-lg shadow-lg p-4 max-w-4xl">
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/pdfobject.js') }}"></script>
    <script>
        var options = {
            fallbackLink: "Seu navegador não suporta PDFs. Clique aqui para baixar o arquivo.",
            height: "896px"
        };

        function loadPDF() {
            let path = "{{ $attribute->path }}";
            if (!/^https?:\/\//i.test(path)) {
                path = "{{ asset($attribute->path) }}";
            }
            PDFObject.embed(path, "#pdf-container", options);
        }
        //Botões de filtragem
        function filterAttribute(attribute) {
            const accordionItems = document.querySelectorAll('#accordion .border-b');

            accordionItems.forEach(item => {
                const itemAttribute = item.getAttribute('data-nmattribute');

                if (attribute === 'all' || itemAttribute === attribute) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Acordeão e pesquisa
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search');
            const accordion = document.getElementById('accordion');
            const attributeGroups = Array.from(accordion.getElementsByClassName('attribute-group'));

            // Acordeão
            document.querySelectorAll('.border-b button').forEach(button => {
                button.addEventListener('click', () => {
                    const content = button.nextElementSibling;

                    content.style.display = (content.style.display === 'none' || content.style.display === '') ? 'block' : 'none';

                    const icon = button.querySelector('.material-icons');
                    icon.style.transform = icon.style.transform === 'rotate(90deg)' ? '' : 'rotate(90deg)';
                });
            });

            // Filtro de pesquisa
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.toLowerCase();

                attributeGroups.forEach(group => {
                    const content = group.querySelector('div');
                    const pElements = content.querySelectorAll('p');
                    let hasMatchingDocument = false;

                    for (let i = 0; i < pElements.length; i += 3) {
                        const numeroDoc = pElements[i].textContent.split(':').pop().trim();
                        const nmusrupd = pElements[i + 1].textContent.split(':').pop().trim();
                        const cdrevision = pElements[i + 2].textContent.split(':').pop().trim();

                        const match = [numeroDoc, nmusrupd, cdrevision].some(value => value.toLowerCase().includes(query));

                        if (match) {
                            hasMatchingDocument = true;
                            pElements[i].style.display = 'block';
                            pElements[i + 1].style.display = 'block';
                            pElements[i + 2].style.display = 'block';
                            if (i + 3 < pElements.length) {
                                pElements[i + 3].style.display = 'block';
                            }
                        } else {
                            pElements[i].style.display = 'none';
                            pElements[i + 1].style.display = 'none';
                            pElements[i + 2].style.display = 'none';
                            if (i + 3 < pElements.length) {
                                pElements[i + 3].style.display = 'none';
                            }
                        }
                    }

                    if (hasMatchingDocument) {
                        group.style.display = 'block';
                    } else {
                        group.style.display = 'none';
                    }
                });
            });
        });
    </script>


</x-app-layout>
