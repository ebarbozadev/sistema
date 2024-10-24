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

Route::get('/pedidos', [PedidosController::class, 'index'])->name('pedidos.index');
Route::get('/pesquisar', [PesquisarController::class, 'index'])->name('pesquisar.index');
Route::get('/cadastrar', [CadastrarController::class, 'index'])->name('cadastrar.index');
Route::get('/anunciar', [AnunciarController::class, 'index'])->name('anunciar.index');
Route::get('/entrar', [EntrarController::class, 'index'])->name('entrar.index');

Route::get('/', [MainController::class, 'index']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
