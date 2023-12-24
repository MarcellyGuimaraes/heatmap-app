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
    <div id="map" style="height: 50vh;"></div>
    <div id="loading" v-show="loading">Carregando...</div>
    <div id="enderecos-nao-encontrados"></div>

    <div>
      <button @click="alternarMapa('clientes')">Mapa de Clientes</button>
      <button @click="alternarMapa('pedidos')">Mapa de Pedidos</button>
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
          mapaSelecionado: 'clientes',
          coordenadas: {
            clientes: [],
            pedidos: []
          }
        };
      },
      methods: {
        alternarMapa(tipoMapa) {
          this.mapaSelecionado = tipoMapa;
          this.atualizarMapa();
        },
        atualizarMapa() {
          const map = L.map('map').setView([0, 0], 13);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
          }).addTo(map);

          const heat = L.heatLayer(this.coordenadas[this.mapaSelecionado], {
            radius: 25,
            blur: 15,
            minOpacity: 0.5
          }).addTo(map);

          const loadingIndicator = document.getElementById('loading');
          loadingIndicator.style.display = 'block';
          loadingIndicator.style.display = 'none';
          this.loading = false;
        },
        obterCoordenadas(tipo) {
          fetch(`/get-coordenadas/${tipo}`)
            .then(response => response.json())
            .then(data => {
              this.coordenadas[tipo] = data[tipo];
              if (tipo === this.mapaSelecionado) {
                this.atualizarMapa();
              }
            })
            .catch(error => {
              console.error('Erro ao obter os dados:', error);
              this.loading = false;
            });
        }
      },
      mounted() {
        this.obterCoordenadas('clientes'); // Carrega as coordenadas dos clientes ao iniciar a página
      }
    });
  </script>
</body>

</html>
