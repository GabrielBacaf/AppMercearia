# ADR: Endpoints e Payload do Modelo Product

## Status
Aceito

## Contexto
Necessitamos documentar a estrutura de dados (payload) esperada pela API e a forma como a API retorna os dados do modelo **Product** para auxiliar o front-end na integração correta.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/products` (Autenticado via Sanctum)
- **GET** `/api/v1/products` - Listar todos os produtos.
- **GET** `/api/v1/products/{id}` - Obter os detalhes de um produto.
- **POST** `/api/v1/products` - Criar um novo produto.
- **PUT/PATCH** `/api/v1/products/{id}` - Atualizar um produto existente.
- **DELETE** `/api/v1/products/{id}` - Remover um produto.

### Payload Esperado para Cadastro (JSON)
O front-end deve enviar os dados no seguinte formato JSON (conforme `StoreProductRequest`):
```json
{
  "barcode": "12345678901234",    // Obrigatório, String, max 14 (Único)
  "name": "Nome do Produto",      // Obrigatório, String, max 255 (Único)
  "expiration_date": "2027-12-31",// Opcional, Data (AAAA-MM-DD), maior ou igual hoje
  "sale_value": 150.50,           // Obrigatório, Numérico, min 0
  "category": "nome_categoria",   // Obrigatório, Enum (CategoryEnum)
  "amount": 10,                   // Obrigatório, Inteiro, min 0
  "purchase_id": 1,               // Obrigatório, Inteiro, existente na tabela purchases
  "purchase_value": 100.00        // Obrigatório, Numérico
  // "stock_quantity": Proibido ser enviado via request.
}
```

### Formato de Retorno da API (Resource JSON)
A API retornará os dados formatados usando o `ProductResource`:
```json
{
  "data": {
    "id": 1,
    "barcode": "12345678901234",
    "name": "Nome do Produto",
    "expiration_date": "2027-12-31",
    "sale_value": 150.50,
    "category": "nome_categoria",
    "stock_quantity": 10
  }
}
```
