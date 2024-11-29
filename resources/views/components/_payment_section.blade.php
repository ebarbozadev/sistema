<style>
    .payment-section {
        margin-top: 20px;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .form-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .table {
        margin-top: 20px;
    }

    .summary {
        margin-top: 20px;
        font-size: 1.2em;
    }

    .summary .row {
        margin-bottom: 10px;
    }

    .btn-adc {
        padding: 0;
        margin: 0;
    }

    #add-payment-btn {
        padding: 5px 10px;
    }
</style>

<div class="payment-section">
    <div class="form-group">
        <label for="payment_method">Forma de Pagamento</label>
        <select id="payment_method" class="form-control">
            <option value="dinheiro">Dinheiro</option>
            <option value="pix">Pix</option>
            <option value="cartao">Cartão</option>
        </select>
    </div>

    <div class="form-group">
        <label for="payment-amount">Valor</label>
        <input type="number" id="payment-amount" class="form-control" step="0.01" min="0">
    </div>

    <button id="add-payment-btn" class="btn btn-primary">Adicionar</button>

    <div class="form-group">
        <label for="discount-amount">Desconto/Acréscimo</label>
        <div class="form-row">
            <input type="number" id="discount-amount" class="form-control" step="0.01" min="0" placeholder="Valor">
            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" name="discount-type" value="desconto" class="form-check-input" checked> Desconto
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" name="discount-type" value="acrescimo" class="form-check-input"> Acréscimo
                </label>
            </div>
        </div>
    </div>

    <table class="payments-table table">
        <thead>
            <tr>
                <th>Tipo de Pagamento</th>
                <th>Valor Pago</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Os pagamentos registrados serão adicionados aqui dinamicamente -->
        </tbody>
    </table>
</div>

<script>
    let totalPedido = 500.00; // Exemplo: total do pedido
    let payments = [];

    function updatePaymentSummary(amountChange) {
        const totalOrder = parseFloat(document.querySelector('.summary-total').textContent.replace('R$', '').replace(',', '.')) || 0; // Total do pedido
        const totalPaidElement = document.querySelector(".summary-paid");
        const remainingElement = document.querySelector(".summary-remaining");

        // Atualizar total pago
        const currentPaid = parseFloat(totalPaidElement.textContent.replace("R$", "").replace(",", ".")) || 0;
        const newPaid = currentPaid + amountChange;

        // Atualizar restante
        const newRemaining = totalOrder - newPaid;

        // Atualizar os elementos na interface
        totalPaidElement.textContent = `R$ ${newPaid.toFixed(2).replace(".", ",")}`;
        remainingElement.textContent = `R$ ${newRemaining.toFixed(2).replace(".", ",")}`;
    }


    // Atualizar o resumo após a remoção de um pagamento
    function updatePaymentSummaryAfterRemoval(amountRemoved) {
        const remainingElement = document.querySelector(".summary-remaining");
        const remainingAmount = parseFloat(remainingElement.textContent.replace("R$", "").replace(",", ".")) || 0;

        remainingElement.textContent = `R$ ${(remainingAmount + amountRemoved).toFixed(2).replace(".", ",")}`;
    }

    document.getElementById("payment_method").addEventListener("change", function() {
        const cardDetails = document.getElementById("card-details");
        cardDetails.style.display = this.value === "cartao" ? "block" : "none";
    });


    document.getElementById("add-payment-btn").addEventListener("click", function(e) {
        e.preventDefault();

        // Capturar o método de pagamento e o valor
        const paymentMethod = document.getElementById("payment_method").value;
        const paymentAmount = parseFloat(document.getElementById("payment-amount").value) || 0;

        // Verificar se o valor é válido
        if (paymentAmount <= 0) {
            alert("Por favor, insira um valor válido.");
            return;
        }

        // Adicionar a nova linha na tabela
        const table = document.querySelector(".payments-table tbody");
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
        <td>${paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1)}</td>
        <td>R$ ${paymentAmount.toFixed(2).replace(".", ",")}</td>
        <td>
            <button class="btn btn-danger btn-sm remove-payment-btn">Excluir</button>
        </td>
    `;
        table.appendChild(newRow);

        // Adicionar evento de exclusão à nova linha
        newRow.querySelector(".remove-payment-btn").addEventListener("click", function() {
            // Atualizar o total pago e restante ao remover
            updatePaymentSummary(-paymentAmount);
            newRow.remove();
        });

        // Atualizar o total pago e restante
        updatePaymentSummary(paymentAmount);

        // Limpar o campo de valor
        document.getElementById("payment-amount").value = "";
    });

    document.getElementById('add-payment-btn').addEventListener('click', function() {
        const paymentMethod = document.getElementById('payment-method').value;
        const paymentAmount = parseFloat(document.getElementById('payment-amount').value);

        if (!paymentAmount || paymentAmount <= 0) {
            alert('Insira um valor válido.');
            return;
        }

        // Adiciona o pagamento ao array
        payments.push({
            method: paymentMethod,
            amount: paymentAmount
        });

        // Atualiza a tabela
        updatePaymentTable();

        // Limpa o campo de valor
        document.getElementById('payment-amount').value = '';
    });

    // Atualizar a tabela de pagamentos na interface
    function updatePaymentTable(method, amount) {
        const table = document.querySelector(".payments-table tbody");
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
        <td>${method.charAt(0).toUpperCase() + method.slice(1)}</td>
        <td>R$ ${amount.toFixed(2).replace(".", ",")}</td>
        <td>
            <button class="btn btn-danger btn-sm remove-payment-btn">Excluir</button>
        </td>
    `;
        table.appendChild(newRow);

        // Adicionar evento para remoção do pagamento
        newRow.querySelector(".remove-payment-btn").addEventListener("click", function() {
            newRow.remove();
            // Atualizar o resumo após a remoção
            updatePaymentSummaryAfterRemoval(amount);
        });
    }

    function removePayment(index) {
        payments.splice(index, 1);
        updatePaymentTable();
    }

    function updateTotals() {
        const subtotal = parseFloat(document.querySelector('.summary-subtotal').textContent.replace('R$', '').replace(',', '.')) || 0;
        const totalPaid = parseFloat(document.querySelector('.summary-paid').textContent.replace('R$', '').replace(',', '.')) || 0;

        // Captura o valor do desconto ou acréscimo
        const adjustmentAmount = parseFloat(document.getElementById('discount-amount').value) || 0;
        const adjustmentType = document.querySelector('input[name="discount-type"]:checked').value;

        // Calcula o total ajustado
        let adjustedTotal = subtotal;
        if (adjustmentType === 'desconto') {
            adjustedTotal -= adjustmentAmount;
        } else if (adjustmentType === 'acrescimo') {
            adjustedTotal += adjustmentAmount;
        }

        // Evita valores negativos
        adjustedTotal = Math.max(0, adjustedTotal);

        // Calcula o restante
        const remaining = adjustedTotal - totalPaid;

        // Atualiza os elementos na interface
        document.querySelector('.summary-discount').textContent = `R$ ${adjustmentAmount.toFixed(2).replace('.', ',')}`;
        document.querySelector('.summary-total').textContent = `R$ ${adjustedTotal.toFixed(2).replace('.', ',')}`;
        document.querySelector('.summary-remaining').textContent = `R$ ${Math.max(0, remaining).toFixed(2).replace('.', ',')}`;
    }

    // Eventos para atualizar os valores dinamicamente
    document.getElementById('discount-amount').addEventListener('input', updateTotals);
    document.querySelectorAll('input[name="discount-type"]').forEach(radio => {
        radio.addEventListener('change', updateTotals);
    });
</script>