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
  <div id="app" class="container mt-4">
    <h1>Todos os Endereços</h1>
    <div id="map" style="width: 600px; height: 400px;"></div>
    <div id="loading" v-show="loading">Carregando...</div>
    <div id="enderecos-nao-encontrados"></div>
    <form @submit.prevent="salvarEndereco">
      <input type="text" v-model="form.cliente" placeholder="Nome do Cliente"><br>
      <input type="text" v-model="form.rua" placeholder="Rua"><br>
      <input type="text" v-model="form.cep" placeholder="CEP"><br>
      <input type="text" v-model="form.numero" placeholder="Número"><br>
      <input type="text" v-model="form.bairro" placeholder="Bairro"><br>
      <input type="text" v-model="form.cidade" placeholder="Cidade"><br>
      <input type="text" v-model="form.estado" placeholder="Estado"><br>
      <button type="submit">Enviar</button>
    </form>
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
          form: {
            cliente: '',
            rua: '',
            cep: '',
            numero: '',
            bairro: '',
            cidade: '',
            estado: ''
          }
        };
      },
      methods: {
        salvarEndereco() {
          // Faça algo com os dados do formulário
          console.log('Dados do formulário:', this.form);
          // Aqui você pode enviar os dados para o servidor usando Axios ou fetch
          // Exemplo com fetch:
          fetch('{{ route('salvarEndereco') }}', {
            method: 'POST',
            body: JSON.stringify(this.form),
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
          })
          .then(response => {
            window.location.href = "/"
            // Trate a resposta do servidor, se necessário
          })
          .catch(error => {
            console.error('Erro ao enviar os dados:', error);
          });
        }
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
              minOpacity: 0.9
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