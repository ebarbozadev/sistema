@extends('voyager::master')

@section('content')
<div id="frenteDeCaixa" class="container">
    <h1>Compra</h1>

    {{-- Formulário para Adicionar Produto --}}
    @include('pdc._add_product_form')

    {{-- Tabela de Produtos --}}
    @include('pdc._product_table', ['products' => $products])

    {{-- Resumo do Pagamento --}}

    {{-- Formulário de Pagamento --}}

    @include('pdc.infoCompra')

    @include('pdc._payment_section')

    @include('pdc._payment_summary', ['summary' => $summary])
    {{-- Ações no Rodapé --}}
    @include('pdc._footer_actions')
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
            alert('Por favor, selecione um cliente antes de finalizar a compra.');
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

        fetch('/admin/c/finalize-sale', {
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
                    alert('Compra finalizada com sucesso!');
                    window.location.href = '/admin/c/pedido-de-compra';
                } else {
                    alert('Erro ao finalizar compra: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao finalizar compra.');
            });
    }
</script>