# ADR: Endpoints e Payload do Modelo User

## Status
Aceito

## Contexto
Gerenciamento de **Users** (Usuários). O cadastro também envolve definir quais **Roles** (perfis) o usuário terá acesso.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/users` (Autenticado via Sanctum)
- **GET** `/api/v1/users` - Listar usuários.
- **GET** `/api/v1/users/{id}` - Obter dados do usuário.
- **POST** `/api/v1/users` - Criar novo usuário.
- **PUT/PATCH** `/api/v1/users/{id}` - Atualizar usuário.
- **DELETE** `/api/v1/users/{id}` - Remover usuário.

### Payload Esperado para Cadastro/Edição (JSON)
```json
{
  "name": "João da Silva",        // Obrigatório, String, max 100
  "login": "joao.silva",          // Obrigatório, String, max 50, único
  "email": "joao@email.com",      // Opcional, Email, único
  "password": "senha_segura123",  // Obrigatório no POST, string min 6
  "password_confirmation": "senha_segura123", // Requerido devido à regra 'confirmed'
  "roles": [                      // Obrigatório, Array, mín 1
    "Gerente"                     // Obrigatório passar o NOME da Role, não o ID
  ],
  "status": true                  // Opcional, Boolean
}
```

### Formato de Retorno da API (Resource JSON)
```json
{
  "data": {
    "id": 1,
    "name": "João da Silva",
    "login": "joao.silva",
    "email": "joao@email.com",
    "status": true
  }
}
```
