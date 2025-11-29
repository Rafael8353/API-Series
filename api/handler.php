<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once('../utils/functions.php');
require_once('../utils/validations.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error_response("Método HTTP {$_SERVER['REQUEST_METHOD']} não permitido. Use POST.", 405);
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response("Erro ao decodificar JSON: " . json_last_error_msg(), 400);
}

if ($data === null || !is_array($data)) {
    send_error_response("Dados inválidos. Envie um JSON válido no corpo da requisição.", 400);
}

$series = getSeries();

if (!isset($_SESSION['next_series_id'])) {
    $_SESSION['next_series_id'] = 1;
}

$isEdit = isset($data['id']) && !empty($data['id']);

if ($isEdit) {
    $id = filter_var($data['id'], FILTER_VALIDATE_INT);
    
    if ($id === false) {
        send_error_response("ID inválido. Deve ser um número inteiro.", 400);
    }
    
    $index = false;
    foreach ($series as $idx => $serie) {
        if (isset($serie['id']) && $serie['id'] == $id) {
            $index = $idx;
            break;
        }
    }
    
    if ($index === false) {
        send_error_response("Série com ID {$id} não encontrada para edição.", 404);
    }
    
    $validation = validateSeries($data);
    if (!$validation['valid']) {
        send_error_response("Erro de validação: " . implode(', ', $validation['errors']), 400);
    }
    
    $updatedSerie = [
        'id' => $id,
        'nome' => trim($data['nome']),
        'genero' => trim($data['genero']),
        'ano' => (int)$data['ano'],
        'nota' => (float)$data['nota']
    ];
    
    $series[$index] = $updatedSerie;
    
    if (!saveSeries($series)) {
        send_error_response("Erro ao salvar os dados atualizados.", 500);
    }
    
    send_success_response($updatedSerie, "Registro atualizado com sucesso", 200);
    
} else {
    $validation = validateSeries($data);
    if (!$validation['valid']) {
        send_error_response("Erro de validação: " . implode(', ', $validation['errors']), 400);
    }
    
    $nextId = $_SESSION['next_series_id'];
    if (!empty($series)) {
        $maxId = 0;
        foreach ($series as $serie) {
            if (isset($serie['id']) && $serie['id'] > $maxId) {
                $maxId = $serie['id'];
            }
        }
        $nextId = $maxId + 1;
    }
    
    $_SESSION['next_series_id'] = $nextId + 1;
    
    $newSerie = [
        'id' => $nextId,
        'nome' => trim($data['nome']),
        'genero' => trim($data['genero']),
        'ano' => (int)$data['ano'],
        'nota' => (float)$data['nota']
    ];
    
    $series[] = $newSerie;
    
    if (!saveSeries($series)) {
        send_error_response("Erro ao salvar os dados.", 500);
    }
    
    send_success_response($newSerie, "Registro criado com sucesso", 201);
}
?>
