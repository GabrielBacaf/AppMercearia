# ADR: Endpoints e Payload do Modelo AccountPayable (Contas a Pagar)

## Status
Aceito

## Contexto
Parte do novo Módulo Financeiro. Gerencia o dinheiro que *sai* do caixa da empresa (Despesas, Compras a prazo, Impostos, etc). Pode ser gerado automaticamente através de uma Compra (`Purchase`) ou inserido manualmente como uma conta avulsa (ex: Conta de Luz).

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/financial/payables` (Autenticado via Sanctum)
- **GET** `/api/v1/financial/payables` - Listar e filtrar contas a pagar.
- **POST** `/api/v1/financial/payables` - Criar uma despesa/conta avulsa manualmente.
- **POST** `/api/v1/financial/payables/{id}/settle` - Dar baixa (pagar) uma parcela/conta específica.

### Payload Esperado para Cadastro Avulso (POST /payables)
Usado para cadastrar contas que não vieram do estoque.
```json
{
  "title": "Conta de Luz - Janeiro",
  "amount": 250.00,
  "due_date": "2026-02-10",
  "type": "OPERATIONAL" // Opcional. Vem do AccountPayableTypeEnum
}
```

### Payload Esperado para Pagamento (POST /payables/{id}/settle)
Usado para registrar que você pagou aquela conta ou parcela.
```json
{
  "amount": 250.00,
  "payment_type": "PIX"
}
```

### Filtros Disponíveis no GET (Query Params)
- `?status=PENDING` (Filtra por status Financeiro)
- `?type=SUPPLIER` (Filtra por tipo de despesa)
- `?due_date=2026-07-25` (Filtra pela data de vencimento)

### Formato de Retorno da API (Exemplo no Settle)
```json
{
    "id": 45,
    "title": "Compra #10 - Parcela 2/3",
    "amount": 300.00,
    "due_date": "2026-08-25",
    "status": "RECEIVED",
    "type": "SUPPLIER",
    "payable_id": 10,
    "payable_type": "App\\Models\\Purchase"
}
```
