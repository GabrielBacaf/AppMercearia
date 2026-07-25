# Architecture Decision Record (ADR) - 0001
## Multi-Database Tenancy Architecture para SaaS ERP

**Status:** Aceito
**Data:** 24/07/2026

### Contexto
O sistema AppMercearia está evoluindo de uma aplicação de lojista único para uma plataforma B2B SaaS (Software as a Service) multi-empresa. Nesta nova arquitetura, o administrador (Super Admin) necessita de controle total sobre o ecossistema, enquanto cada cliente (Lojista/Admin) terá o ambiente de seu respectivo ERP totalmente isolado.

Uma decisão arquitetural fundamental era como separar os dados de cada empresa (Tenant), garantindo alta segurança, isolamento da informação e facilidade de manutenção. As opções consideravam o uso de uma coluna `tenant_id` (Single Database) versus separar fisicamente o banco de dados (Multi-Database).

### Decisão
Foi escolhida a arquitetura **Multi-Database (Banco de Dados Isolado por Tenant)**, utilizando o pacote `stancl/tenancy`.

As principais decisões técnicas tomadas foram:
1. **Separação de Domínios/Subdomínios:** A API Central responderá no domínio principal (ex: `erpmercearia.test`), enquanto cada tenant terá seu ambiente servido em subdomínios (ex: `joao.erpmercearia.test`).
2. **Isolamento via Pacote (stancl/tenancy):** A gestão de inicialização do tenant ocorre via Middleware baseado no domínio da requisição HTTP (`InitializeTenancyByDomain`), que dinamicamente troca a conexão padrão do banco de dados do Laravel para o banco específico do tenant.
3. **Divisão de Migrations:**
   - As migrations de sistema e do super admin (como `users`, `tenants`, `domains`) permanecem em `database/migrations`.
   - As migrations que compõem o modelo de negócios do ERP (produtos, vendas, clientes, permissões Spatie) foram movidas para `database/migrations/tenant`. Ao criar um novo tenant, o pacote automaticamente cria um novo schema/database e executa as migrations apenas da pasta `tenant/`.
4. **Isolamento de Cache/Fila (Futuro):** A infraestrutura do `stancl/tenancy` está configurada para, caso necessário futuramente, anexar o ID do tenant a chaves de Cache (Redis) e filas (Queues), evitando sobreposição de jobs e sessões de usuários concorrentes.

### Justificativas
* **Segurança Reforçada:** O ERP lida com dados sensíveis de vendas, clientes e faturamento de diversas empresas. Um erro no código onde o desenvolvedor esquece um `where('tenant_id', ...)` na arquitetura tradicional poderia resultar no vazamento de dados de um concorrente. Com o isolamento físico via conexão (Multi-Database), o código sequer "vê" os dados das outras empresas.
* **Escalabilidade (Sharding):** A separação permite colocar bancos de dados pesados de grandes clientes em servidores de banco de dados dedicados, reduzindo gargalos sem afetar outros usuários.
* **Backup de Arquivos Específicos:** Facilitou o backup do banco de dados individual de um lojista se o mesmo desejar seus dados exportados, bem como permitiu deletar integralmente a empresa via `DROP DATABASE` quando a conta for fechada.

### Consequências
1. **Migrations complexas (Testes e Seeders):** A equipe de desenvolvimento deve tomar cuidado ao lidar com o banco de dados central e o tenant. Os testes automatizados agora devem criar dinamicamente os tenants no `setUp` para que as migrations da pasta `tenant/` existam em tempo de teste.
2. **Manutenção do Código Centralizado:** Diferente de rodar múltiplas instâncias da aplicação, a base de código é única. Uma atualização de funcionalidade é entregue para todas as lojas simultaneamente.
3. **Roles e Permissions:** O modelo do `spatie/laravel-permission` agora roda no contexto de Tenant, significando que o Super Admin não partilha permissões com o Lojista Admin, uma vez que eles habitam conexões diferentes. (Para o Super Admin atuar sobre os lojistas, a autenticação central o fará, ou o Spatie precisará de migrations sincronizadas na área central).
