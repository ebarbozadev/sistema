<style>
    #product-suggestions {
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

    #product-suggestions a {
        display: block;
        padding: 8px;
        text-decoration: none;
        color: #333;
        cursor: pointer;
    }

    #product-suggestions a:hover {
        background-color: #f8f9fa;
    }

    .dados-produto {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    #btn-add {
        display: flex;
        flex-direction: column;
        justify-content: end;
    }

    #btn-add button {
        margin: 0 !important;
        height: 36px;
    }
</style>

<form id="add-product-form" method="POST" action="{{ route('add_product') }}">
    @csrf
    <div class="form-group" style="position: relative">
        <label for="descricao">Descrição</label>
        <input type="text" id="descricao" name="descricao" class="form-control" autocomplete="off">
        <div id="product-suggestions"></div>
    </div>
    <div class="dados-produto">
        <div class="form-group">
            <label for="code">Código</label>
            <input type="text" id="code" name="code" class="form-control">
        </div>
        <div class="form-group">
            <label for="quantity">Quantidade</label>
            <input type="number" id="quantity" name="quantity" class="form-control" step="1" min="1" required>
        </div>
        <div class="form-group">
            <label for="unit_price">Valor Unitário</label>
            <input type="number" id="unit_price" name="unit_price" class="form-control" step="0.01" readonly>
        </div>
        <div class="form-group">
            <label for="total_price">Total</label>
            <input type="number" id="total_price" name="total_price" class="form-control" step="0.01" readonly>
        </div>
        <div id="btn-add" class="form-group">
            <button style="display: block;" type="button" id="add-product-btn" class="btn btn-primary">Inserir</button>
        </div>

    </div>
</form>

