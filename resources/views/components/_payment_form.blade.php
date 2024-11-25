<form id="payment-form" method="POST" action="{{ route('process_payment') }}">
    @csrf
    <div class="form-group">
        <label for="payment_method">Forma de Pagamento</label>
        <select id="payment_method" name="payment_method" class="form-control" required>
            <option value="dinheiro">Dinheiro</option>
            <option value="pix">Pix</option>
            <option value="cartao">Cartão</option>
        </select>
    </div>
    <div class="form-group">
        <label for="amount_paid">Valor Pago</label>
        <input type="number" id="amount_paid" name="amount_paid" class="form-control" step="0.01" required>
    </div>
    <button type="submit" class="btn btn-success">Adicionar Pagamento</button>
</form>