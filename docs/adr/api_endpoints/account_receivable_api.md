# ADR: Endpoints e Payload do Modelo AccountReceivable (Contas a Receber)

## Status
Aceito

## Contexto
Parte do novo Módulo Financeiro. Gerencia o dinheiro que *entra* no caixa da empresa (Receitas, Vendas a prazo, etc). Pode ser gerado automaticamente através de uma Venda (`Sale`) ou inserido manualmente como uma receita avulsa.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/financial/receivables` (Autenticado via Sanctum)
- **GET** `/api/v1/financial/receivables` - Listar e filtrar contas a receber.
- **POST** `/api/v1/financial/receivables` - Criar uma receita avulsa manualmente.
- **POST** `/api/v1/financial/receivables/{id}/settle` - Dar baixa (receber o pagamento) de uma parcela/conta específica.

### Payload Esperado para Cadastro Avulso (POST /receivables)
Usado para cadastrar receitas que não vieram do PDV/Vendas.
```json
{
  "title": "Consultoria Externa",
  "amount": 1500.00,
  "due_date": "2026-08-05",
  "client_id": 2 // Opcional
}
```

### Payload Esperado para Recebimento (POST /receivables/{id}/settle)
Usado para registrar que o cliente pagou aquela parcela (fiado).
```json
{
  "amount": 150.00,
  "payment_type": "DINHEIRO"
}
```

### Filtros Disponíveis no GET (Query Params)
- `?status=OVERDUE` (Filtra por status Financeiro)
- `?client_id=2` (Filtra por cliente específico)
- `?due_date=2026-07-25` (Filtra pela data de vencimento)

### Formato de Retorno da API (Exemplo no Settle)
```json
{
    "id": 12,
    "title": "Venda #55 - Parcela 1/2",
    "amount": 150.00,
    "due_date": "2026-07-25",
    "received_date": "2026-07-25",
    "status": "RECEIVED",
    "client_id": 2,
    "receivable_id": 55,
    "receivable_type": "App\\Models\\Sale"
}
```
