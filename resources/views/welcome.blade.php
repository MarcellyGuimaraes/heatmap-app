<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laravel</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body>
  <div class="container mt-4">

    <h1>Todos os Endereços</h1>

    <div id="map" style="width: 600px; height: 400px;"></div>

    <form method="POST" action="{{ route('salvarEndereco') }}">
      @csrf
  
      <input type="text" name="cliente" placeholder="Nome do Cliente"><br>
      <input type="text" name="rua" placeholder="Rua"><br>
      <input type="text" name="cep" placeholder="CEP"><br>
      <input type="text" name="numero" placeholder="Número"><br>
      <input type="text" name="bairro" placeholder="Bairro"><br>
      <input type="text" name="cidade" placeholder="Cidade"><br>
      <input type="text" name="estado" placeholder="Estado"><br>
  
      <button type="submit">Enviar</button>
  </form>
  <!-- Inclua o Leaflet Heat -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
  
  <script>
    var map = L.map('map').setView([0, 0], 13); // Coordenadas iniciais temporárias
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    fetch('/get-coordenadas-estabelecimento')
      .then(response => response.json())
      .then(data => {
        map.setView([data.latitude, data.longitude], 13);
      })
      .catch(error => {
        console.error('Erro ao obter as coordenadas do endereço do estabelecimento:', error);
      });
  
    fetch('/get-coordenadas')
      .then(response => response.json())
      .then(data => {
        var heat = L.heatLayer(data, {
          radius: 25,
          blur: 15
        }).addTo(map);
      })
      .catch(error => {
        console.error('Erro ao obter os dados:', error);
      });
  </script>
</body>
</html>