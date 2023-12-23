<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laravel</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">

    <h1>Todos os Endereços</h1>

    @foreach($enderecos as $endereco)
        <p>Nome do Cliente: {{ $endereco->cliente }}</p>
        <p>Rua: {{ $endereco->rua }}</p>
        <p>CEP: {{ $endereco->cep }}</p>
        <p>Número: {{ $endereco->numero }}</p>
        <p>Bairro: {{ $endereco->bairro }}</p>
        <p>Cidade: {{ $endereco->cidade }}</p>
        <p>Estado: {{ $endereco->estado }}</p>
        <hr>
    @endforeach

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
  </div>
  <!-- Bootstrap JavaScript (jQuery and Popper.js dependencies included) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
</body>
</html>