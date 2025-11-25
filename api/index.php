<?php
// projeto-api-series/api/index.php

// 1. Iniciar sessão com session_start() [cite: 138, 230]
session_start();

// 2. Configure o Content-Type OBRIGATÓRIO (application/json) [cite: 43, 152]
header('Content-Type: application/json; charset=utf-8');

// Inclui as funções auxiliares e de validação
require_once('../utils/functions.php');
require_once('../utils/validation.php');

// 3. Gerenciamento do ID Sequencial (Uso de Sessão 1) [cite: 27]
if (!isset($_SESSION['next_series_id'])) {
    $_SESSION['next_series_id'] = 1; // Inicializa o contador
}

// Lógica de Roteamento
$method = $_SERVER['REQUEST_METHOD'];

// Endpoint 1: Buscar Dados (GET)
if ($method === 'GET') {
    require_once('search.php');
}
// Endpoint 2, 3 e 4: Criar/Editar/Remover Dados (POST)
elseif ($method === 'POST') {
    // Uso de Sessão 2: Armazenar timestamp da última operação [cite: 139]
    $_SESSION['last_operation_timestamp'] = time(); 
    require_once('handler.php');
}
// 405 - Método não permitido (Method Not Allowed) [cite: 148, 158]
else {
    send_error_response("Método HTTP {$method} não permitido para esta rota.", 405);
}
?>