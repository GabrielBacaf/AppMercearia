# ADR 0005: Product Rich Domain Model e Encapsulamento de Regras de Negócio

## Status
Aceito

## Contexto
Durante o desenvolvimento do módulo de Vendas e Controle de Estoque da aplicação `AppMercearia`, nos deparamos com o desafio de onde alocar a lógica matemática e as validações para a dedução de estoque dos Produtos.

Inicialmente, a tendência natural (comum na arquitetura MVC padrão) seria colocar toda essa responsabilidade matemática no `ProductService` ou no `SaleService` (Modelo Anêmico). Isso geraria os seguintes problemas:
1. **Fuga de Responsabilidade**: O Service teria que conhecer o estado interno da Model e fazer cálculos que deveriam ser inerentes à própria Model.
2. **Duplicação e Espaguete (ifs)**: O Service precisaria testar o estoque usando blocos condicionais grandes (`if ($product->stock_quantity < $requested)`) antes de salvar, gerando sujeira visual.
3. **Respostas HTTP Genéricas**: As falhas resultariam num Erro 500 ou necessitariam de blocos `try/catch` manuais no Controller, violando o princípio "Bubble Up" de exceções.

## Decisão
Foi decidido adotar os princípios Táticos do **Domain-Driven Design (DDD)** para a entidade `Product`, implementando a abordagem de **Modelo Rico (Rich Domain Model)** associado à delegação de Exceções de Domínio Personalizadas (Custom Domain Exceptions).

### 1. Rich Domain Model (Tell, Don't Ask)
A classe `Product` (Model) deixou de ser apenas um mapeamento de banco de dados (Anemic Model) para se tornar a "Dona" do seu próprio estado (estoque).
Criamos o método `$product->deductStock(int $quantity)` dentro da Model. O Service agora atua apenas como um "Orquestrador", limitando-se a *mandar* o produto descontar o estoque.

### 2. Tratamento por Custom Exceptions (Domain Layer)
Para eliminar os múltiplos `ifs`, foram criadas classes de Exceção específicas de domínio organizadas na pasta `app/Exceptions/Product/`:
- `ProductNotFoundException` (Lançada pelo Service na camada de Infraestrutura quando o ID não existe).
- `InsufficientStockException` (Lançada pela Model na camada de Domínio quando não há estoque suficiente).

O Laravel gerencia automaticamente essas exceções através do método `render()` implementado em suas classes, retornando códigos HTTP semânticos (ex: `422 Unprocessable Entity` e `404 Not Found`) contendo os detalhes do erro em formato JSON estruturado, sem a necessidade de blocos `try/catch` espalhados pelo código.

### 3. Query Scopes (Isolamento de Banco de Dados)
A regra de banco de dados `whereIn()->lockForUpdate()` (Prevenção de Condições de Corrida - Race Conditions) foi encapsulada no método estático `scopeLockedByIds` dentro da Model `Product`. Isso impede que o Service misture regras de domínio com regras de Query Builder.

## Consequências

### Positivas
- Código limpo, testável e puramente aderente aos princípios SOLID e Clean Code.
- A model de produto garante sua própria integridade, impossibilitando que desenvolvedores externos façam deduções que resultem em estoque negativo sem querer.
- Padronização no tratamento e retorno de mensagens de erro JSON para a aplicação cliente (Front-End/Mobile).

### Negativas
- Leve curva de aprendizado inicial para novos desenvolvedores que estão acostumados apenas com tutoriais básicos de MVC (Modelos Anêmicos) e que não têm familiaridade com padrões DDD (Tell, Don't Ask) ou Query Scopes.
