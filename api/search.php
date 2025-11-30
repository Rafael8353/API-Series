<?php


// Carrega os dados persistidos no cookie
$series_data = get_series_data();

// Verifica se foi passado um ID na query string (ex: /api/series?id=1)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $found_series = null;

    // Busca o registro específico por ID
    foreach ($series_data as $series) {
        if ($series['id'] === $id) {
            $found_series = $series;
            break;
        }
    }

    // 404 - Não encontrado (Not Found)
    if ($found_series === null) {
        send_error_response("Série com ID {$id} não encontrada.", 404);
    }

    // 200 - Sucesso (OK)
    send_json_response([
        "success" => true,
        "data" => $found_series
    ], 200);
}

// Verifica se foi passado um critério de filtro (ex: /api/series?genero=Acao)
elseif (isset($_GET['genero']) && !empty($_GET['genero'])) {
    $genero_filtro = $_GET['genero'];
    $filtered_series = [];

    // Filtra os registros
    foreach ($series_data as $series) {
        if (strtolower($series['genero']) === strtolower($genero_filtro)) {
            $filtered_series[] = $series;
        }
    }

    // 200 - Sucesso (OK)
    send_json_response([
        "success" => true,
        "data" => $filtered_series,
        "total" => count($filtered_series)
    ], 200);
}

// Listar todos os registros (padrão)
else {
    // 200 - Sucesso (OK)
    send_json_response([
        "success" => true,
        "data" => $series_data,
        "total" => count($series_data)
    ], 200);
}
?>
