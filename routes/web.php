<?php

// use App\Http\Controllers\EnderecoEntregaController;

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\MapaCalorController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [MapaCalorController::class, 'getEnderecos']);
Route::get('/get-coordenadas', [MapaCalorController::class, 'getCoordenadas']);

Route::get('/formulario-clientes', [ClienteController::class, 'showFormCliente'])->name('formularioClientes');
Route::post('/criar-cliente', [ClienteController::class, 'criarCliente'])->name('criarCliente');


Route::get('/formulario-pedido', [PedidoController::class, 'showFormPedido'])->name('formularioPedido');
Route::post('/criar-pedido', [PedidoController::class, 'criarPedido'])->name('criarPedido');