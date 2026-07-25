# 4. Engine de Notificações de Vencimento

Data: 2026-07-25

## Status
Aceito

## Contexto
Com a criação do módulo de Contas a Pagar e Receber (`AccountPayable` e `AccountReceivable`), surgiu a necessidade de alertar os usuários do sistema sobre a aproximação e o atraso dos vencimentos. O sistema precisa garantir que o gestor saiba quando uma conta vence amanhã, quando vence hoje e quando ela entra em inadimplência (atrasada).

## Decisão
1. **Notifications Channel:** Foi decidido utilizar o canal `database` nativo do Laravel para armazenar os alertas. As notificações ficam salvas na tabela `notifications` de cada Tenant, facilitando a exibição em um "sino de notificações" no painel do front-end.
2. **Classes de Notificação:** Foram criadas classes específicas para granularidade dos avisos:
   - `PaymentDueTomorrowNotification` / `ReceivableDueTomorrowNotification`
   - `PaymentDueTodayNotification` / `ReceivableDueTodayNotification`
   - `PaymentOverdueNotification` / `ReceivableOverdueNotification`
3. **Comando de Varredura (Cron):** A lógica não roda baseada em eventos (Listeners), pois a passagem do tempo não dispara um evento web. Foi criado o comando de console `CheckOverdueAccountsCommand`, que encapsula a lógica de verificação.
4. **Clean Code (Match Expression):** O comando utiliza recursos do PHP 8 (`match`) e a API do Carbon (`isTomorrow`, `isToday`, etc.) para resolver o estado temporal da conta e engatilhar a notificação apropriada, evitando aninhamentos profundos de condicionais (`if/else`).
5. **Agendamento (Scheduler):** O comando foi registrado no `routes/console.php` para rodar diariamente (`dailyAt('00:00')`) utilizando o escopo `tenants:run` do pacote *stancl/tenancy*.

## Consequências
- **Positivas:** Processamento assíncrono e passivo em background; código altamente legível e segregado por responsabilidade temporal; suporte multi-tenant nativo no disparo.
- **Negativas:** Exige que a infraestrutura do servidor tenha o *cron* ativado (`php artisan schedule:run`) para funcionar; contas criadas e pagas no mesmo dia não geram alertas (o que é desejável, mas requer compreensão do ciclo de vida).
