<style>
    tbody tr td, thead tr th {
        font-size: .8rem;
    }

    tbody tr td {
        vertical-align: middle !important;
    }

    .table .btn {
        padding: 5px 10px;
    }    
</style>

<table class="table">
    <thead>
        <tr>
            <th style="width: 40px;">Ref.</th>
            <th>Nome</th>
            <th style="width: 80px;">Qtd.</th>
            <th>Vl. Unit.</th>
            <th>Total</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $index => $product)
        <tr>
            <td>{{ $product['code'] ?? 'N/A' }}</td>
            <td>{{ $product['nome'] ?? 'N/A' }}</td>
            <td>
                <input
                    type="number"
                    class="form-control form-control-sm update-quantity"
                    value="{{ $product['quantity'] }}"
                    data-index="{{ $loop->index }}"
                    data-unit-price="{{ $product['unit_price'] }}"
                    min="1"
                    step="1">
            </td>
            <td>{{ isset($product['unit_price']) ? number_format($product['unit_price'], 2, ',', '.') : 'N/A' }}</td>
            <td>{{ isset($product['total_price']) ? number_format($product['total_price'], 2, ',', '.') : 'N/A' }}</td>
            <td style="text-align: center;">
                <form action="{{ route('remove_product', $index) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const removeButtons = document.querySelectorAll('form[action*="remove-product"] button');

        removeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Tem certeza que deseja remover este produto?')) {
                    e.preventDefault(); // Impede o envio do formulário
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const quantityInputs = document.querySelectorAll('.update-quantity');
        let debounceTimeout;

        quantityInputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(debounceTimeout); // Limpa o timeout anterior

                debounceTimeout = setTimeout(() => {
                    const index = this.dataset.index;
                    const quantity = this.value;

                    if (quantity && quantity > 0) {
                        fetch(`/admin/update-product/${index}`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    quantity: quantity
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Atualizar o total na linha da tabela
                                    const totalCell = this.closest('tr').querySelector('td:nth-child(5)');
                                    totalCell.textContent = new Intl.NumberFormat('pt-BR', {
                                        style: 'currency',
                                        currency: 'BRL'
                                    }).format(data.product.total_price);

                                    // Atualizar o resumo
                                    document.querySelector('.summary-items').textContent = data.summary.items;
                                    document.querySelector('.summary-subtotal').textContent =
                                        new Intl.NumberFormat('pt-BR', {
                                            style: 'currency',
                                            currency: 'BRL'
                                        }).format(data.summary.subtotal);
                                    document.querySelector('.summary-total').textContent =
                                        new Intl.NumberFormat('pt-BR', {
                                            style: 'currency',
                                            currency: 'BRL'
                                        }).format(data.summary.total);
                                } else {
                                    console.error('Erro ao atualizar o produto:', data);
                                }
                            })
                            .catch(error => console.error('Erro ao processar a requisição:', error));
                    }
                }, 500); // Debounce de 500ms
            });
        });
    });
</script>