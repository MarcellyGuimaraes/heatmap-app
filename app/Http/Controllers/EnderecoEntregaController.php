<?php

namespace App\Http\Controllers;

use App\Models\EnderecoEntrega;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    function getCoordenadas()
    {
        $enderecosClientes = EnderecoEntrega::all();
        $enderecoEstabelecimento = DB::table('estabelecimentos')->first();

        $client = new Client();
        $coordenadasClientes = [];
        $coordenadasEstabelecimento = [];

        // Obter coordenadas dos endereços dos clientes
        foreach ($enderecosClientes as $endereco) {
            $formattedAddress = urlencode($endereco->rua . ', ' . $endereco->numero . ', ' . $endereco->bairro . ', ' . $endereco->cidade . ', ' . $endereco->estado);
            $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . $formattedAddress;

            try {
                $response = $client->request('GET', $url);
                $data = json_decode($response->getBody(), true);
                foreach ($data as $result) {
                    if (isset($result['lat']) && isset($result['lon'])) {
                        $coordenadasClientes[] = [$result['lat'], $result['lon']];
                    }
                }
            } catch (Exception $e) {
                // Tratar erros, se necessário
            }
        }

        // Obter coordenadas do endereço do estabelecimento
        $formattedEstabelecimentoAddress = urlencode($enderecoEstabelecimento->rua . ', ' . $enderecoEstabelecimento->numero . ', ' . $enderecoEstabelecimento->cidade . ', ' . $enderecoEstabelecimento->estado);
        $urlEstabelecimento = 'https://nominatim.openstreetmap.org/search?format=json&q=' . $formattedEstabelecimentoAddress;

        try {
            $response = $client->request('GET', $urlEstabelecimento);
            $data = json_decode($response->getBody(), true);
            $latitudeEstabelecimento = $data[0]['lat'];
            $longitudeEstabelecimento = $data[0]['lon'];

            // Adicionar coordenadas do estabelecimento à lista de coordenadas
            $coordenadasEstabelecimento = [$latitudeEstabelecimento, $longitudeEstabelecimento];
        } catch (Exception $e) {
            // Tratar erros, se necessário
        }

        return response()->json([
            "estabelecimento" => $coordenadasEstabelecimento,
            "clientes" => $coordenadasClientes
        ]);
    }
}
