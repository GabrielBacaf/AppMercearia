# ADR: Endpoints para Vínculo de Produtos em Compras (Pivot)

## Status
Aceito

## Contexto
Necessitamos documentar a estrutura de dados (payload) esperada pela API e a forma como a API lida com a adição, atualização e remoção de produtos em uma compra já existente. Essa separação respeita o princípio de Responsabilidade Única (SRP).

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/purchases/{purchase}/products` (Autenticado via Sanctum, requer permissão de edição de compras)

- **POST** `/api/v1/purchases/{purchase}/products` - Adicionar/vincular um produto já cadastrado à compra.
- **PUT** `/api/v1/purchases/{purchase}/products/{product}` - Atualizar os dados de estoque/valor desse produto nesta compra.
- **DELETE** `/api/v1/purchases/{purchase}/products/{product}` - Remover o produto desta compra e recalcular o estoque.

### Payload Esperado para Adição (POST)
O front-end deve enviar os dados da tabela pivot (`PurchaseProductRequest`):
```json
{
  "product_id": 1,               // Obrigatório, Inteiro, existente na tabela products
  "stock_quantity": 50,          // Obrigatório, Inteiro, min 0
  "purchase_value": 12.50,       // Obrigatório, Numérico, min 0
  "expiration_date": "2027-12-31"// Opcional, Data (AAAA-MM-DD), maior ou igual hoje
}
```

### Payload Esperado para Atualização (PUT)
Não exige `product_id` pois já está na rota:
```json
{
  "stock_quantity": 60,          // Obrigatório, Inteiro, min 0
  "purchase_value": 12.50,       // Obrigatório, Numérico, min 0
  "expiration_date": "2028-01-31"// Opcional, Data
}
```

### Formato de Retorno da API
O retorno de sucesso destas operações irá retornar os dados do `ProductResource` (com o relacionamento carregado, mostrando o estoque total atualizado).
