# 4. Arquitetura do Dashboard Financeiro e DRE

Data: 2026-07-25

## Status
Aceito

## Contexto
O sistema precisa de um relatório de fechamento de mês (DRE Simplificada) para fornecer uma visão clara da saúde financeira da mercearia/distribuidora. Lojas desse segmento vendem produtos de alto giro com margens baixas (como arroz, feijão) e itens de baixo giro com altas margens (como utilidades e itens de conveniência). 
Apenas cruzar vendas vs despesas não traz a granularidade necessária. É necessário avaliar:
- O faturamento nos Regimes de Caixa (dinheiro na conta) e Competência (obrigação/direito adquirido).
- O desempenho diário para gráficos.
- Os produtos campeões de vendas separados por volume (giro) e por lucratividade (margem).

## Decisão
1. **Service Layer:** Criação do `FinancialReportService` para orquestrar a soma de `AccountReceivable` e `AccountPayable`, agrupar despesas por categoria e calcular a saúde financeira.
2. **Duplo Regime:** A API retornará simultaneamente os indicadores sob os regimes de Caixa e Competência, evitando que o front-end precise fazer requisições separadas.
3. **Divisão de Top Produtos:** O relatório destacará duas listas separadas de "Top Produtos":
   - **Top por Volume (Giro):** O que atrai o cliente para a loja.
   - **Top por Lucratividade:** O que de fato traz dinheiro limpo para a manutenção do negócio, cruzando a receita do produto com seu custo médio (ou preço de custo).
4. **Endpoints:** A consolidação de tudo será devolvida de uma vez pelo endpoint `GET /api/v1/financial/dashboard`.

## Consequências
- **Positivas:** O gestor terá uma fotografia em tempo real da saúde da loja sem precisar de cálculos manuais ou relatórios pesados de BI de terceiros. A clara separação entre Giro e Lucratividade ajudará em estratégias de marketing.
- **Negativas:** Consultar as tabelas pivot de produtos vendidos juntamente com custos e contas a pagar exigirá queries robustas, o que pode pesar conforme o banco de dados cresce. Caching futuro poderá ser necessário.
