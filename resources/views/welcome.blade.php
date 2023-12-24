<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laravel</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
</head>

<body>
  <div id="app">
    <div>
      <a href="/formulario-clientes">ADICIONAR CLIENTES</a>
      <a href="/formulario-pedido">ADICIONAR PEDIDOS</a>
    </div>

    <div id="map" style="height: 50vh;"></div>
    <div id="loading" v-show="loading">Carregando...</div>
    <div id="enderecos-nao-encontrados"></div>
    <div>
      <button @click="alternarMapa('clientes')">Mapa de Calor - Clientes</button>
      <button @click="alternarMapa('pedidos')">Mapa de Calor - Pedidos</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

  <script>
    new Vue({
      el: '#app',
      data() {
        return {
          loading: true,
          tipoMapa: 'clientes',
          map: null, // Guarda a referência do mapa
          heatLayer: null // Guarda a referência do mapa de calor
        };
      },
      methods: {
        alternarMapa(tipo) {
          this.tipoMapa = tipo;
          this.carregarMapa();
        },
        carregarMapa() {
          if (!this.map) {
            this.map = L.map('map').setView([0, 0], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
          }

          const loadingIndicator = document.getElementById('loading');
          loadingIndicator.style.display = 'block';

          fetch(`/get-coordenadas?tipo=${this.tipoMapa}`)
            .then(response => response.json())
            .then(data => {
              const estabelecimentoCoords = data.estabelecimento;
              this.map.setView(estabelecimentoCoords, 13);

              const coordenadas = this.tipoMapa === 'clientes' ? data.clientes : data.pedidos;

              if (this.heatLayer) {
                this.map.removeLayer(this.heatLayer); // Remove a camada de mapa de calor existente
              }

              this.heatLayer = L.heatLayer(coordenadas, {
                radius: 25,
                blur: 15,
                minOpacity: 0.5
              }).addTo(this.map);

              loadingIndicator.style.display = 'none';
              this.loading = false;
            })
            .catch(error => {
              console.error('Erro ao obter os dados:', error);
              loadingIndicator.style.display = 'none';
              this.loading = false;
            });
        }
      },
      mounted() {
        this.carregarMapa(); // Carregar o mapa inicialmente
      }
    });
  </script>
</body>

</html>
