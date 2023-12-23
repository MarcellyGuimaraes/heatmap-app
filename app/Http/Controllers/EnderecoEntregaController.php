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

    function getCoordenadasClientes()
    {
        $enderecos = EnderecoEntrega::all();
        $client = new Client();

        $coordenadas = [];
        foreach ($enderecos as $endereco) {
            $formattedAddress = urlencode($endereco->rua . ', ' . $endereco->numero . ', ' . $endereco->bairro . ', ' . $endereco->cidade . ', ' . $endereco->estado);
            $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . $formattedAddress;
            
            try {
                $response = $client->request('GET', $url);
                $data = json_decode($response->getBody(), true);
                foreach ($data as $result) {
                    if (isset($result['lat']) && isset($result['lon'])) {
                        $coordenadas[] = [$result['lat'], $result['lon']];
                    }
                }
            } catch (Exception $e) {
                echo "Erro ao buscar coordenadas para o endereço: {$endereco->rua}, {$endereco->numero}, {$endereco->bairro}, {$endereco->cidade}, {$endereco->estado}. Erro: " . $e->getMessage();
            }
         }

        return response()->json($coordenadas);
    }

    function getCoordenadasEstabelecimento()
    {
        $enderecoEstabelecimento = DB::table('estabelecimentos')->first();
        $formattedAddress = urlencode($enderecoEstabelecimento->rua . ', ' . $enderecoEstabelecimento->numero . ', ' . $enderecoEstabelecimento->cidade . ', ' . $enderecoEstabelecimento->estado);
        $client = new Client();
        $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . $formattedAddress;

        try {
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody(), true);
            $latitude = $data[0]['lat'];
            $longitude = $data[0]['lon'];

            return response()->json(['latitude' => $latitude, 'longitude' => $longitude]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao buscar as coordenadas do endereço do estabelecimento']);
        }
    }
}