<script>
    const descricaoInput = document.getElementById('descricao');
    const codeInput = document.getElementById('code');
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const totalPriceInput = document.getElementById('total_price');
    const suggestionsBox = document.getElementById('product-suggestions');

    // Atualizar o total automaticamente
    function updateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        totalPriceInput.value = (quantity * unitPrice).toFixed(2);
    }

    quantityInput.addEventListener('input', updateTotal);

    // Buscar produto pelo código ao perder o foco ou pressionar Enter
    codeInput.addEventListener('blur', fetchProductByCode);
    codeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchProductByCode();
        }
    });

    document.getElementById('add-product-btn').addEventListener('click', function(e) {
        e.preventDefault();

        const code = codeInput.value.trim();
        const quantity = parseInt(quantityInput.value.trim()) || 1; // Padrão para 1
        const unitPrice = parseFloat(unitPriceInput.value.trim()) || 0;

        if (!code || quantity <= 0 || unitPrice <= 0) {
            alert('Por favor, preencha corretamente o código, quantidade e valor unitário.');
            return;
        }

        // Verifica se o produto já está listado na tabela
        const existingRow = Array.from(document.querySelectorAll('table tbody tr')).find(
            (row) => row.querySelector('td:first-child').textContent === code
        );

        if (existingRow) {
            // Incrementa a quantidade do produto existente
            const quantityInputField = existingRow.querySelector('input.update-quantity');
            const currentQuantity = parseInt(quantityInputField.value) || 0;
            const newQuantity = currentQuantity + quantity;
            quantityInputField.value = newQuantity;

            // Atualiza o total
            const totalCell = existingRow.querySelector('td:nth-child(5)');
            const newTotal = newQuantity * unitPrice;
            totalCell.textContent = new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
            }).format(newTotal);

            // Atualiza o backend
            fetch(`/admin/pdc/update-product/${existingRow.querySelector('input.update-quantity').dataset.index}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        quantity: newQuantity,
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Atualiza o resumo
                        document.querySelector('.summary-items').textContent = data.summary.items;
                        document.querySelector('.summary-subtotal').textContent = new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL',
                        }).format(data.summary.subtotal);
                        document.querySelector('.summary-total').textContent = new Intl.NumberFormat('pt-BR', {
                            style: 'currency',
                            currency: 'BRL',
                        }).format(data.summary.total);
                    } else {
                        console.error('Erro ao atualizar o produto:', data);
                    }
                })
                .catch((error) => console.error('Erro ao processar a requisição:', error));
        } else {
            // Adiciona um novo produto à tabela
            fetch('/admin/pdc/add-product', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        code: code,
                        quantity: quantity,
                        unit_price: unitPrice,
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Adiciona a linha na tabela
                        const tableBody = document.querySelector('table tbody');
                        const newRow = tableBody.insertRow();
                        newRow.innerHTML = `
                        <td>${data.product.code}</td>
                        <td>${data.product.nome}</td>
                        <td>
                            <input
                                type="number"
                                class="form-control form-control-sm update-quantity"
                                value="${data.product.quantity}"
                                data-index="${tableBody.rows.length - 1}"
                                data-unit-price="${data.product.unit_price}"
                                min="1"
                                step="1">
                        </td>
                        <td>${new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(data.product.unit_price)}</td>
                        <td>${new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(data.product.total_price)}</td>
                        <td style="text-align: center;">
                            <form action="/admin/pdc/remove-product/${data.product.code}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                            </form>
                        </td>
                    `;
                    } else {
                        alert(data.message || 'Erro ao adicionar o produto.');
                    }
                })
                .catch((error) => console.error('Erro ao adicionar produto:', error));
        }

        // Limpa os campos do formulário
        codeInput.value = '';
        descricaoInput.value = '';
        unitPriceInput.value = '';
        quantityInput.value = '1';
        totalPriceInput.value = '';
    });

    // Atualiza a tabela e o resumo
    function updateTable(product, summary) {
        const tableBody = document.querySelector('table tbody');

        // Verifica se o produto já existe na tabela
        let existingRow = Array.from(tableBody.rows).find(row => row.cells[0].textContent == product.code);

        if (existingRow) {
            // Atualiza a linha existente
            existingRow.cells[2].querySelector('input').value = product.quantity;
            existingRow.cells[4].textContent = product.total_price.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        } else {
            // Adiciona uma nova linha para o produto
            const newRow = tableBody.insertRow();
            newRow.innerHTML = `
            <td>${product.code}</td>
            <td>${product.nome}</td>
            <td><input type="number" class="form-control form-control-sm update-quantity" value="${product.quantity}" min="1" step="1" data-index="${product.index}" /></td>
            <td>${product.unit_price.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</td>
            <td>${product.total_price.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</td>
            <td>
                <form action="/admin/pdc/remove-product/${product.index}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                </form>
            </td>
        `;
        }

        // Atualiza o resumo
        document.querySelector('.summary-items').textContent = summary.items;
        document.querySelector('.summary-subtotal').textContent = summary.subtotal.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        document.querySelector('.summary-total').textContent = summary.total.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function fetchProductByCode() {
        const code = codeInput.value.trim(); // Obtém o código do produto   

        if (code) {
            fetch(`/admin/pdc/search-product-by-code/${code}`)
                .then(response => response.json())
                .then(product => {
                    if (product && product.id) {
                        // Preenche os campos com os dados retornados da API
                        descricaoInput.value = product.nome;
                        unitPriceInput.value = parseFloat(product.preco_venda).toFixed(2);

                        // Atualiza o valor total (inicialmente considerando quantidade = 1)
                        const quantity = parseFloat(quantityInput.value) || 1;
                        totalPriceInput.value = (quantity * product.preco_venda).toFixed(2);

                        // Adiciona evento para recalcular o total ao mudar a quantidade
                        quantityInput.addEventListener('input', () => {
                            const updatedQuantity = parseFloat(quantityInput.value) || 1;
                            totalPriceInput.value = (updatedQuantity * product.preco_venda).toFixed(2);
                        });
                        quantityInput.value = 1; // Define quantidade como 1quantityInput.value = 1; // Define quantidade como 1
                    } else {
                        alert('Produto não encontrado. Verifique o código.');
                        // Limpa os campos em caso de erro
                        clearFields();
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar produto:', error);
                    alert('Erro ao buscar o produto. Tente novamente.');
                });
        }
    }

    function clearFields() {
        descricaoInput.value = '';
        unitPriceInput.value = '';
        totalPriceInput.value = '';
        quantityInput.value = 1; // Define o valor padrão como 1
    }

    // Buscar produtos com base na descrição
    descricaoInput.addEventListener('input', function() {
        const query = this.value;

        if (query.length >= 2) {
            fetch(`/admin/pdc/search-products?query=${query}`)
                .then((response) => response.json())
                .then((products) => {
                    suggestionsBox.innerHTML = ''; // Limpa as sugestões anteriores

                    if (products.length > 0) {
                        suggestionsBox.style.display = 'block'; // Exibe o box de sugestões

                        products.forEach((product) => {
                            const suggestionItem = document.createElement('a');
                            suggestionItem.textContent = product.nome;
                            suggestionItem.dataset.id = product.id;
                            suggestionItem.dataset.price = product.preco_venda; // Altere para `preco_venda`
                            suggestionItem.classList.add('dropdown-item');

                            suggestionItem.addEventListener('click', function(e) {
                                e.preventDefault();

                                // Preenche os campos com os dados selecionados
                                descricaoInput.value = product.nome;
                                codeInput.value = product.id;
                                unitPriceInput.value = parseFloat(product.preco_venda).toFixed(2); // Garante o formato correto
                                quantityInput.value = 1; // Define quantidade como 1
                                totalPriceInput.value = parseFloat(product.preco_venda).toFixed(2); // Total igual ao unitário para 1 unidade

                                // Esconde as sugestões
                                suggestionsBox.style.display = 'none';
                            });

                            suggestionsBox.appendChild(suggestionItem);
                        });
                    } else {
                        suggestionsBox.style.display = 'none'; // Esconde se não houver resultados
                    }
                })
                .catch((error) => console.error('Erro ao buscar produtos:', error));
        } else {
            suggestionsBox.style.display = 'none'; // Esconde o box se menos de 2 caracteres
        }
    });

    // Esconde as sugestões se clicar fora do campo
    document.addEventListener('click', function(e) {
        if (!descricaoInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.style.display = 'none';
        }
    });
</script>