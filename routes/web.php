<?php

use App\Http\Controllers\EnderecoEntregaController;
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
Route::get('/', [EnderecoEntregaController::class, 'getEnderecos']);
Route::get('/get-coordenadas', [EnderecoEntregaController::class, 'getCoordenadasClientes']);
Route::get('/get-coordenadas-estabelecimento', [EnderecoEntregaController::class, 'getCoordenadasEstabelecimento']);
Route::post('/salvar-endereco', [EnderecoEntregaController::class, 'postEndereco'])->name('salvarEndereco');