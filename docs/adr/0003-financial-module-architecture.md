# 3. Módulo Financeiro e Arquitetura de Pagamentos

Data: 2026-07-25

## Status
Aceito

## Contexto
O sistema possuía uma tabela de pagamentos (`payments`) acoplada diretamente aos fluxos de Vendas (`Sale`) e Compras (`Purchase`). Quando essas transações geravam obrigações futuras (vendas fiado/cartão ou compras a prazo com o fornecedor), o sistema não conseguia rastrear essas obrigações em um modelo contábil de contas a pagar/receber (DRE).

Isso impedia o disparo de notificações precisas sobre vencimentos, especialmente quando uma mesma venda ou compra era dividida em múltiplas parcelas. O uso de Observers (ex: `PurchaseObserver`) também dificultava a captura de parâmetros externos (como número de parcelas) no momento da criação do título, quebrando a transparência da regra de negócio.

## Decisão
1. **Contas a Pagar e Receber:** Foram criados os modelos e tabelas `AccountPayable` e `AccountReceivable` em contexto Tenant.
2. **Desacoplamento de Pagamentos:** A tabela de pagamentos agora é vinculada através de relação polimórfica (`payable_id` / `payable_type`) exclusivamente aos Títulos Financeiros (`AccountPayable` / `AccountReceivable`), e não à compra/venda de origem.
3. **Serviços de Orquestração:** A criação e o parcelamento dos títulos financeiros passam a ser responsabilidade da camada de Service (`ProcessPurchaseService` e `CheckoutSaleService`), abandonando o uso de Observers para esta tarefa.
4. **Notificações:** O controle de inadimplência fará varreduras nestes títulos para notificar via Tabela Nativa de Notificações (`database/migrations/tenant`).

## Consequências
- **Positivas:** Fluxo de caixa fiel à contabilidade real de ERPs; suporte nativo a múltiplas parcelas de um mesmo pedido; código mais testável através da injeção de dependência via Services.
- **Negativas:** Maior complexidade nas consultas, pois exigirá a navegação de Pagamento -> Título -> Origem (Purchase/Sale) para consolidação de relatórios.
