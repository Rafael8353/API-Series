<?php

define('COOKIE_NAME', 'api_series_data');
define('COOKIE_EXPIRATION', time() + (60 * 60)); 

function get_series_data() {
    if (isset($_COOKIE[COOKIE_NAME])) {
        $data = json_decode($_COOKIE[COOKIE_NAME], true);
        return is_array($data) ? $data : [];
    }
    return [];
}

function save_series_data(array $data) {
    $json_data = json_encode($data);
    setcookie(COOKIE_NAME, $json_data, COOKIE_EXPIRATION, "/");
}

function send_json_response(array $response_data, int $http_code = 200) {
    http_response_code($http_code);
    echo json_encode($response_data);
    exit; 
}

function send_error_response(string $message, int $code) {
    send_json_response([
        "success" => false,
        "message" => $message,
        "error_code" => $code
    ], $code);
}
?>