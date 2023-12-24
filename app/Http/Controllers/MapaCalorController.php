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
        // Obter todos os clientes e pedidos do banco de dados
        $clientes = Cliente::all();
        $pedidos = EnderecoPedido::all();

        // Obter coordenadas para clientes e pedidos usando a função obterCoordenadasEntidades
        $resultadoClientes = $this->obterCoordenadasEntidades($clientes);
        $resultadoPedidos = $this->obterCoordenadasEntidades($pedidos);

        // Retornar as coordenadas, coordenadas do estabelecimento e endereços não encontrados em formato JSON
        return response()->json([
            'clientes' => $resultadoClientes['coordenadas'],
            'pedidos' => $resultadoPedidos['coordenadas'],
            'estabelecimento' => $this->obterCoordenadasEstabelecimento(),
            'enderecosNaoEncontrados' => array_merge($resultadoClientes['enderecosNaoEncontrados'], $resultadoPedidos['enderecosNaoEncontrados'])
        ]);
    }

    // Função para obter coordenadas para entidades (clientes ou pedidos)
    private function obterCoordenadasEntidades($entidades)
    {
        $client = new Client();
        $coordenadas = [];
        $enderecosNaoEncontrados = []; // Array para armazenar endereços não encontrados

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
                    // Se não houver resultados, adiciona o endereço à lista de endereços não encontrados
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

        return [
            'coordenadas' => $coordenadas,
            'enderecosNaoEncontrados' => $enderecosNaoEncontrados
        ];
    }

    // Função para obter coordenadas do estabelecimento
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
            // Lidar com exceções
        }

        return $coordenadasEstabelecimento;
    }
}
