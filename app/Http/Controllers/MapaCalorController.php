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
        $enderecosNaoEncontrados = []; // Inicialize um array para os endereços não encontrados

        foreach ($entidades as $entidade) {
            $formattedAddress = urlencode($entidade->enderecoCompleto());

            $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . $formattedAddress;

            try {
                $response = $client->request('GET', $url);
                $data = json_decode($response->getBody(), true);

                if (!empty($data)) {
                    foreach ($data as $result) {
                        if (isset($result['lat']) && isset($result['lon'])) {
                            $coordenadas[] = [$result['lat'], $result['lon']];
                        }
                    }
                } else {
                    // Se o resultado estiver vazio, adicione o endereço à lista de endereços não encontrados
                    $enderecosNaoEncontrados[] = [
                        'id' => $entidade->id,
                        'nome_cliente' => $entidade->nome_cliente, // Ajuste para os campos corretos da sua entidade
                        'rua' => $entidade->rua // Ajuste para os campos corretos da sua entidade
                    ];
                }
            } catch (Exception $e) {
                // Lidar com exceções
            }
        }

        return [
            'coordenadas' => $coordenadas,
            'enderecosNaoEncontrados' => $enderecosNaoEncontrados // Retorne os endereços não encontrados
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

            $latitudeEstabelecimento = $data[0]['lat'];
            $longitudeEstabelecimento = $data[0]['lon'];

            $coordenadasEstabelecimento = [$latitudeEstabelecimento, $longitudeEstabelecimento];
        } catch (Exception $e) {
            dd($e);
        }

        return $coordenadasEstabelecimento;
    }
}
