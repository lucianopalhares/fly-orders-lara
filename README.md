<p align="center"><img src="assets/img/cover.png" width="400" alt="Cover"></p>

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

#### adicione permissão

```
chmod 777 -R storage bootstrap
```

#### saia do container

```
exit
```

#### reinicie a aplicação

```
docker-compose restart
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
