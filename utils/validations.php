<?php
function validateSeries($data) {
    $errors = [];
    
    if (!isset($data['nome']) || empty(trim($data['nome']))) {
        $errors[] = "Campo 'nome' é obrigatório";
    } elseif (!is_string($data['nome'])) {
        $errors[] = "Campo 'nome' deve ser uma string";
    }
    
    if (!isset($data['genero']) || empty(trim($data['genero']))) {
        $errors[] = "Campo 'genero' é obrigatório";
    } elseif (!is_string($data['genero'])) {
        $errors[] = "Campo 'genero' deve ser uma string";
    }
    
    if (!isset($data['ano'])) {
        $errors[] = "Campo 'ano' é obrigatório";
    } elseif (!is_numeric($data['ano'])) {
        $errors[] = "Campo 'ano' deve ser um número";
    } else {
        $ano = (int)$data['ano'];
        if ($ano < 1900 || $ano > (date('Y') + 10)) {
            $errors[] = "Campo 'ano' deve estar entre 1900 e " . (date('Y') + 10);
        }
    }
    
    if (!isset($data['nota'])) {
        $errors[] = "Campo 'nota' é obrigatório";
    } elseif (!is_numeric($data['nota'])) {
        $errors[] = "Campo 'nota' deve ser um número";
    } else {
        $nota = (float)$data['nota'];
        if ($nota < 0 || $nota > 10) {
            $errors[] = "Campo 'nota' deve estar entre 0 e 10";
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

function findSeriesById($series, $id) {
    foreach ($series as $serie) {
        if (isset($serie['id']) && $serie['id'] == $id) {
            return $serie;
        }
    }
    return null;
}

function findSeriesIndexById($series, $id) {
    foreach ($series as $index => $serie) {
        if (isset($serie['id']) && $serie['id'] == $id) {
            return $index;
        }
    }
    return false;
}
?>
