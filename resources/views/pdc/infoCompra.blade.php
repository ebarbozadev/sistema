<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Pedido de Compra</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .suggestions-box {
            position: absolute;
            z-index: 1000;
            width: 100%;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .suggestion-item {
            display: block;
            padding: 8px;
            text-decoration: none;
            color: #333;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <form id="pedido-compra-form" action="{{ route('pdc.finalize_purchase') }}" method="POST">
        @csrf
        <!-- Outros campos -->

        <div class="form-group" style="position: relative">
            <label for="fornecedor-descricao">Fornecedor</label>
            <input type="text" id="fornecedor-descricao" name="fornecedor_descricao" class="form-control" autocomplete="off">
            <div id="fornecedor-suggestions" class="suggestions-box"></div>
            <input type="hidden" id="id_fornecedor" name="id_fornecedor">
        </div>

        <!-- Outros campos e botões -->
        <button type="submit" class="btn btn-primary">Finalizar Compra</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fornecedorInput = document.getElementById('fornecedor-descricao');
            const fornecedorSuggestions = document.getElementById('fornecedor-suggestions');
            const idFornecedorInput = document.getElementById('id_fornecedor');

            fornecedorInput.addEventListener('input', function() {
                const query = this.value;

                if (query.length >= 2) {
                    fetch(`/admin/pdc/search-suppliers?query=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erro na resposta da rede');
                            }
                            return response.json();
                        })
                        .then(suppliers => {
                            fornecedorSuggestions.innerHTML = '';

                            if (suppliers.length > 0) {
                                fornecedorSuggestions.style.display = 'block';

                                suppliers.forEach(supplier => {
                                    const suggestionItem = document.createElement('a');
                                    suggestionItem.textContent = supplier.nome;
                                    suggestionItem.dataset.id = supplier.id;
                                    suggestionItem.classList.add('suggestion-item');

                                    suggestionItem.addEventListener('click', function(e) {
                                        e.preventDefault();

                                        fornecedorInput.value = supplier.nome;
                                        idFornecedorInput.value = supplier.id;
                                        fornecedorSuggestions.style.display = 'none';
                                    });

                                    fornecedorSuggestions.appendChild(suggestionItem);
                                });
                            } else {
                                fornecedorSuggestions.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao buscar fornecedores:', error);
                            fornecedorSuggestions.style.display = 'none';
                        });
                } else {
                    fornecedorSuggestions.style.display = 'none';
                }
            });

            // Esconder sugestões ao clicar fora
            document.addEventListener('click', function(e) {
                if (!fornecedorInput.contains(e.target) && !fornecedorSuggestions.contains(e.target)) {
                    fornecedorSuggestions.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>