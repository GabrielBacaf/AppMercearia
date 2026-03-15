<div align="center">
  <img src="https://img.shields.io/badge/Maintained%3F-yes-green.svg?style=for-the-badge" alt="Maintained Badge"/>
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php" alt="PHP Badge"/>
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Badge"/>
  <img src="https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker" alt="Docker Badge"/>
  <img src="https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge" alt="License Badge"/>
</div>

<br>

# 🛒 AppMercearia API

> **O que faz:** Uma API RESTful robusta desenvolvida para gerenciar as operações diárias de uma mercearia (estoque, clientes, compras e permissões).
> 
> **Com o que foi construído:** Desenvolvido com Laravel 12, PHP 8.2 e MySQL 8.4, rodando em um ambiente 100% isolado com Docker (Laravel Sail).
> 
> **Por que foi construído:** Este projeto nasceu como um objeto prático de estudo focado no ecossistema Laravel. O objetivo principal é dominar a criação e documentação de uma API RESTful do zero, aplicando conceitos avançados de arquitetura e segurança, com o propósito real de implementar o sistema para modernizar e automatizar a gestão da mercearia da família.

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

## 📖 Instruções de Uso

A API está disponível em `http://localhost`. Todas as requisições (exceto login) exigem um token Bearer retornado pelo Sanctum.

**Exemplo de Endpoint de Produtos (GET `/api/v1/products`)**

*Request:*
```http
GET /api/v1/products HTTP/1.1
Host: localhost
Accept: application/json
Authorization: Bearer {seu_token_aqui}
```

*Response:*
```json
{
  "data": [
    {
      "id": 1,
      "name": "Arroz 5kg",
      "price": 25.90,
      "stock_quantity": 50,
      "supplier_id": 3
    }
  ]
}
```

> **Dica:** Para testar a aplicação de forma visual, recomendamos a utilização do **Postman** ou importar a coleção na nossa documentação interativa do Swagger.

---

## 🧪 Testes Automatizados

Para garantir que novas implementações não quebrem as regras de negócio do estoque ou clientes, execute a suíte de testes do PHPUnit:

```bash
./vendor/bin/sail artisan test
```
*O repositório possui integração contínua (GitHub Actions) que executa os testes automaticamente a cada Pull Request.*

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

---

## ✨ Owners / Contribuidores

Pessoas que construíram e mantêm a arquitetura deste projeto:

| [<img loading="lazy" src="https://github.com/GabrielBacaf.png" width=115><br><sub>Gabriel Baca</sub>](https://github.com/GabrielBacaf) |
| :---: |
| Desenvolvedor Backend<br>Engenharia de Computação |
