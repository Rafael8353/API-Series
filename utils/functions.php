<?php

// Nome do cookie que armazenará os dados da API
define('COOKIE_NAME', 'api_series_data');
// Tempo de expiração adequado (mínimo 1 hora)
define('COOKIE_EXPIRATION', time() + (60 * 60)); // 1 hora

/**
 * Retorna todos os registros de séries do cookie.
 * @return array
 */
function get_series_data() {
    // Tratar a leitura dos cookies com $_COOKIE
    if (isset($_COOKIE[COOKIE_NAME])) {
        // Usa json_decode() para deserializar arrays/objetos do cookie
        $data = json_decode($_COOKIE[COOKIE_NAME], true);
        return is_array($data) ? $data : [];
    }
    return [];
}

/**
 * Salva o array de séries no cookie.
 * @param array $data O array de dados a ser salvo.
 */
function save_series_data(array $data) {
    // Serializar arrays/objetos usando json_encode()
    $json_data = json_encode($data);

    // setcookie() para armazenar dados
    // O '/' garante que o cookie seja acessível em todas as rotas da sua aplicação.
    setcookie(COOKIE_NAME, $json_data, COOKIE_EXPIRATION, "/");
}

/**
 * Retorna uma resposta JSON e encerra a execução.
 * @param array $response_data O array de dados de resposta.
 * @param int $http_code O código de status HTTP (padrão 200).
 */
function send_json_response(array $response_data, int $http_code = 200) {
    // Use http_response_code() para definir o código de status
    http_response_code($http_code);
    
    // Retorno OBRIGATÓRIO em formato JSON 
    // Nunca use echo ou print fora do json_encode()
    echo json_encode($response_data);
    exit; // Garante que a execução do script pare aqui
}

/**
 * Retorna um erro com código HTTP específico.
 * @param string $message Mensagem de erro.
 * @param int $code Código HTTP (ex: 400, 404, 405).
 */
function send_error_response(string $message, int $code) {
    // Trata mensagens de erro gerais, útil para 404 e 405.
    send_json_response([
        "success" => false,
        "message" => $message,
        "error_code" => $code
    ], $code);
}
?>