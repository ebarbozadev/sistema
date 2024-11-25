<div class="payment-summary">
    <p><strong>Itens:</strong> <span class="summary-items">{{ $summary['items'] }}</span></p>
    <p><strong>Subtotal:</strong> R$ <span class="summary-subtotal">{{ number_format($summary['subtotal'], 2, ',', '.') }}</span></p>
    <p><strong>Desconto:</strong> R$ {{ number_format($summary['discount'], 2, ',', '.') }}</p>
    <p><strong>Total:</strong> R$ <span class="summary-total">{{ number_format($summary['total'], 2, ',', '.') }}</span></p>
</div>