<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EnderecoPedido;
use App\Models\Estabelecimento;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

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

        $resultadoClientes = $this->obterCoordenadasEntidades($clientes);
        $resultadoPedidos = $this->obterCoordenadasEntidades($pedidos);

        return response()->json([
            'clientes' => $resultadoClientes['coordenadas'],
            'pedidos' => $resultadoPedidos['coordenadas'],
            'estabelecimento' => $this->obterCoordenadasEstabelecimento(),
            'enderecosNaoEncontrados' => array_merge($resultadoClientes['enderecosNaoEncontrados'], $resultadoPedidos['enderecosNaoEncontrados'])
        ]);
    }

    private function obterCoordenadasEntidades($entidades)
    {
        $client = new Client();
        $coordenadas = [];
        $enderecosNaoEncontrados = [];

        foreach ($entidades as $entidade) {
            $formattedAddress = urlencode($entidade->enderecoCompleto());
            $cacheKey = 'coord_' . $formattedAddress;

            $cached = Cache::get($cacheKey);

            if ($cached) {
                $coordenadas = array_merge($coordenadas, $cached);
            } else {
                try {
                    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . $formattedAddress;
                    $response = $client->request('GET', $url);
                    $data = json_decode($response->getBody(), true);

                    if (!empty($data)) {
                        $coords = [];
                        foreach ($data as $result) {
                            if (isset($result['lat']) && isset($result['lon'])) {
                                $coords[] = [$result['lat'], $result['lon']];
                                $coordenadas[] = [$result['lat'], $result['lon']];
                            }
                        }
                        Cache::put($cacheKey, $coords, 1440); // Cache por 24 horas
                    } else {
                        $enderecosNaoEncontrados[] = [
                            'id' => $entidade->id,
                            'nome_cliente' => $entidade->nome_cliente,
                            'rua' => $entidade->rua
                        ];
                    }
                } catch (Exception $e) {
                    // Lidar com exceções
                }
            }
        }

        return [
            'coordenadas' => $coordenadas,
            'enderecosNaoEncontrados' => $enderecosNaoEncontrados
        ];
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

            if (!empty($data)) {
                $latitudeEstabelecimento = $data[0]['lat'];
                $longitudeEstabelecimento = $data[0]['lon'];
                $coordenadasEstabelecimento = [$latitudeEstabelecimento, $longitudeEstabelecimento];
            } else {
                $coordenadasEstabelecimento = null;
            }
        } catch (Exception $e) {
            // Lidar com exceções
        }

        return $coordenadasEstabelecimento;
    }
}
