<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laravel</title>

  <!-- Importação do estilo CSS do Tailwind -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  
  <!-- Importação do estilo CSS da biblioteca Leaflet para mapas -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
</head>

<body class="bg-gray-100">
  <div id="app" class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-4">Mapa de calor com laravel</h1>

    <!-- Div para exibir botões -->
    <div class="mb-4">
      <button :class="{ 'bg-blue-700': tipoMapa === 'clientes' }" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-4" @click="alternarMapa('clientes')">Mapa de Calor - Clientes</button>
      <button :class="{ 'bg-blue-700': tipoMapa === 'pedidos' }" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="alternarMapa('pedidos')">Mapa de Calor - Pedidos</button>
    </div>

    <!-- Div que contém o mapa -->
    <div id="map" class="mb-4 relative" style="width:60rem; height:40rem;"></div>

    <!-- Div para exibir mensagem de carregamento -->
    <div id="loading" class="hidden">Carregando...</div>

    <!-- Div para exibir endereços não encontrados -->
    <div id="enderecos-nao-encontrados"></div>
  </div>

  <!-- Importação da biblioteca Vue.js -->
  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>

  <!-- Importação da biblioteca Leaflet para mapas interativos -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <!-- Importação da extensão Leaflet.heat para visualização de mapas de calor -->
  <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

  <!-- Script Vue.js -->
  <script>
    new Vue({
      el: '#app',
      data() {
        return {
          loading: true, // Estado de carregamento
          tipoMapa: 'clientes', // Tipo de mapa exibido inicialmente
          map: null, // Referência para o mapa Leaflet
          heatLayer: null // Referência para a camada de mapa de calor
        };
      },
      methods: {
        // Método para alternar entre mapas de clientes e pedidos
        alternarMapa(tipo) {
          this.tipoMapa = tipo;
          this.carregarMapa();
        },
        // Método para carregar e exibir o mapa
        carregarMapa() {
          if (!this.map) {
            // Criação do mapa Leaflet
            this.map = L.map('map').setView([0, 0], 13);
            // Adição de um conjunto de tiles do OpenStreetMap ao mapa
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
          }

          // Exibição do indicador de carregamento
          const loadingIndicator = document.getElementById('loading');
          loadingIndicator.style.display = 'block';

          // Requisição para obter coordenadas do servidor dependendo do tipo de mapa selecionado
          fetch(`/get-coordenadas?tipo=${this.tipoMapa}`)
            .then(response => response.json())
            .then(data => {
              // Configuração da visualização do mapa com base nos dados obtidos
              const estabelecimentoCoords = data.estabelecimento;
              this.map.setView(estabelecimentoCoords, 13);

              const coordenadas = this.tipoMapa === 'clientes' ? data.clientes : data.pedidos;

              // Remoção da camada de mapa de calor existente, se houver
              if (this.heatLayer) {
                this.map.removeLayer(this.heatLayer);
              }

              // Exibição de endereços não encontrados, se houver
              const mensagemEnderecosNaoEncontrados = document.getElementById('enderecos-nao-encontrados');
              if (data.enderecosNaoEncontrados.length > 0) {
                const mensagens = data.enderecosNaoEncontrados.map(endereco => {
                  return `<p>Endereço não encontrado para ${endereco.nome_cliente}, ${endereco.rua}</p>`;
                });

                mensagemEnderecosNaoEncontrados.innerHTML = mensagens.join('');
              }

              // Criação e adição da camada de mapa de calor ao mapa Leaflet
              this.heatLayer = L.heatLayer(coordenadas, {
                radius: 25,
                blur: 15,
                minOpacity: 0.5
              }).addTo(this.map);

              // Ocultação do indicador de carregamento e atualização do estado de carregamento
              loadingIndicator.style.display = 'none';
              this.loading = false;
            })
            .catch(error => {
              // Tratamento de erros durante a obtenção dos dados
              console.error('Erro ao obter os dados:', error);
              loadingIndicator.style.display = 'none';
              this.loading = false;
            });
        }
      },
      mounted() {
        this.carregarMapa(); // Carregar o mapa inicialmente ao montar o componente Vue
      }
    });
  </script>
</body>

</html>
