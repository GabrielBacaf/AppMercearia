# ADR: Endpoints e Payload do Modelo Permission

## Status
Aceito

## Contexto
O modelo **Permission** serve apenas para leitura de sistema. Utilizado para preencher selects e checkboxes no frontend na hora de criar um **Role**.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/permissions` (Autenticado via Sanctum)
- **GET** `/api/v1/permissions` - Listar todas as permissões cadastradas no sistema.

### Payload Esperado (JSON)
*(Não possui método POST/PUT, apenas GET)*

### Formato de Retorno da API (Resource JSON)
```json
{
  "data": [
    {
      "id": 1,
      "name": "product_store"
    },
    {
      "id": 2,
      "name": "product_index"
    }
  ]
}
```
