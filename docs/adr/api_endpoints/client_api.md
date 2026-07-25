# ADR: Endpoints e Payload do Modelo Client

## Status
Aceito

## Contexto
Necessitamos documentar a estrutura de dados (payload) esperada pela API e a forma como a API retorna os dados do modelo **Client** para auxiliar o front-end na integração correta (consumo da API).

## Decisão

### Endpoints (Rotas)
Base URL: `/api/v1/clients` (Autenticado via Sanctum)
- **GET** `/api/v1/clients` - Listar todos os clientes.
- **GET** `/api/v1/clients/{id}` - Obter os detalhes de um cliente.
- **POST** `/api/v1/clients` - Criar um novo cliente.
- **PUT/PATCH** `/api/v1/clients/{id}` - Atualizar um cliente existente.

### Payload Esperado para Cadastro/Edição (JSON)
O front-end deve enviar os dados no seguinte formato JSON:
```json
{
  "name": "Nome do Cliente",      // Obrigatório, String, máx 255
  "email": "cliente@email.com",   // Opcional, String, formato email único
  "phone": "11999999999",         // Opcional, String, único
  "address": {                    // Opcional, Objeto
    "street": "Rua Exemplo",
    "number": "123",
    "complement": "Apto 1",
    "city": "São Paulo",
    "state": "SP",
    "postal_code": "01000-000",
    "country": "Brasil",
    "latitude": -23.55052,
    "longitude": -46.633308
  }
}
```

### Formato de Retorno da API (Resource JSON)
A API retornará os dados encapsulados usando o `ClientResource`:
```json
{
  "data": {
    "id": 1,
    "name": "Nome do Cliente",
    "email": "cliente@email.com",
    "phone": "11999999999",
    "address": {
      "street": "Rua Exemplo",
      "number": "123",
      "complement": "Apto 1",
      "city": "São Paulo",
      "state": "SP",
      "postal_code": "01000-000",
      "country": "Brasil",
      "latitude": -23.55052,
      "longitude": -46.633308
    }
  }
}
```
