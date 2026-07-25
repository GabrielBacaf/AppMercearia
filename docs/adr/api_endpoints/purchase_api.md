# ADR: Endpoints e Payload do Modelo Purchase

## Status
Aceito (Atualizado pós Módulo Financeiro)

## Contexto
O modelo **Purchase** gerencia o registro das compras feitas de fornecedores e a entrada de produtos no estoque, incluindo anexos de notas fiscais. O fluxo financeiro (parcelamento) agora é delegado ao `AccountPayable`.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/purchases` (Autenticado via Sanctum)
- **GET** `/api/v1/purchases` - Listar compras.
- **GET** `/api/v1/purchases/{id}` - Obter detalhes de uma compra.
- **POST** `/api/v1/purchases` - Criar (Registrar) uma nova compra.
- **PUT/PATCH** `/api/v1/purchases/{id}` - Atualizar uma compra.
- **DELETE** `/api/v1/purchases/{id}` - Remover uma compra.

### Payload Esperado para Cadastro (FormData / JSON)
Para enviar arquivos (document_files), usar `multipart/form-data`.
```json
{
  "title": "Compra Mensal",             
  "description": "Detalhes da compra",  
  "purchase_date": "2026-07-25",        
  "supplier_id": 1,                     
  "invoice_id": 1,                      
  
  "installments": 3,                    // Opcional, Default 1. Em quantas vezes a compra será dividida (gera Contas a Pagar)
  
  "payments": [                         // Opcional. Útil se a compra teve uma ENTRADA ou foi PAGA A VISTA.
    {
      "payment_type": "BOLETO",         
      "payment_status": "PAID",      
      "value": 500.00                  
    }
  ],
  
  "document_files[]": [ /* Arquivos PDF */ ],
  "document_labels[]": [ "Nota Fiscal 1" ]
}
```

### Observações sobre Pagamentos e Parcelas
- **100% A Prazo:** Se você não enviar o array `payments`, o sistema gera as X parcelas pendentes e a compra fica aguardando pagamento futuro (via endpoints de `payables`).
- **Com Entrada:** Se enviar pagamentos, eles quitarão a **primeira** parcela gerada na hora.
