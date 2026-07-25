<div align="center">
  <img src="https://img.shields.io/badge/Maintained%3F-yes-green.svg?style=for-the-badge" alt="Maintained Badge"/>
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php" alt="PHP Badge"/>
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Badge"/>
  <img src="https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker" alt="Docker Badge"/>
  <img src="https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge" alt="License Badge"/>
</div>

<br>

# 🛒 AppMercearia ERP (Multi-Tenant)

> **O que faz:** Um ERP (Enterprise Resource Planning) SaaS completo e Multi-Tenant, desenvolvido para automatizar e gerenciar as operações diárias de múltiplas Mercearias e Distribuidoras. Cada empresa (loja/distribuidora) possui seu banco de dados totalmente isolado.
> 
> **Com o que foi construído:** Desenvolvido com Laravel 12, PHP 8.2 e MySQL 8.4, arquitetura de banco de dados por inquilino (`stancl/tenancy`), rodando em um ambiente isolado com Docker (Laravel Sail).
> 
> **Por que foi construído:** Este projeto foca em dominar a criação de um SaaS B2B moderno. A arquitetura Multi-Tenant garante a segurança e privacidade dos dados, fundamental para um sistema de gestão robusto que será utilizado por diversas distribuidoras e pequenos mercados.

---

## 🚀 Instalação e Configuração

Siga os passos abaixo para executar este projeto na sua máquina local. 

**Pré-requisitos:**
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (com WSL2 configurado para usuários Windows).
- Porta `80` e `3306` liberadas na sua máquina.

**Passo a passo:**

1. Clone o repositório:
```bash
git clone https://github.com/GabrielBacaf/AppMercearia.git
cd AppMercearia
```

2. Instale as dependências usando um mini-contêiner do Laravel (não exige PHP instalado no Windows):
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

3. Configure as variáveis de ambiente e suba os contêineres:
```bash
cp .env.example .env
./vendor/bin/sail up -d
```

4. Prepare o banco de dados e as chaves de segurança:
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

---

## 🏢 Gestão de Múltiplas Lojas (Multi-Tenant)

O sistema utiliza a abordagem "Database per Tenant" (um banco de dados exclusivo para cada empresa cadastrada). 
Isso significa que existem **duas rotas principais** de acesso:
1. **Central SaaS (`/api/v1/tenants`):** Acessada apenas pelo Administrador Global do sistema para cadastrar novas empresas/mercearias.
2. **Empresas/Lojas (`http://nomedaloja.localhost/...`):** Acessada pelos donos das mercearias para operar seu negócio diário. O banco de dados muda dinamicamente baseado no subdomínio.

### Como criar uma nova Distribuidora/Mercearia via Tinker:
Para simular a criação de uma nova loja pelo terminal (ou testar o comportamento SaaS):
```bash
php artisan tinker
```
```php
// 1. Cria a loja e o subdomínio
$tenant = App\Models\Tenant::create(['id' => 'minhadistribuidora']);
$tenant->domains()->create(['domain' => 'minhadistribuidora.localhost']);

// 2. Entra no banco de dados exclusivo da loja e cria o usuário Admin dela
$tenant->run(function () {
    $role = Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
    $user = App\Models\User::create([
        'name' => 'Dono da Distribuidora',
        'login' => 'adminloja',
        'email' => 'admin@distribuidora.com',
        'password' => Hash::make('12345678')
    ]);
    $user->assignRole($role);
});
```

---

## 📖 Instruções de Uso da API

A API está disponível em `http://localhost:8000` (Central) e nos **subdomínios das lojas** (ex: `http://minhadistribuidora.localhost:8000`). Todas as rotas de operação (produtos, vendas) DEVEM ser acessadas pelo domínio da loja.

Recomendamos o uso do **Postman** para testar as rotas.

### 🔐 1. Autenticação (Login na Loja)
**POST** `http://minhadistribuidora.localhost:8000/api/v1/login`
```json
{
  "login": "adminloja",
  "password": "12345678",
  "device_name": "postman"
}
```
*Copie o `access_token` retornado e use na aba "Authorization -> Bearer Token" nas próximas requisições.*

### 👥 2. Criar Usuário e Perfil
**POST** `http://minhadistribuidora.localhost:8000/api/v1/roles` (Criar Perfil)
```json
{
  "name": "gerente",
  "permissions": [1, 2, 3] 
}
```
*(Faça um GET em `/api/v1/permissions` para ver os IDs disponíveis)*

