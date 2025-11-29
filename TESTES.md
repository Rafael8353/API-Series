# Guia Rápido de Testes - API Séries

##  Checklist de Funcionalidades Implementadas

### Requisitos Atendidos

-  **3 Endpoints implementados:**

  - GET `/api/index.php` - Listar todas as séries
  - GET `/api/index.php?id=X` - Buscar série por ID
  - POST `/api/index.php` - Criar nova série
  - POST `/api/index.php` (com `id` no body) - Editar série

-  **Cookies:**

  - Dados persistidos em cookie `api_series_data`
  - Duração de 1 hora (3600 segundos)
  - Serialização JSON

-  **Sessões PHP (3 usos):**

  1. `$_SESSION['next_series_id']` - Contador de IDs sequenciais
  2. `$_SESSION['last_operation_timestamp']` - Timestamp da última operação POST
  3. `$_SESSION['search_count']` - Contador de acessos ao endpoint GET

-  **Validações:**

  - Campos obrigatórios (nome, genero, ano, nota)
  - Tipos de dados (string, int, float)
  - Intervalos válidos (ano: 1900-2030+, nota: 0-10)

-  **Tratamento de Erros:**

  - 200 - Sucesso (GET e POST edição)
  - 201 - Criado (POST criação)
  - 400 - Requisição inválida
  - 404 - Não encontrado
  - 405 - Método não permitido
  - 500 - Erro interno

-  **Formato JSON:**
  - Todas as respostas em JSON puro
  - Headers corretos: `Content-Type: application/json`
  - Nenhum HTML retornado

---

##  Sequência de Testes Recomendada

### Passo 1: Testar GET - Listar (deve retornar vazio)

**Request:**

```
GET http://localhost/API-Series/api/index.php
```

**Resposta esperada:**

```json
{
  "success": true,
  "data": [],
  "total": 0
}
```

---

### Passo 2: Criar Série 1

**Request:**

```
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "nome": "Breaking Bad",
    "genero": "Drama",
    "ano": 2008,
    "nota": 9.5
}
```

**Resposta esperada:**

```json
{
  "success": true,
  "message": "Registro criado com sucesso",
  "data": {
    "id": 1,
    "nome": "Breaking Bad",
    "genero": "Drama",
    "ano": 2008,
    "nota": 9.5
  }
}
```

**Status Code:** 201

---

### Passo 3: Criar Série 2

**Request:**

```
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "nome": "Game of Thrones",
    "genero": "Fantasia",
    "ano": 2011,
    "nota": 9.3
}
```

**Resposta esperada:** ID 2, Status 201

---

### Passo 4: Criar Série 3

**Request:**

```
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "nome": "Stranger Things",
    "genero": "Ficção Científica",
    "ano": 2016,
    "nota": 8.7
}
```

**Resposta esperada:** ID 3, Status 201

---

### Passo 5: GET - Listar Todas (deve ter 3 séries)

**Request:**

```
GET http://localhost/API-Series/api/index.php
```

**Resposta esperada:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "Breaking Bad",
      "genero": "Drama",
      "ano": 2008,
      "nota": 9.5
    },
    {
      "id": 2,
      "nome": "Game of Thrones",
      "genero": "Fantasia",
      "ano": 2011,
      "nota": 9.3
    },
    {
      "id": 3,
      "nome": "Stranger Things",
      "genero": "Ficção Científica",
      "ano": 2016,
      "nota": 8.7
    }
  ],
  "total": 3
}
```

---

### Passo 6: GET - Buscar por ID

**Request:**

```
GET http://localhost/API-Series/api/index.php?id=2
```

**Resposta esperada:**

```json
{
  "success": true,
  "data": {
    "id": 2,
    "nome": "Game of Thrones",
    "genero": "Fantasia",
    "ano": 2011,
    "nota": 9.3
  }
}
```

---

### Passo 7: POST - Editar Série

**Request:**

```
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "id": 2,
    "nome": "Game of Thrones - Atualizado",
    "genero": "Fantasia",
    "ano": 2011,
    "nota": 9.5
}
```

**Resposta esperada:**

```json
{
  "success": true,
  "message": "Registro atualizado com sucesso",
  "data": {
    "id": 2,
    "nome": "Game of Thrones - Atualizado",
    "genero": "Fantasia",
    "ano": 2011,
    "nota": 9.5
  }
}
```

**Status Code:** 200

---

### Passo 8: Verificar Edição - GET por ID

**Request:**

```
GET http://localhost/API-Series/api/index.php?id=2
```

**Resposta esperada:** Série atualizada com novo nome e nota

---

##  Testes de Erros (Importante para a Apresentação)

### Teste 1: Criar sem campo obrigatório

**Request:**

```
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "genero": "Drama",
    "ano": 2008,
    "nota": 9.5
}
```

**Resposta esperada:**

```json
{
  "success": false,
  "message": "Erro de validação: Campo 'nome' é obrigatório",
  "error_code": 400
}
```

**Status Code:** 400

---

### Teste 2: Buscar ID inexistente

**Request:**

```
GET http://localhost/API-Series/api/index.php?id=999
```

**Resposta esperada:**

```json
{
  "success": false,
  "message": "Série com ID 999 não encontrada.",
  "error_code": 404
}
```

**Status Code:** 404

---

### Teste 3: Editar ID inexistente

**Request:**

```
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "id": 999,
    "nome": "Série Inexistente",
    "genero": "Drama",
    "ano": 2020,
    "nota": 5.0
}
```

**Resposta esperada:**

```json
{
  "success": false,
  "message": "Série com ID 999 não encontrada para edição.",
  "error_code": 404
}
```

**Status Code:** 404

---

### Teste 4: Nota inválida (> 10)

**Request:**

```
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "nome": "Série Teste",
    "genero": "Ação",
    "ano": 2020,
    "nota": 15.0
}
```

**Resposta esperada:**

```json
{
  "success": false,
  "message": "Erro de validação: Campo 'nota' deve estar entre 0 e 10",
  "error_code": 400
}
```

**Status Code:** 400

---

### Teste 5: Método não permitido

**Request:**

```
PUT http://localhost/API-Series/api/index.php
```

**Resposta esperada:**

```json
{
  "success": false,
  "message": "Método HTTP PUT não permitido para esta rota.",
  "error_code": 405
}
```

**Status Code:** 405

---

##  Como Verificar Cookies

### No Postman:

1. Após fazer requisições, vá em **Cookies** (menu ou canto inferior)
2. Selecione `localhost`
3. Procure `api_series_data`
4. Visualize o conteúdo JSON

### No Navegador (DevTools):

1. Abra DevTools (F12)
2. Aba **Application** (Chrome) ou **Storage** (Firefox)
3. **Cookies** → `http://localhost`
4. Clique em `api_series_data` para ver o JSON

---

##  Checklist para Apresentação

- [ ] Criar pelo menos 3 séries via POST
- [ ] Listar todas as séries via GET
- [ ] Buscar uma série específica por ID
- [ ] Editar uma série existente
- [ ] Demonstrar tratamento de erros (validação, 404, etc.)
- [ ] Mostrar cookies armazenados (screenshot)
- [ ] Demonstrar que dados persistem entre requisições
- [ ] Explicar uso de sessões PHP
- [ ] Explicar geração de IDs

---
