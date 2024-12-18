@extends('voyager::master')

@section('content')
<div id="frenteDeCaixa" class="container">
    <h1>Venda</h1>

    {{-- Formulário para Adicionar Produto --}}
    @include('components._add_product_form')

    {{-- Tabela de Produtos --}}
    @include('components._product_table', ['products' => $products])

    {{-- Resumo do Pagamento --}}

    {{-- Formulário de Pagamento --}}

    @include('components.infoVenda')

    @include('components._payment_section')

    @include('components._payment_summary', ['summary' => $summary])
    {{-- Ações no Rodapé --}}
    @include('components._footer_actions')
</div>
@endsection

<style>
    #frenteDeCaixa .pdvDados {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }
</style>

<script>
    function finalizeSale() {
        // Coletar o ID do cliente selecionado
        const selectedClientInput = document.getElementById('selected-client');
        const cliente_id = selectedClientInput.dataset.clientId;

        if (!cliente_id) {
            alert('Por favor, selecione um cliente antes de finalizar a venda.');
            return;
        }

        // Coletar os pagamentos registrados
        const payments = [];
        const paymentRows = document.querySelectorAll('.payments-table tbody tr');
        paymentRows.forEach(row => {
            const method = row.cells[0].textContent.trim();
            const amount = parseFloat(row.cells[1].textContent.replace('R$', '').replace(/\./g, '').replace(',', '.'));
            payments.push({
                method: method,
                amount: amount
            });
        });

        if (payments.length === 0) {
            alert('Por favor, adicione pelo menos um pagamento antes de finalizar a venda.');
            return;
        }

        // Coletar o valor de desconto ou acréscimo
        const discountAmount = parseFloat(document.getElementById('discount-amount').value.replace(',', '.')) || 0;
        const discountType = document.querySelector('input[name="discount-type"]:checked').value;

        // Preparar os dados para enviar ao servidor
        const data = {
            cliente_id: cliente_id,
            payments: payments,
            discountAmount: discountAmount,
            discountType: discountType,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        fetch('/admin/finalize-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Venda finalizada com sucesso!');
                    window.location.href = '/admin/pedido-de-venda';
                } else {
                    alert('Erro ao finalizar venda: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao finalizar venda.');
            });
    }
</script>