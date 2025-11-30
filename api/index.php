<?php


session_start();


header('Content-Type: application/json; charset=utf-8');


require_once('../utils/functions.php');
require_once('../utils/validations.php');

if (!isset($_SESSION['next_series_id'])) {
    $_SESSION['next_series_id'] = 1;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    require_once('search.php');
}
elseif ($method === 'POST') {
    $_SESSION['last_operation_timestamp'] = time(); 
    require_once('handler.php');
}
else {
    send_error_response("Método HTTP {$method} não permitido para esta rota.", 405);
}
?>
