<?php

use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;

use App\Http\Controllers\PedidosController;
use App\Http\Controllers\PesquisarController;
use App\Http\Controllers\CadastrarController;
use App\Http\Controllers\AnunciarController;
use App\Http\Controllers\EntrarController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProdutosController;

Route::get('/pedidos', [PedidosController::class, 'index'])->name('pedidos.index');
Route::get('/pesquisar', [ProdutosController::class, 'index'])->name('produtos.index');
Route::get('/cadastrar', [CadastrarController::class, 'index'])->name('cadastrar.index');
Route::get('/anunciar', [AnunciarController::class, 'index'])->name('anunciar.index');
Route::get('/entrar', [EntrarController::class, 'index'])->name('entrar.index');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('page.show');
Route::get('/products', [ProdutosController::class, 'index'])->name('produtos.index');
Route::get('/products/{slug}', [ProdutosController::class, 'show'])->name('produtos.show');
Route::get('/products/{slug}/{id}', [ProdutosController::class, 'showProduct'])->name('produtos.showProduct');
Route::get('/products/{categoria}/{propertyType}/{id}', [ProdutosController::class, 'showProductWithPropertyType'])->name('produtos.showProductWithPropertyType');


Route::get('/', [MainController::class, 'index']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
