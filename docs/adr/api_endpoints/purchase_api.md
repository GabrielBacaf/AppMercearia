# ADR: Endpoints e Payload do Modelo Purchase

## Status
Aceito

## Contexto
O modelo **Purchase** gerencia o registro das compras feitas de fornecedores e a entrada de produtos no estoque, incluindo pagamentos e notas fiscais (documentos).

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/purchases` (Autenticado via Sanctum)
- **GET** `/api/v1/purchases` - Listar compras.
- **GET** `/api/v1/purchases/{id}` - Obter detalhes de uma compra.
- **POST** `/api/v1/purchases` - Criar (Registrar) uma nova compra.
- **PUT/PATCH** `/api/v1/purchases/{id}` - Atualizar uma compra.
- **DELETE** `/api/v1/purchases/{id}` - Remover uma compra.

### Payload Esperado para Cadastro (FormData / JSON)
Como suporte a envio de arquivos, para enviar `document_files` o frontend geralmente usará `multipart/form-data`.
```json
{
  "title": "Compra Mensal",             // Obrigatório, String, max 50
  "description": "Detalhes da compra",  // Opcional, String, max 255
  "purchase_date": "2024-05-10",        // Obrigatório, Data, menor ou igual a hoje
  "supplier_id": 1,                     // Opcional, Inteiro (ID de Supplier)
  "invoice_id": 1,                      // Opcional, Inteiro (ID de Invoice)
  
  // "status", "count_value", "user_id", "updated_by" são PROIBIDOS de enviar.
  
  "payments": [                         // Obrigatório, Array, mín 1 item
    {
      "payment_type": "BOLETO",         // Obrigatório, Enum
      "payment_status": "PENDING",      // Obrigatório, Enum
      "value": 1500.00                  // Obrigatório, Numérico, min 0.01
    }
  ],
  
  // Envio de Documentos (Opcional - via multipart/form-data idealmente)
  "document_files[]": [ /* Arquivos PDF, max 5MB cada */ ],
  "document_labels[]": [ "Nota Fiscal 1", "Comprovante" ] // O tamanho deve bater com os arquivos
}
```

### Formato de Retorno da API (Resource JSON)
```json
{
  "data": {
    "id": 1,
    "title": "Compra Mensal",
    "description": "Detalhes da compra",
    "supplier_id": 1,
    "invoice_id": null,
    "purchase_date": "2024-05-10",
    "count_value": 1500.00,
    "status": "APPROVED",
    "user_id": 1,
    "payments": [
      {
        "id": 1,
        "value": 1500.00,
        "payment_status": "PENDING",
        "payable_id": 1,
        "payment_type": "BOLETO"
      }
    ]
  }
}
```
