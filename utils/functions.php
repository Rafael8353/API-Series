<?php
function getSeries() {
    if (isset($_COOKIE['api_series_data'])) {
        $series = json_decode($_COOKIE['api_series_data'], true);
        
        if ($series === null || !is_array($series)) {
            return [];
        }
        
        return $series;
    }
    
    return [];
}

function saveSeries($series) {
    if (!is_array($series)) {
        return false;
    }
    
    $jsonData = json_encode($series, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    $cookieSet = setcookie(
        'api_series_data',
        $jsonData,
        time() + 3600,
        '/',
        '',
        false,
        true
    );
    
    $_COOKIE['api_series_data'] = $jsonData;
    
    return $cookieSet;
}

function send_error_response($message, $code = 400) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'error_code' => $code
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function send_success_response($data, $message = null, $code = 200) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    $response = [
        'success' => true,
        'data' => $data
    ];
    
    if ($message !== null) {
        $response['message'] = $message;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
?>
