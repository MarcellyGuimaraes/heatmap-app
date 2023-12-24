<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EnderecoPedido;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function showFormPedido()
    {
        $clientes = Cliente::all();
        return view('formulario-pedidos', ['clientes' => $clientes]);
    }

    public function criarPedido(Request $request)
    {
        $cliente_id = $request->input('cliente_id');

        // Verifica se o cliente existe
        $cliente = Cliente::find($cliente_id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        // Cria um novo endereço de entrega
        $endereco = new EnderecoPedido();
        $endereco->cliente_id = $cliente_id;
        $endereco->end_rua = $request->input('rua');
        $endereco->end_numero = $request->input('numero');
        $endereco->end_bairro = $request->input('bairro');
        $endereco->end_cep = $request->input('cep');
        $endereco->end_cidade = $request->input('cidade');
        $endereco->end_estado = $request->input('estado');
        // Mais campos, se houver

        $endereco->save();

        // Cria o pedido associado ao cliente e ao endereço de entrega
        $pedido = new Pedido();
        $pedido->cliente_id = $cliente_id;
        $pedido->endereco_entrega_id = $endereco->endereco_entrega_id; // Referência ao endereço criado
        $pedido->ped_observacoes = $request->input('observacoes');
        $pedido->ped_valor_total = $request->input('valor_total');
        $pedido->ped_status = 'OK';
        // Outros campos do pedido, se necessário

        $pedido->save();

        return response()->json(['message' => 'Pedido criado com sucesso'], 201);
    }

}
