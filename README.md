<p align="center"><img src="public/assets/img/cover.png" width="400" alt="Cover"></p>

## Fly Orders Lara

Microserviço para fechar pedidos de viagem. Funcionalidades:

- Criar um pedido de viagem: Um pedido deve incluir o ID do pedido, o nome do solicitante, o destino, a data de ida, a data de volta e o status (solicitado, aprovado, cancelado).
- Atualizar o status de um pedido de viagem: Possibilitar a atualização do status para "aprovado" ou "cancelado". (nota: o usuário que fez o pedido não pode alterar o status do mesmo)
- Consultar um pedido de viagem: Retornar as informações detalhadas de um pedido de viagem com base no ID fornecido.
- Listar todos os pedidos de viagem: Retornar todos os pedidos de viagem cadastrados, com a opção de filtrar por status.
- Cancelar pedido de viagem após aprovação: Implementar uma lógica de negócios que verifique se é possível cancelar um pedido já aprovado 
- Filtragem por período e destino: Adicionar filtros para listar pedidos de viagem por período de tempo (ex: pedidos feitos ou com datas de viagem dentro de uma faixa de datas) e/ou por destino.
- Notificação de aprovação ou cancelamento: Sempre que um pedido for aprovado ou cancelado, uma notificação deve ser enviada para o usuário que solicitou o pedido.

### Tecnologias usadas:

* Framework Laravel 11 (php 8.4)
* API de Microserviço com as melhores práticas de design patterns
* Autenticação JWT
* Banco de dados Mysql
* Documentação da API

### Instalação

#### instale a aplicação

```
docker-compose up -d
```

#### entre na aplicação laravel

```
docker exec -it fly_orders_lara_app bash
```

#### instale as dependencias necessarias

```
composer install
```

#### crie o arquivo de configuração

```
cp .env.example .env
```

#### crie a chave da aplicação

```
php artisan key:generate
```

#### crie as tabelas do banco de dados

```
php artisan migrate
```

#### popule o banco de dados

```
php artisan db:seed
```

#### adicione permissão

```
chmod 777 -R storage bootstrap
```

### Documentação da api

http://localhost:8000/docs/api

### Exemplo de uso [criar ou realizar login com usuário]:

#### crie um usuário pela api

metodo: POST
url:
```
http://localhost:8000/api/register
```
body:
```
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

a conta do usuário sera criada
e um token de acesso sera gerado

#### se usuário ja foi criado, faça login

metodo: POST
url:
```
http://localhost:8000/api/login
```
body:
```
{
    "email": "john@example.com",
    "password": "password"
}
```

um token de acesso sera gerado

#### se usuário ja esta logado e deseja apenas pegar os dados

metodo: GET
url:
```
http://localhost:8000/api/profile
```

#### sair da sessão do usuário

metodo: POST
url:
```
http://localhost:8000/api/logout
```

#### utilize o token para as proximas etapas

### Exemplo de uso [gerenciar pedidos]:

#### crie um pedido para o usuário logado (use o token)

    -   o pedido é criado e é atrelado ao usuário logado

metodo: POST
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/orders/create
```
body:
```
{
  "requester_name": "João Silva",
  "destination_name": "Rio de Janeiro",
  "departure_date": "2025-06-15",
  "return_date": "2025-06-20",
  "status": "requested"
}
```

#### listar pedidos do usuário logado

    -   parametros de busca:
        -   filtrar pelo nome do solicitante definido: requester_name
        -   filtrar o local do destino: destination_name
        -   filtrar intervalo da data de partida: departure_date_start e departure_date_end
        -   filtrar intervalo da data de retorno: return_date_start e return_date_end
        -   filtrar por status: requested, canceled ou approved

        -   coloque no filtro as datas com formato: ano/mes/dia

        -   somente os pedidos pertencentes ao usuario logado são visualizados

metodo: GET
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/orders/list
```

#### ver pedido do usuário logado

    -   somente sera autorizado o usuario ver algum pedido que ele mesmo criou

metodo: GET
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/orders/show/ID
```

#### atualizar status de um pedido de outro usuário

    -   o usuário logado que criou o pedido não pode alterar o status do mesmo
    -   somente outros usuários que não criou o pedido, podem alterar o status do mesmo

    -   a seguinte ordem de alteração de status é obedecida:
        -   requested -> canceled (solicitado para cancelado)
        -   requested -> approved (solicitado para aprovado)
        -   approved  -> canceled (somente se a data de partida (departure_date) não estiver vencida)

    -   não precisa indicar o status que quer alterar
    -   existe uma lógica que identifica o proximo status do pedido

metodo: GET
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/orders/ID/update-status
```

#### ver notificações de usuário logado

 -   sera exibido todas notificações que pertencem ao usuario logado

metodo: GET
authorization: BearerToken (use o token gerado)
url:
```
http://localhost:8000/api/notifications
```

## Funções somente para usuário logado:

    -   criar pedido
    -   listar pedidos
    -   alterar status do pedido
    -   visualizar um pedido
    -   deslogar
    -   ver perfil do usuario logado
    -   ver notificações do usuário logado

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
