<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeesController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PedidoDeVendaController;
use App\Http\Controllers\ProductController;

Route::get('/', [MainController::class, 'index']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::get('/teste', [MainController::class, 'teste']);
    Route::get('/pedido-de-venda', [PedidoDeVendaController::class, 'index'])->name('pdv.index');
    Route::delete('/remove-product/{index}', [PedidoDeVendaController::class, 'removeProduct'])->name('remove_product');
    Route::get('/search-product-by-code/{code}', [ProductController::class, 'searchProductByCode']);
    Route::patch('/update-product/{index}', [PedidoDeVendaController::class, 'updateProduct'])->name('update_product');
    Route::post('/add-product', [PedidoDeVendaController::class, 'addProduct'])->name('add_product');
    Route::post('/process-payment', [PedidoDeVendaController::class, 'processPayment'])->name('process_payment');
    Route::get('/search-products', [ProductController::class, 'searchProducts'])->name('search_products');


    Route::post('/employees', [EmployeesController::class, 'store'])->name('voyager.employees.store');
    Route::get('/search-clients', [ClientController::class, 'searchClients'])->name('search_clients');
    Route::get('clients', [ClientController::class, 'index'])->name('voyager.clients.index');
    Route::get('clients/{id}/edit', [ClientController::class, 'edit'])->name('voyager.clients.edit');
    Route::delete('clients/{id}', [ClientController::class, 'destroy'])->name('voyager.clients.destroy');
});
