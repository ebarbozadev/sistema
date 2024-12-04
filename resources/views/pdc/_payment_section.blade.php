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
            <option value="cartao">Cartão Antecipado</option>
        </select>
    </div>

    <div class="form-group">
        <label for="payment-amount">Valor</label>
        <input type="number" id="payment-amount" class="form-control" step="0.01" min="0" placeholder="Digite o valor">
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
    // Atualizar o resumo de pagamentos dinamicamente
    function updateSummaryWithDiscountOrAddition() {
        const totalElement = document.querySelector(".summary-total");
        const totalPaidElement = document.querySelector(".summary-paid");
        const remainingElement = document.querySelector(".summary-remaining");
        const discountInput = document.getElementById("discount-amount");
        const discountType = document.querySelector('input[name="discount-type"]:checked').value;

        // Valores base
        const subtotal = parseFloat(document.querySelector(".summary-subtotal").textContent.replace("R$", "").replace(/\./g, "").replace(",", ".")) || 0;
        const totalPago = parseFloat(totalPaidElement.textContent.replace("R$", "").replace(/\./g, "").replace(",", ".")) || 0;

        // Valor do desconto ou acréscimo
        const adjustment = parseFloat(discountInput.value.replace(",", ".")) || 0;
        let totalAdjusted = subtotal;

        if (discountType === "desconto") {
            totalAdjusted = subtotal - adjustment;
        } else if (discountType === "acrescimo") {
            totalAdjusted = subtotal + adjustment;
        }

        // Evitar valores negativos no total ajustado
        totalAdjusted = Math.max(totalAdjusted, 0);

        // Recalcular restante
        const remaining = totalAdjusted - totalPago;

        // Atualizar valores na interface
        totalElement.textContent = `R$ ${totalAdjusted.toFixed(2).replace(".", ",")}`;
        remainingElement.textContent = `R$ ${Math.max(0, remaining).toFixed(2).replace(".", ",")}`;
    }

    // Eventos para atualizar os valores dinamicamente
    document.getElementById("discount-amount").addEventListener("input", updateSummaryWithDiscountOrAddition);
    document.querySelectorAll('input[name="discount-type"]').forEach(radio => {
        radio.addEventListener("change", updateSummaryWithDiscountOrAddition);
    });

    // Atualizar o resumo geral ao adicionar pagamentos
    function updateSummary(amountChange) {
        const totalElement = document.querySelector(".summary-total");
        const totalPaidElement = document.querySelector(".summary-paid");
        const remainingElement = document.querySelector(".summary-remaining");

        const totalPedido = parseFloat(totalElement.textContent.replace("R$", "").replace(/\./g, "").replace(",", ".")) || 0;
        const totalPago = parseFloat(totalPaidElement.textContent.replace("R$", "").replace(/\./g, "").replace(",", ".")) || 0;

        const newTotalPago = totalPago + amountChange;
        const newRemaining = totalPedido - newTotalPago;

        totalPaidElement.textContent = `R$ ${newTotalPago.toFixed(2).replace(".", ",")}`;
        remainingElement.textContent = `R$ ${Math.max(0, newRemaining).toFixed(2).replace(".", ",")}`;
    }

    document.getElementById("add-payment-btn").addEventListener("click", function(e) {
        e.preventDefault();

        const paymentMethod = document.getElementById("payment_method").value;
        const paymentAmount = parseFloat(document.getElementById("payment-amount").value.replace(",", ".")) || 0;

        if (!paymentAmount || paymentAmount <= 0) {
            alert("Por favor, insira um valor válido.");
            return;
        }

        const totalElement = document.querySelector(".summary-total");
        const totalPaidElement = document.querySelector(".summary-paid");
        const remainingElement = document.querySelector(".summary-remaining");

        const totalPedido = parseFloat(totalElement.textContent.replace("R$", "").replace(/\./g, "").replace(",", ".")) || 0;
        const totalPago = parseFloat(totalPaidElement.textContent.replace("R$", "").replace(/\./g, "").replace(",", ".")) || 0;

        if ((totalPago + paymentAmount) > totalPedido) {
            alert("O valor total pago não pode exceder o valor total da compra.");
            return;
        }

        const table = document.querySelector(".payments-table tbody");
        let existingRow = Array.from(table.rows).find(
            (row) => row.cells[0].textContent.trim().toLowerCase() === paymentMethod.toLowerCase()
        );

        if (existingRow) {
            const currentValue = parseFloat(existingRow.cells[1].textContent.replace("R$", "").replace(/\./g, "").replace(",", ".")) || 0;
            const newValue = currentValue + paymentAmount;
            existingRow.cells[1].textContent = `R$ ${newValue.toFixed(2).replace(".", ",")}`;
        } else {
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td>${paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1)}</td>
                <td>R$ ${paymentAmount.toFixed(2).replace(".", ",")}</td>
                <td>
                    <button class="btn btn-danger btn-sm remove-payment-btn">Excluir</button>
                </td>
            `;
            table.appendChild(newRow);

            newRow.querySelector(".remove-payment-btn").addEventListener("click", function() {
                const valueToRemove = parseFloat(newRow.cells[1].textContent.replace("R$", "").replace(/\./g, "").replace(",", ".")) || 0;
                newRow.remove();
                updateSummary(-valueToRemove);
            });
        }

        updateSummary(paymentAmount);
        document.getElementById("payment-amount").value = "";
    });
</script>