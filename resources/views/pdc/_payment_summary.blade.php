<div class="payment-summary" style="margin: 30px 0 20px 0">
    <span><strong>Itens:</strong> <span class="summary-items">{{ $summary['items'] }}</span> - </span>
    <span><strong>Subtotal:</strong> R$ <span class="summary-subtotal">{{ number_format($summary['subtotal'], 2, ',', '.') }}</span> - </span>
    <span><strong>Desconto:</strong> R$ <span class="summary-discount">0,00</span> - </span>
    <span><strong>Total:</strong> R$ <span class="summary-total">{{ number_format($summary['total'], 2, ',', '.') }}</span> - </span>
    <span><strong>Total Pago:</strong> R$ <span class="summary-paid">0,00</span> - </span>
    <span><strong>Restante:</strong> R$ <span class="summary-remaining">{{ number_format($summary['total'], 2, ',', '.') }}</span></span>
</div>