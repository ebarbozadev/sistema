<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContasPagarController;
use App\Http\Controllers\EmployeesController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PedidoDeVendaController;

use App\Http\Controllers\PedidoDeCompraController;
use App\Http\Controllers\PDCController;

use App\Http\Controllers\CaixaController;
use App\Http\Controllers\ContasReceberController;
use App\Http\Controllers\FornecedoreController;
use App\Http\Controllers\PDVController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use TCG\Voyager\Http\Controllers\VoyagerMediaController;

Route::get('/', [MainController::class, 'index']);
Route::post('/products/media/remove', [VoyagerMediaController::class, 'remove'])->name('voyager.products.media.remove');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::get('/teste', [MainController::class, 'teste']);
    Route::get('/search-product-by-code/{code}', [ProductController::class, 'searchProductByCode']);
    Route::post('voyager/products/media/remove', [VoyagerMediaController::class, 'remove'])->name('voyager.products.media.remove');
    // Route::get('/search-products', [ProductController::class, 'searchProducts'])->name('admin.search-products');
    Route::get('/search-products', [ProductController::class, 'searchProducts']);


    // Venda
    Route::get('/pedido-de-venda', [PedidoDeVendaController::class, 'index'])->name('pdv.index');
    Route::delete('/remove-product/{index}', [PedidoDeVendaController::class, 'removeProduct'])->name('remove_product');
    Route::patch('/update-product/{index}', [PedidoDeVendaController::class, 'updateProduct'])->name('update_product');
    Route::post('/add-product', [PedidoDeVendaController::class, 'addProduct'])->name('add_product');
    Route::post('/process-payment', [PedidoDeVendaController::class, 'processPayment'])->name('process_payment');

    Route::post('/add-product', [PDVController::class, 'addProduct'])->name('add_product');
    Route::post('/finalize-sale', [PDVController::class, 'finalizeSale'])->name('finalize_sale');


    Route::group(['prefix' => '/c'], function () {
        Route::get('/pedido-de-compra', [PedidoDeCompraController::class, 'index'])->name('pdc.index');
        Route::post('/add-product', [PedidoDeCompraController::class, 'addProduct'])->name('pdc.add_product');
        Route::patch('/update-product/{index}', [PedidoDeCompraController::class, 'updateProduct'])->name('pdc.update_product');
        Route::delete('/remove-product/{index}', [PedidoDeCompraController::class, 'removeProduct'])->name('pdc.remove_product');
        Route::post('/finalize-purchase', [PedidoDeCompraController::class, 'finalizePurchase'])->name('pdc.finalize_purchase');

        // Rotas para buscar fornecedores
        Route::get('/search-suppliers', [FornecedoreController::class, 'searchSuppliers'])->name('pdc.search_suppliers');
    });



    Route::group(['prefix' => '/pagar-contas'], function () {
        Route::get('/', [ContasPagarController::class, 'index'])->name('voyager.pagar-contas.index');
        Route::get('/create', [ContasPagarController::class, 'create'])->name('voyager.pagar-contas.create');
        Route::post('/', [ContasPagarController::class, 'store'])->name('voyager.pagar-contas.store');
        Route::get('/{id}/edit', [ContasPagarController::class, 'edit'])->name('voyager.pagar-contas.edit');
        Route::put('/{id}', [ContasPagarController::class, 'update'])->name('voyager.pagar-contas.update');
        Route::delete('/{id}', [ContasPagarController::class, 'destroy'])->name('voyager.pagar-contas.destroy');
    });

    Route::get('/receber-contas', [ContasReceberController::class, 'index'])->name('voyager.receber-contas.index');
    Route::get('/receber-contas/{id}/edit', [ContasReceberController::class, 'edit'])->name('voyager.receber-contas.edit');
    Route::delete('/receber-contas/{id}', [ContasReceberController::class, 'destroy'])->name('voyager.receber-contas.destroy');
    Route::post('/receber-contas', [ContasReceberController::class, 'store'])->name('voyager.receber-contas.store');
    Route::put('/receber-contas/{id}', [ContasReceberController::class, 'update'])->name('voyager.receber-contas.update');

    Route::get('/caixa', [CaixaController::class, 'index'])->name('caixa.index');
    Route::post('/caixa/abrir', [CaixaController::class, 'abrirCaixa'])->name('caixa.abrir');
    Route::post('/caixa/fechar/{id}', [CaixaController::class, 'fecharCaixa'])->name('caixa.fechar');
    Route::get('/caixa/detalhes/{id}', [CaixaController::class, 'detalhesCaixa'])->name('caixa.detalhes');

    Route::get('/caixa/verificar', [CaixaController::class, 'verificarCaixa'])->name('caixa.verificar');
    Route::get('/caixa/fechar-anterior', [CaixaController::class, 'fecharCaixaAnterior'])->name('caixa.fecharAnterior');
    Route::post('/caixa/fechar-anterior', [CaixaController::class, 'processarFechamentoAnterior'])->name('caixa.processarFechamentoAnterior');

    Route::get('/search-clients', [ClientController::class, 'searchClients']);

    Route::post('/employees', [EmployeesController::class, 'store'])->name('voyager.employees.store');

    Route::prefix('/clients')->name('voyager.clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy');
    });
});
