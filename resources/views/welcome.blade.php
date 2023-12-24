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
          loading: true
        };
      },
      mounted() {
        const map = L.map('map').setView([0, 0], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        const loadingIndicator = document.getElementById('loading');
        loadingIndicator.style.display = 'block';
        fetch('/get-coordenadas')
          .then(response => response.json())
          .then(data => {
            const estabelecimentoCoords = data.estabelecimento;
            map.setView(estabelecimentoCoords, 13);
            const clientesCoords = data.clientes;
            const mensagemEnderecosNaoEncontrados = document.getElementById('enderecos-nao-encontrados');
            if (data.enderecosNaoEncontrados.length > 0) {
              const mensagens = data.enderecosNaoEncontrados.map(endereco => {
                return `<p>Endereço não encontrado para ${endereco.nome_cliente}, ${endereco.rua}</p>`;
              });

              mensagemEnderecosNaoEncontrados.innerHTML = mensagens.join('');
            }
            const heat = L.heatLayer(clientesCoords, {
              radius: 25,
              blur: 15,
              minOpacity: 0.5
            }).addTo(map);
            loadingIndicator.style.display = 'none';
            this.loading = false;
          })
          .catch(error => {
            console.error('Erro ao obter os dados:', error);
            loadingIndicator.style.display = 'none';
            this.loading = false;
          });
      }
    });
  </script>
</body>

</html>
