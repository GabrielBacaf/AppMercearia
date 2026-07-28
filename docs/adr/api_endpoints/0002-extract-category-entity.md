# 2. Extração da Categoria para Entidade no Banco de Dados

Data: 2026-07-24

## Status

Aceito

## Contexto

A categoria dos produtos estava sendo gerenciada através de um campo de texto (`varchar`) na tabela `products`, baseado no Enum `CategoryEnum`. Isso mantinha as categorias como um "texto solto e burro" no banco de dados, impossibilitando guardar informações adicionais de negócio atreladas à categoria, e causando possíveis anomalias de atualização.

A criação dessa tabela transforma a sua categoria de um "texto solto e burro" para uma entidade inteligente no banco de dados. O relacionamento será de Um para Muitos (1:N): Uma categoria possui vários produtos, e um produto pertence a uma única categoria.

## Decisão

Nós extrairemos o campo `category` em texto para uma tabela autônoma `categories` (em esquema multi-tenancy), e referenciaremos essa tabela em `products` através de uma chave estrangeira `category_id`.

No nível do código, usaremos o Eloquent ORM para mapear esse relacionamento:
- No model `Product`: `$this->belongsTo(Category::class)`
- No model `Category`: `$this->hasMany(Product::class)`

Isso nos permite fazer consultas elegantes e eficientes, como carregar uma categoria e todos os seus produtos de uma vez usando Eager Loading.

Durante a migração dos dados, usaremos o método `firstOrCreate` para garantir idempotência, convertendo as strings antigas de categoria para a nova tabela e vinculando os IDs aos produtos.

## Consequências (Vantagens)

Além de resolver o problema das "Anomalias de Atualização" (ter que alterar o texto em milhares de produtos se o nome da categoria mudar), existem três vantagens técnicas e de negócio gigantescas:

1. **Desempenho nas Buscas (Indexação)**
Quando se filtra produtos por categoria em uma tabela desnormalizada (`WHERE category = 'Materiais Elétricos'`), o banco compara strings (textos), o que é mais custoso. Ao usar o `category_id`, o banco compara números inteiros (`WHERE category_id = 3`). Inteiros são absurdamente mais rápidos de buscar, filtrar e indexar do que textos.

2. **Integridade de Relatórios**
Sem a tabela, erros de digitação de cadastro poderiam fragmentar relatórios em grupos diferentes (ex: "Eletrica", "Elétrica", "eletricas"). O `category_id` força a consistência absoluta (Foreign Key Constraint).

3. **Escalabilidade de Regras de Negócio (O grande diferencial)**
Se a categoria é só um texto na tabela de produtos, ela não pode guardar informações próprias. Ao criar a tabela `categories`, podemos adicionar colunas futuras, como `margem_lucro_padrao`.

Isso é extremamente útil na precificação inteligente. Por exemplo, a categoria "Conveniência Elétrica" pode ter uma `margem_lucro_padrao` de 60%, enquanto "Mercearia Básica" tem 20%. Ao cadastrar um novo produto, o frontend buscará os detalhes da categoria via API e, com base no preço de custo (ex: R$ 10,00), o sistema pode aplicar a margem e sugerir o preço de venda automaticamente (R$ 16,00), reduzindo o esforço manual do operador.
