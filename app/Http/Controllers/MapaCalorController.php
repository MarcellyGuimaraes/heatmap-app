<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Estabelecimento;
use Exception;
use GuzzleHttp\Client;

class MapaCalorController extends Controller
{
    public function getEnderecos()
    {
        $enderecos = Cliente::all();

        return view('welcome', compact('enderecos'));
    }

    public function getCoordenadas()
    {
        $clientes = Cliente::all();
        $estabelecimento = Estabelecimento::first();

        $client = new Client();
        $coordenadasClientes = [];
        $coordenadasEstabelecimento = [];
        $enderecosNaoEncontrados = [];

        foreach ($clientes as $cliente) {

            $formattedAddress = urlencode($cliente->cli_endereco .  ', ' . $cliente->cli_cidade . ', ' . $cliente->cli_estado);
            $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . $formattedAddress;

            try {
                $response = $client->request('GET', $url);
                $data = json_decode($response->getBody(), true);

                foreach ($data as $result) {
                    if (isset($result['lat']) && isset($result['lon'])) {
                        $coordenadasClientes[] = [$result['lat'], $result['lon']];
                    }
                }

                if (empty($data)) {
                    $enderecosNaoEncontrados[] = [
                        'id' => $cliente->cli_id,
                        'nome_cliente' => $cliente->cli_nome,
                        'endereco' => $cliente->cli_endereco
                    ];
                }
            } catch (Exception $e) {
                dd($e);
            }
        }

        $formattedEstabelecimentoAddress = urlencode($estabelecimento->est_endereco . ', ' . $estabelecimento->est_numero . ', ' . $estabelecimento->est_cidade . ', ' . $estabelecimento->est_estado);
        $urlEstabelecimento = 'https://nominatim.openstreetmap.org/search?format=json&q=' . $formattedEstabelecimentoAddress;

        try {
            $response = $client->request('GET', $urlEstabelecimento);
            $data = json_decode($response->getBody(), true);

            $latitudeEstabelecimento = $data[0]['lat'];
            $longitudeEstabelecimento = $data[0]['lon'];

            $coordenadasEstabelecimento = [$latitudeEstabelecimento, $longitudeEstabelecimento];
        } catch (Exception $e) {
            dd($e);
        }

        return response()->json([
            "estabelecimento" => $coordenadasEstabelecimento,
            "clientes" => $coordenadasClientes,
            "enderecosNaoEncontrados" => $enderecosNaoEncontrados
        ]);
    }
}