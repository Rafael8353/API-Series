<?php

function validate_series_data(array $data) {
    $required_fields = ['titulo', 'genero', 'ano_lancamento', 'status'];

    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            return ['success' => false, 'message' => "Erro: campo '{$field}' é obrigatório."];
        }
    }

    if (!is_numeric($data['ano_lancamento']) || (int)$data['ano_lancamento'] <= 0) {
        return ['success' => false, 'message' => "Erro: campo 'ano_lancamento' deve ser um número inteiro positivo."];
    }
    
    return ['success' => true, 'message' => "Dados válidos."];
}

function validation_error(string $message) {
    send_json_response([
        "success" => false,
        "message" => $message,
        "error_code" => 400
    ], 400); 
}
?>