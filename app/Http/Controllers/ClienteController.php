<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function showFormCliente()
    {
        return view('formulario-clientes');
    }

    public function criarCliente(Request $request)
    {
        $cliente = new Cliente();

        $cliente->cli_nome = $request->input('cliente');
        $cliente->cli_endereco = $request->input('rua');
        $cliente->cli_numero = $request->input('numero');
        $cliente->cli_cep = $request->input('cep');
        $cliente->cli_bairro = $request->input('bairro');
        $cliente->cli_cidade = $request->input('cidade');
        $cliente->cli_estado = $request->input('estado');

        $cliente->save();

        return response()->json(['message' => 'Endereço adicionado com sucesso'], 201);
    }

}
