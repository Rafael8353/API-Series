<?php


/**
 * Valida os dados de entrada para uma série (usado em Criar e Editar).
 * @param array $data O array de dados a ser validado.
 * @return array Um array com 'success' (bool) e 'message' (string).
 */
function validate_series_data(array $data) {
    // 1. Campos obrigatórios não podem estar vazios
    // Entidade "Séries": titulo, genero, ano_lancamento, status (4 atributos + ID)
    $required_fields = ['titulo', 'genero', 'ano_lancamento', 'status'];

    foreach ($required_fields as $field) {
        // Use isset() e empty() para validações
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            return ['success' => false, 'message' => "Erro: campo '{$field}' é obrigatório."];
        }
    }

    // 2. Tipos de dados corretos (ano_lancamento deve ser um número inteiro)
    if (!is_numeric($data['ano_lancamento']) || (int)$data['ano_lancamento'] <= 0) {
        return ['success' => false, 'message' => "Erro: campo 'ano_lancamento' deve ser um número inteiro positivo."];
    }
    
    return ['success' => true, 'message' => "Dados válidos."];
}

/**
 * Retorna um erro de validação (400 - Requisição inválida).
 * @param string $message Mensagem de erro.
 */
function validation_error(string $message) {
    send_json_response([
        "success" => false,
        "message" => $message,
        "error_code" => 400
    ], 400); // 400 - Requisição inválida (Bad Request)
}
?>
