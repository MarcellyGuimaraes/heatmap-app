<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Formulário de Clientes</title>
</head>

<body>
  <div id="app" class="container mt-4">
    <h1>Formulário de Criação de Clientes</h1>
    <form @submit.prevent="criarNovoCliente">
      <input type="text" v-model="form.cliente" placeholder="Nome do Cliente"><br>
      <input type="text" v-model="form.rua" placeholder="Rua"><br>
      <input type="text" v-model="form.cep" placeholder="CEP" @input="buscarEndereco"><br>
      <input type="text" v-model="form.numero" placeholder="Número"><br>
      <input type="text" v-model="form.bairro" placeholder="Bairro"><br>
      <input type="text" v-model="form.cidade" placeholder="Cidade"><br>
      <input type="text" v-model="form.estado" placeholder="Estado"><br>
      <button type="submit">Enviar</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
  <script>
    new Vue({
      el: '#app',
      data() {
        return {
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
        buscarEndereco() {
            // Verifica se o CEP possui 8 dígitos para realizar a busca
            if (this.form.cep.length === 8) {
            fetch(`http://viacep.com.br/ws/${this.form.cep}/json/`)
                .then(response => response.json())
                .then(data => {
                this.form.rua = data.logradouro;
                this.form.bairro = data.bairro;
                this.form.cidade = data.localidade;
                this.form.estado = data.uf;
                // Você pode adicionar mais campos conforme necessário
                })
                .catch(error => {
                console.error('Erro ao buscar o endereço:', error);
                });
            }
        },
        criarNovoCliente() {
          fetch('{{ route('criarCliente') }}', {
            method: 'POST',
            body: JSON.stringify(this.form),
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
          })
          .then(response => {
            // Redirecionar ou tratar a resposta do servidor, se necessário
            console.log('Resposta do servidor:', response);
          })
          .catch(error => {
            console.error('Erro ao enviar os dados:', error);
          });
        }
      }
    });
  </script>
</body>

</html>
