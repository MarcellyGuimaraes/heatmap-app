<?php

namespace App\Http\Controllers;

use App\Models\EnderecoEntrega;
use Illuminate\Http\Request;

class EnderecoEntregaController extends Controller
{
    function getEnderecos() {
        $enderecos = EnderecoEntrega::all();

        return view('welcome', compact('enderecos'));
    }

    function postEndereco(Request $request) {
        $endereco = new EnderecoEntrega();

        $endereco->cliente = $request->input('cliente');
        $endereco->rua = $request->input('rua');
        $endereco->cep = $request->input('cep');
        $endereco->numero = $request->input('numero');
        $endereco->bairro = $request->input('bairro');
        $endereco->cidade = $request->input('cidade');
        $endereco->estado = $request->input('estado');

        $endereco->save();

        return response()->json(['message' => 'Endereço adicionado com sucesso'], 201);
    }
}
