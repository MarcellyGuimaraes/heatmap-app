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

    public function getCoordenadas()
    {
        $clientes = Cliente::all();
        $pedidos = EnderecoPedido::all();

        $coordenadasClientes = $this->obterCoordenadasEntidades($clientes);
        $coordenadasPedidos = $this->obterCoordenadasEntidades($pedidos);
        $coordenadasEstabelecimento = $this->obterCoordenadasEstabelecimento();

        return response()->json([
            'clientes' => $coordenadasClientes,
            'pedidos' => $coordenadasPedidos,
            'estabelecimento' => $coordenadasEstabelecimento
        ]);
    }

    private function obterCoordenadas($tipo)
    {
        $coordenadas = [];
        $estabelecimentoCoords = $this->obterCoordenadasEstabelecimento();
        
        if ($tipo === 'clientes') {
            $clientes = Cliente::all();
            $coordenadas = $this->obterCoordenadasEntidades($clientes);
        } elseif ($tipo === 'pedidos') {
            $pedidos = EnderecoPedido::all();
            $coordenadas = $this->obterCoordenadasEntidades($pedidos);
        }

        return response()->json([
            $tipo => $coordenadas,
            'estabelecimento' => $estabelecimentoCoords
        ]);
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

    private function obterCoordenadasEstabelecimento()
    {
        $estabelecimento = Estabelecimento::first();
        $client = new Client();

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

        return $coordenadasEstabelecimento;
    }
}
