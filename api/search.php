<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once('../utils/functions.php');

if (!isset($_SESSION['search_count'])) {
    $_SESSION['search_count'] = 0;
}
$_SESSION['search_count']++;

$series = getSeries();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    
    if ($id === false) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "ID inválido. Deve ser um número inteiro.",
            'error_code' => 400
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    $foundSerie = null;
    foreach ($series as $serie) {
        if (isset($serie['id']) && $serie['id'] == $id) {
            $foundSerie = $serie;
            break;
        }
    }
    
    if ($foundSerie === null) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => "Série com ID {$id} não encontrada.",
            'error_code' => 404
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $foundSerie
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $series,
    'total' => count($series)
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>