**POST** `http://minhadistribuidora.localhost:8000/api/v1/users` (Criar Usuário)
```json
{
  "name": "João Silva",
  "login": "joaosilva",
  "email": "joao@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "roles": ["gerente"]
}
```

### 🛒 3. Registrar Compra (Entrada de Estoque)
**POST** `http://minhadistribuidora.localhost:8000/api/v1/purchases`
```json
{
  "title": "Compra de Bebidas",
  "purchase_date": "2023-11-20",
  "supplier_id": 1, 
  "payments": [
    {
      "payment_type": "Pix Empresa", 
      "payment_status": "Pago", 
      "value": 150.50
    }
  ]
}
```

### 📦 4. Cadastrar Produto
**POST** `http://minhadistribuidora.localhost:8000/api/v1/products`
```json
{
  "barcode": "7891010101015",
  "name": "Coca-Cola 2L",
  "sale_value": 10.50,
  "category": "Bebidas", 
  "amount": 24, 
  "purchase_id": 1, 
  "purchase_value": 7.50 
}
```

### 💵 5. Registrar Venda (Saída)
**POST** `http://minhadistribuidora.localhost:8000/api/v1/sales`
```json
{
  "discount": 0,
  "delivery_price": 0,
  "products": [
    {
      "id": 1, 
      "quantity": 2
    }
  ],
  "payments": [
    {
      "payment_type": "Dinheiro",
      "payment_status": "Pago",
      "value": 21.00
    }
  ]
}
```

> **Rotas de Leitura:** Para listar dados (como `GET http://minhadistribuidora.localhost:8000/api/v1/products`), não é necessário enviar JSON Body. Apenas coloque a URL e certifique-se de enviar o Token de Autorização.

---

## 📈 Módulo Financeiro (Contas a Pagar e Receber)

O ERP conta com um robusto sistema financeiro nativo:
- **Contas a Pagar (Accounts Payable):** Compras no fornecedor geram obrigações parceladas dinamicamente.
- **Contas a Receber (Accounts Receivable):** Vendas corporativas, fiado, e cartões de crédito geram títulos recebíveis.
- O desacoplamento dos `Payments` (baixas) permite rastrear com precisão o DRE e Fluxo de Caixa, com integração às Notificações nativas do Laravel para evitar inadimplência.

---

## 🧪 Testes Automatizados e Multi-Tenancy

O sistema possui uma suíte completa de testes automatizados de Integração e Unidade (mais de 60 testes), que validam a lógica de negócios, controle de acesso e concorrência no ambiente Multi-Tenant.

**Arquitetura de Testes Multi-Tenant:**
Para garantir que os testes rodem rapidamente e sem conflitos:
- O banco de dados central (`testing.sqlite`) é recriado a cada teste.
- Um tenant (e seu banco de dados exclusivo de testes) é criado dinamicamente no `setUp()` e limpo no `tearDown()` para simular as transações de cada loja isoladamente sem conflito de chaves estrangeiras.
- As permissões e roles são injetadas de forma encapsulada por teste.

Para executar a suíte localmente com sucesso:

```bash
# Rode usando o Laravel Sail
./vendor/bin/sail artisan test

# Ou diretamente no seu ambiente local (requer banco configurado em database/testing.sqlite)
php artisan test
```
*A suíte de testes passou por uma grande refatoração e garante a confiabilidade do isolamento das lojas.*

---

## 🤝 Como Contribuir

Este projeto segue um modelo de fluxo estruturado (GitFlow). Para contribuir:

1. Faça um **Fork** do projeto.
2. Crie uma nova branch com a sua feature: `git checkout -b feature/minha-feature`.
3. Siga o padrão de commits semânticos (ex: `feat: adiciona modulo de pagamentos`).
4. Faça o commit das suas alterações: `git commit -m 'feat: minha nova feature'`.
5. Faça o push para a sua branch: `git push origin feature/minha-feature`.
6. Abra um **Pull Request** detalhando o que foi feito.

---

## 📝 Licença

Este projeto está sob a licença [MIT](https://choosealicense.com/licenses/mit/). Sinta-se à vontade para usá-lo, modificá-lo e distribuí-lo para fins educacionais ou comerciais.
