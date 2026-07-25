# ADR: Endpoints e Payload do Modelo Sale

## Status
Aceito (Atualizado pós Módulo Financeiro)

## Contexto
Para registrar uma venda, o payload engloba os produtos vendidos. O sistema agora delega automaticamente as parcelas e cobranças a prazo para o módulo `AccountReceivable`.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/sales` (Autenticado via Sanctum)
- **GET** `/api/v1/sales` - Listar vendas.
- **GET** `/api/v1/sales/{id}` - Ver os detalhes de uma venda.
- **POST** `/api/v1/sales` - Registrar uma nova venda.
- **PUT/PATCH** `/api/v1/sales/{id}` - Atualizar dados da venda.
- **DELETE** `/api/v1/sales/{id}` - Cancelar/remover venda.

### Payload Esperado para Cadastro (JSON)
Campos de sistema como `user_id` e `total_value` não devem ser enviados.
```json
{
  "discount": 5.0,              // Opcional
  "delivery_price": 15.0,       // Opcional
  "client_id": 2,               // Opcional (Obrigatório se a venda for fiado/a prazo longo)
  
  "installments": 2,            // Opcional, Default 1. Divide o total da venda em N Contas a Receber.
  
  "products": [                 // Obrigatório
    {
      "id": 1,                  
      "quantity": 2             
    }
  ],
  
  "payments": [                 // Opcional. Útil para pagar a primeira parcela na hora (entrada) ou liquidar venda à vista.
    {
      "payment_type": "PIX",    
      "payment_status": "PAID", 
      "value": 100.50           
    }
  ]
}
```

### Observações de Fluxo
- Se a venda for 100% fiado, **omita** o array `payments`. O sistema criará as faturas como `PENDING` nas Contas a Receber.
- Para pagar as faturas pendentes dos meses seguintes, o front-end deve acionar a rota do financeiro (`POST /api/v1/financial/receivables/{id}/settle`).
