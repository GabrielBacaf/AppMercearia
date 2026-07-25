# ADR: Endpoints e Payload do Modelo Supplier

## Status
Aceito

## Contexto
Documentação das estruturas de dados e contratos de API para o modelo **Supplier** (Fornecedor), vital para que o front-end saiba construir os formulários e consumir os dados corretamente.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/suppliers` (Autenticado via Sanctum)
- **GET** `/api/v1/suppliers` - Listar todos os fornecedores.
- **GET** `/api/v1/suppliers/{id}` - Obter dados de um fornecedor.
- **POST** `/api/v1/suppliers` - Criar um novo fornecedor.
- **PUT/PATCH** `/api/v1/suppliers/{id}` - Atualizar um fornecedor.

### Payload Esperado para Cadastro/Edição (JSON)
```json
{
  "fantasy_name": "Nome Fantasia", // Obrigatório, String, max 60
  "legal_name": "Razão Social SA", // Obrigatório, String, max 70, único
  "cnpj": "12345678000199"         // Opcional, String, max 14, único
}
```

### Formato de Retorno da API (Resource JSON)
```json
{
  "data": {
    "id": 1,
    "fantasy_name": "Nome Fantasia",
    "legal_name": "Razão Social SA",
    "cnpj": "12345678000199"
  }
}
```
