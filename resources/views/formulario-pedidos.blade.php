<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Formulário de Pedidos</title>
</head>

<body>
  <div id="app" class="container mt-4">
    <h1>Formulário de Criação de Pedidos</h1>
    <form @submit.prevent="criarNovoPedido">
      <select v-model="form.cliente_id">
        <option v-for="cliente in clientes" :value="cliente.cliente_id">[[cliente.cli_nome]]</option>
      </select><br>
      <input type="text" v-model="form.rua" placeholder="Rua"><br>
      <input type="text" v-model="form.cep" placeholder="CEP" @input="buscarEnderecoPorCEP"><br>
      <input type="text" v-model="form.numero" placeholder="Número"><br>
      <input type="text" v-model="form.bairro" placeholder="Bairro"><br>
      <input type="text" v-model="form.cidade" placeholder="Cidade"><br>
      <input type="text" v-model="form.estado" placeholder="Estado"><br>
      <input type="text" v-model="form.valor_total" placeholder="Valor Total"><br>
      <textarea v-model="form.observacoes" placeholder="Observações"></textarea><br>
      <button type="submit">Enviar</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
  <script>
    new Vue({
      el: '#app',
      delimiters: ['[[', ']]'],
      data() {
        return {
          form: {
            cliente_id: '',
            rua: '',
            cep: '',
            numero: '',
            bairro: '',
            cidade: '',
            estado: '',
            valor_total: '',
            observacoes: ''
          },
          clientes: {!! json_encode($clientes) !!} // Aqui você precisa passar os dados dos clientes para a variável clientes
        };
      },
      methods: {
        buscarEnderecoPorCEP() {
          // Aqui você fará a requisição para o serviço de consulta de CEP
          const cep = this.form.cep.replace(/\D/g, ''); // Remove caracteres não numéricos do CEP

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
        },
        criarNovoPedido() {
          fetch('{{ route('criarPedido') }}', {
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
