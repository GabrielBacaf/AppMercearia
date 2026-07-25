# ADR: Endpoints e Payload do Modelo Sale

## Status
Aceito

## Contexto
Para registrar uma venda, o payload é complexo pois engloba produtos e pagamentos. Esta documentação orienta a equipe de front-end em como formatar o JSON de envio e o que esperar de volta da API para o modelo **Sale**.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/sales` (Autenticado via Sanctum)
- **GET** `/api/v1/sales` - Listar vendas.
- **GET** `/api/v1/sales/{id}` - Ver os detalhes de uma venda.
- **POST** `/api/v1/sales` - Registrar uma nova venda.
- **PUT/PATCH** `/api/v1/sales/{id}` - Atualizar dados da venda.
- **DELETE** `/api/v1/sales/{id}` - Cancelar/remover venda.

### Payload Esperado para Cadastro (JSON)
Campos como `user_id`, `updated_by` e `total_value` são **proibidos** de serem enviados, o backend calcula ou insere via token.
```json
{
  "discount": 5.0,              // Opcional, Numérico, min 0, max 10
  "delivery_price": 15.0,       // Opcional, Numérico, min 0
  "client_id": 2,               // Opcional, Inteiro (ID de Client válido)
  
  "products": [                 // Obrigatório, Array, mín 1 item
    {
      "id": 1,                  // Opcional (se não enviar pode dar erro, ideal enviar ID do Product)
      "quantity": 2             // Obrigatório, Inteiro, min 1
    }
  ],
  
  "payments": [                 // Obrigatório, Array, mín 1 item
    {
      "payment_type": "PIX",    // Obrigatório, Enum (PaymentTypeEnum)
      "payment_status": "PAID", // Obrigatório, Enum (PaymentStatusEnum)
      "value": 100.50           // Obrigatório, Numérico, min 0.01
    }
  ]
}
```

### Formato de Retorno da API (Resource JSON)
O retorno carrega relacionalmente os produtos e pagamentos.
```json
{
  "data": {
    "id": 1,
    "discount": 5.0,
    "delivery_price": 15.0,
    "user_id": 1,
    "updated_by": null,
    "client_id": 2,
    "total_value": 100.50,
    "products": [
      {
        "id": 1,
        "barcode": "1234567890",
        "name": "Nome do Produto",
        "expiration_date": "2027-12-31",
        "current_stock": 8,
        "quantity_sold": 2,
        "sale_value": 50.25
      }
    ],
    "payments": [
      {
        "id": 1,
        "value": 100.50,
        "payment_status": "PAID",
        "payable_id": 1,
        "payment_type": "PIX"
      }
    ]
  }
}
```
