<?php


$series_data = get_series_data();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $found_series = null;

    foreach ($series_data as $series) {
        if ($series['id'] === $id) {
            $found_series = $series;
            break;
        }
    }

    if ($found_series === null) {
        send_error_response("Série com ID {$id} não encontrada.", 404);
    }

    send_json_response([
        "success" => true,
        "data" => $found_series
    ], 200);
}

elseif (isset($_GET['genero']) && !empty($_GET['genero'])) {
    $genero_filtro = $_GET['genero'];
    $filtered_series = [];

    foreach ($series_data as $series) {
        if (strtolower($series['genero']) === strtolower($genero_filtro)) {
            $filtered_series[] = $series;
        }
    }

    
    send_json_response([
        "success" => true,
        "data" => $filtered_series,
        "total" => count($filtered_series)
    ], 200);
}

else {
    send_json_response([
        "success" => true,
        "data" => $series_data,
        "total" => count($series_data)
    ], 200);
}
?>
