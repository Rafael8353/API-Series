<?php


$data = json_decode(file_get_contents('php://input'), true);
if (empty($data) || !is_array($data)) {
    $data = $_POST;
}

$series_data = get_series_data();
$id = (int)($data['id'] ?? 0);     
$action = $data['action'] ?? null; 

if ($action === 'delete') {

    if ($id === 0) {
        validation_error("Erro: ID é obrigatório para remover o registro.");
    }

    $found_key = -1;
    foreach ($series_data as $key => $series) {
        if ($series['id'] === $id) {
            $found_key = $key;
            break;
        }
    }

    if ($found_key === -1) {
        send_error_response("Série com ID {$id} para remoção não foi encontrada.", 404); // 404 Not Found
    }

    unset($series_data[$found_key]); 
    $series_data = array_values($series_data); 
    save_series_data($series_data); 

    send_json_response([
        "success" => true,
        "message" => "Registro removido com sucesso",
        "data" => ["id" => $id]
    ], 200); 
}

else {
    $validation = validate_series_data($data);
    if (!$validation['success']) {
        validation_error($validation['message']);
    }
    
    if ($id > 0) {
       
        $found_key = -1;
        foreach ($series_data as $key => $series) {
            if ($series['id'] === $id) {
                $found_key = $key;
                break;
            }
        }

        if ($found_key === -1) {
            validation_error("Erro: Série com ID {$id} para edição não foi encontrada.");
        }

        $series_data[$found_key]['titulo'] = $data['titulo'];
        $series_data[$found_key]['genero'] = $data['genero'];
        $series_data[$found_key]['ano_lancamento'] = (int)$data['ano_lancamento'];
        $series_data[$found_key]['status'] = $data['status'];

        save_series_data($series_data); // Salva no cookie

        send_json_response([
            "success" => true,
            "message" => "Registro atualizado com sucesso",
            "data" => $series_data[$found_key]
        ], 200); 

    }
    else {
        
        $new_id = $_SESSION['next_series_id'];

        $new_series = [
            'id' => $new_id,
            'titulo' => $data['titulo'],
            'genero' => $data['genero'],
            'ano_lancamento' => (int)$data['ano_lancamento'],
            'status' => $data['status']
        ];

        $series_data[] = $new_series;
        save_series_data($series_data);

        $_SESSION['next_series_id']++;

        send_json_response([
            "success" => true,
            "message" => "Registro criado com sucesso",
            "data" => $new_series
        ], 201); 
    }
}
?>
