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

        $cliente = Cliente::find($cliente_id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $endereco = new EnderecoPedido();
        $endereco->cliente_id = $cliente_id;
        $endereco->end_rua = $request->input('rua');
        $endereco->end_numero = $request->input('numero');
        $endereco->end_bairro = $request->input('bairro');
        $endereco->end_cep = $request->input('cep');
        $endereco->end_cidade = $request->input('cidade');
        $endereco->end_estado = $request->input('estado');

        $endereco->save();

        $pedido = new Pedido();
        $pedido->cliente_id = $cliente_id;
        $pedido->endereco_entrega_id = $endereco->endereco_entrega_id; // Referência ao endereço criado
        $pedido->ped_observacoes = $request->input('observacoes');
        $pedido->ped_valor_total = $request->input('valor_total');
        $pedido->ped_status = 'OK';
        
        $pedido->save();

        return response()->json(['message' => 'Pedido criado com sucesso'], 201);
    }

    public function criarRegistrosAleatorios(){
        $clientes = Cliente::all();
        for ($i = 0; $i < 50; $i++) {
            $clienteAleatorio = $clientes->random();

            // $enderecoCliente = EnderecoPedido::where('cliente_id', $clienteAleatorio->cliente_id)->inRandomOrder()->first();

            $enderecoPedido = new EnderecoPedido([
                'cliente_id' => $clienteAleatorio->cliente_id,
                'end_rua' => $clienteAleatorio->cli_endereco,
                'end_numero' => $clienteAleatorio->cli_numero,
                'end_bairro' => $clienteAleatorio->cli_bairro,
                'end_cidade' => $clienteAleatorio->cli_cidade,
                'end_estado' => $clienteAleatorio->cli_estado,
                'end_cep' => $clienteAleatorio->cli_cep,
            ]);

            $pedido = new Pedido([
                'cliente_id' => $clienteAleatorio->cliente_id,
                'endereco_entrega_id' => $enderecoPedido->save(),
                'ped_status' => 'Em andamento',
                'ped_valor_total' => rand(50, 500),
                'ped_observacoes' => 'Observações aleatórias ' . $i,
            ]);

            $pedido->save();
        }

        return '100 pedidos criados com endereços aleatórios baseados nos clientes existentes!';

    }
}
