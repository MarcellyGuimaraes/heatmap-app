<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EnderecoPedido;
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

    public function getCoordenadas($tipo)
    {
        switch ($tipo) {
            case 'clientes':
                return $this->obterCoordenadasClientes();
                break;
            case 'pedidos':
                return $this->obterCoordenadasPedidos();
                break;
            default:
                return response()->json(['error' => 'Tipo inválido'], 400);
                break;
        }
    }

    private function obterCoordenadasClientes()
    {
        $clientes = Cliente::all();
        $coordenadasClientes = $this->obterCoordenadasEntidades($clientes);
        return response()->json(["clientes" => $coordenadasClientes]);
    }

    private function obterCoordenadasPedidos()
    {
        $pedidos = EnderecoPedido::all();
        $coordenadasPedidos = $this->obterCoordenadasEntidades($pedidos);
        return response()->json(["pedidos" => $coordenadasPedidos]);
    }

    private function obterCoordenadasEntidades($entidades)
    {
        $client = new Client();
        $coordenadas = [];

        foreach ($entidades as $entidade) {
            $formattedAddress = urlencode($entidade->enderecoCompleto()); // Supondo um método 'enderecoCompleto' na entidade
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
                // Lidar com exceções
            }
        }

        return $coordenadas;
    }

    function obterCoordenadasEstabelecimentos()
    {
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
            "estabelecimento" => $coordenadasEstabelecimento
        ]);
    }
}