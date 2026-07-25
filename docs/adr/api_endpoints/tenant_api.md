# ADR: Endpoints e Payload do Modelo Tenant (Central Admin)

## Status
Aceito

## Contexto
O sistema é Multi-Tenant. O **Tenant** (Loja) é gerenciado apenas pelo Super Admin através de rotas do sistema central (domínio principal), não do sistema da loja individual.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/tenants` (Autenticado via Sanctum para Super Admin no contexto Central)
- **GET** `/api/v1/tenants` - Listar todos os tenants (Lojas).
- **GET** `/api/v1/tenants/{id}` - Detalhes do tenant e domínios.
- **POST** `/api/v1/tenants` - Criar um novo Tenant, seu DB, e o usuário Admin principal dele.
- **PUT/PATCH** `/api/v1/tenants/{id}` - Atualizar dados (se liberado).
- **DELETE** `/api/v1/tenants/{id}` - Remover loja inteira.

### Payload Esperado para Cadastro (JSON)
```json
{
  "id": "loja-do-joao",             // Obrigatório, String, único (Slug do banco de dados)
  "domain": "loja-do-joao.app.com", // Obrigatório, String, único (Subdomínio/Domínio)
  "admin_name": "João da Silva",    // Obrigatório, String, max 255
  "admin_email": "admin@joao.com",  // Obrigatório, Email
  "admin_password": "senha_forte_123" // Obrigatório, String, mín 8
}
```

### Formato de Retorno da API (Controller JSON)
O cadastro de tenant não possui Resource isolado atualmente, sendo retornado os objetos Eloquent direto.
```json
{
  "message": "Tenant criado com sucesso",
  "tenant": {
    "id": "loja-do-joao",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "domains": [
      {
        "id": 1,
        "domain": "loja-do-joao.app.com",
        "tenant_id": "loja-do-joao",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```
