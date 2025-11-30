<?php


session_start();


header('Content-Type: application/json; charset=utf-8');


require_once('../utils/functions.php');
require_once('../utils/validations.php');

// Gerenciamento do ID Sequencial (Uso de Sessão 1)
if (!isset($_SESSION['next_series_id'])) {
    $_SESSION['next_series_id'] = 1;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    require_once('search.php');
}
elseif ($method === 'POST') {
    // Uso de Sessão 2: Armazenar timestamp da última operação
    $_SESSION['last_operation_timestamp'] = time(); 
    require_once('handler.php');
}
// 405 - Método não permitido (Method Not Allowed)
else {
    send_error_response("Método HTTP {$method} não permitido para esta rota.", 405);
}
?>
