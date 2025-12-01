# 🎬 API Simples de Séries em PHP (CRUD via Cookie)

Este projeto implementa uma API RESTful simples para o gerenciamento de registros de séries. É um projeto de demonstração que utiliza **Cookies** e **Sessões PHP** para persistência e controle de IDs, em vez de um banco de dados tradicional.

---

## ⚙️ 1. Especificações Técnicas e Persistência

### 1.1. Estrutura do Projeto

A arquitetura do projeto segue um padrão modular, separando a lógica de roteamento, tratamento de dados e validação. Tendo uma pasta api, contendo index.php, handler.php e search.php. E uma pasta utils, contendo functions.php e validations.php

### 1.2. Uso de Cookies (Persistência de Dados)

Os dados são armazenados no lado do cliente via cookie.

* **Nome do Cookie:** `api_series_data`
* **Conteúdo:** Array de objetos de séries serializado em formato JSON.
* **Duração:** O cookie expira após **1 hora (3600 segundos)**.

### 1.3. Uso de Sessões PHP

As sessões são usadas para manter o estado do contador de ID e registrar a atividade:

* **`$_SESSION['next_series_id']`**: Contador que garante que cada nova série receba um ID sequencial único (mesmo após exclusões).
* **`$_SESSION['last_operation_timestamp']`**: Timestamp da última operação `POST` (Escrita).
* **`$_SESSION['search_count']`**: Contador de acessos ao endpoint `GET` (Leitura).

---

## 🗺️ 2. Endpoints Implementados

A API utiliza apenas uma URL base (`/api/index.php`), porém utiliza 3 endpoints (Criar, Editar e Buscar/Listar) e roteia as ações com base no Método HTTP e nos dados fornecidos.

| Método | URL Base | Parâmetro/Corpo | Funcionalidade | Status de Sucesso |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/api/index.php` | N/A | Listar todas as séries. | 200 OK |
| **GET** | `/api/index.php` | `?id=X` (Query String) | Buscar série por ID. | 200 OK |
| **POST** | `/api/index.php` | Dados JSON (Sem `id`) | Criar nova série. | 201 Created |
| **POST** | `/api/index.php` | Dados JSON (Com `id`) | Editar série existente. | 200 OK |


## 🧪 3. Guia Completo de Testes (REST Client)

O guia a seguir utiliza o formato `.http` para ser executado diretamente com a extensão **REST Client** do VS Code ou com ferramentas como Postman.

**Base URL:** `http://localhost/API-Series/api/index.php`

### 1. Funcionalidade Básica (CRUD)

| Endpoint | Método | Descrição |
| :--- | :--- | :--- |
| `/api/index.php` | `GET` | Lista todas as séries. |
| `/api/index.php?id=X` | `GET` | Busca série por ID. |
| `/api/index.php` | `POST` | Cria **OU** Edita série (se `id` estiver no corpo). |

```http
### Teste 1: GET - Listar Todas (Deve retornar vazio no início)
GET http://localhost/API-Series/api/index.php

### Teste 2a: POST - Criar Série 1 (Breaking Bad)
# Esperado: Status 201 Created. ID sequencial (e.g., 1).
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "titulo": "Breaking Bad",
    "genero": "Drama",
    "ano_lancamento": 2008,
    "status": "Finalizada"
}

### Teste 2b: POST - Criar Série 2 (Game of Thrones)
# Esperado: Status 201 Created. ID sequencial (e.g., 2).
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "titulo": "Game of Thrones",
    "genero": "Fantasia",
    "ano_lancamento": 2011,
    "status": "Finalizada"
}

### Teste 3: GET - Buscar Série por ID (ID 2)
# Esperado: Status 200 OK. Retornar os dados da série com ID 2.
GET http://localhost/API-Series/api/index.php?id=2

### Teste 4: POST - Editar Série (ID 2)
# Enviamos o ID no corpo para indicar que é uma edição.
# Esperado: Status 200 OK. Título e Gênero atualizados.
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "id": 2,
    "titulo": "House of the Dragon",
    "genero": "Fantasia Épica",
    "ano_lancamento": 2022,
    "status": "Em Exibição"
}

### Teste 5: POST - Remover Série (Delete)
# Enviamos o ID e a ação "delete" para acionar a lógica de remoção.
# Esperado: Status 200 OK. Mensagem de "Registro removido com sucesso".
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "id": 2, 
    "action": "delete"
}

### Teste Erro 1: Criar sem Campo Obrigatório (Faltando 'titulo')
# Esperado: Status 400 Bad Request. Mensagem sobre campo 'titulo' obrigatório.
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "genero": "Drama",
    "ano_lancamento": 2008,
    "status": "Finalizada"
}

### Teste Erro 2: Ano Inválido (Não-numérico)
# Esperado: Status 400 Bad Request. Mensagem sobre 'ano_lancamento' ser inteiro.
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "titulo": "Série Teste",
    "genero": "Ação",
    "ano_lancamento": "texto",
    "status": "Em Exibição"
}

### Teste Erro 3: Buscar ID inexistente
# Esperado: Status 404 Not Found. Mensagem sobre ID 999 não encontrado.
GET http://localhost/API-Series/api/index.php?id=999

### Teste Erro 4: Editar ID inexistente
# Esperado: Status 404 Not Found. Mensagem: "Série com ID 999 para edição não foi encontrada."
POST http://localhost/API-Series/api/index.php
Content-Type: application/json

{
    "id": 999,
    "titulo": "Série Inexistente",
    "genero": "Drama",
    "ano_lancamento": 2020,
    "status": "Pendente"
}

### Teste Erro 5: Método não permitido (PUT)
# Esperado: Status 405 Method Not Allowed. Mensagem sobre o método PUT.
PUT http://localhost/API-Series/api/index.php
