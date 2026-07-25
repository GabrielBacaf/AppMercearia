# ADR: Endpoints e Payload do Modelo Role

## Status
Aceito

## Contexto
O modelo **Role** representa o perfil de acesso dos usuários. Ele vincula um perfil a várias **Permissões** do sistema.

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/roles` (Autenticado via Sanctum)
- **GET** `/api/v1/roles` - Listar perfis.
- **GET** `/api/v1/roles/{id}` - Obter dados de um perfil.
- **POST** `/api/v1/roles` - Criar um perfil e anexar permissões.
- **PUT/PATCH** `/api/v1/roles/{id}` - Atualizar um perfil.

*(Nota: a exclusão (`destroy`) está desativada no roteamento deste resource)*

### Payload Esperado para Cadastro/Edição (JSON)
```json
{
  "name": "Gerente",           // Obrigatório, String, min 3, max 50, único
  "permissions": [             // Obrigatório, Array de IDs das permissões, min 1
    1, 
    2, 
    3
  ]
}
```

### Formato de Retorno da API (Resource JSON)
```json
{
  "data": {
    "id": 1,
    "name": "Gerente"
  }
}
```
