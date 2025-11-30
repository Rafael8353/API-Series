<?php


// 1. Receber dados POST
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data) || !is_array($data)) {
    $data = $_POST;
}

$series_data = get_series_data(); // Carrega os dados persistidos
$id = (int)($data['id'] ?? 0);      // Pega o ID (para Editar/Remover)
$action = $data['action'] ?? null; // Pega o campo 'action' (para Remover)

// --- LÓGICA DE REMOÇÃO (DELETE/POST) ---
// Verifica se a ação é explicitamente 'delete' E tem ID
if ($action === 'delete') {

    if ($id === 0) {
        validation_error("Erro: ID é obrigatório para remover o registro.");
    }

    $found_key = -1;
    // 1. Identificar qual registro será removido (via ID)
    foreach ($series_data as $key => $series) {
        if ($series['id'] === $id) {
            $found_key = $key;
            break;
        }
    }

    // 2. ID existente ao buscar (validação)
    if ($found_key === -1) {
        send_error_response("Série com ID {$id} para remoção não foi encontrada.", 404); // 404 Not Found
    }

    unset($series_data[$found_key]); // Remove o item do array
    $series_data = array_values($series_data); // Reindexa o array para evitar chaves vazias
    save_series_data($series_data); // Salva a alteração no cookie

    send_json_response([
        "success" => true,
        "message" => "Registro removido com sucesso",
        "data" => ["id" => $id]
    ], 200); // 200 OK
}

// --- LÓGICA DE CRIAÇÃO/EDIÇÃO (CREATE/UPDATE) ---
else {
    // 1. Validação de campos obrigatórios
    $validation = validate_series_data($data);
    if (!$validation['success']) {
        validation_error($validation['message']);
    }
    
     // --- EDIÇÃO (UPDATE) ---
    if ($id > 0) {
       
        $found_key = -1;
        // 2. Identificar qual registro será editado (via ID)
        foreach ($series_data as $key => $series) {
            if ($series['id'] === $id) {
                $found_key = $key;
                break;
            }
        }

        if ($found_key === -1) {
            validation_error("Erro: Série com ID {$id} para edição não foi encontrada.");
        }

        // 3. Atualiza o registro no array
        $series_data[$found_key]['titulo'] = $data['titulo'];
        $series_data[$found_key]['genero'] = $data['genero'];
        $series_data[$found_key]['ano_lancamento'] = (int)$data['ano_lancamento'];
        $series_data[$found_key]['status'] = $data['status'];

        save_series_data($series_data); // Salva no cookie

        // 4. Retorna confirmação de atualização [cite: 109]
        send_json_response([
            "success" => true,
            "message" => "Registro atualizado com sucesso",
            "data" => $series_data[$found_key]
        ], 200); // 200 OK

    }
    // Se o ID não foi fornecido, é uma operação de CRIAÇÃO (Endpoint 2)
    else {
        // --- CRIAÇÃO (CREATE) ---
        
        // 1. Gerar um ID único para o novo registro (usando contador da sessão)
        $new_id = $_SESSION['next_series_id'];

        $new_series = [
            'id' => $new_id,
            'titulo' => $data['titulo'],
            'genero' => $data['genero'],
            'ano_lancamento' => (int)$data['ano_lancamento'],
            'status' => $data['status']
        ];

        // 2. Armazenar os dados no cookie
        $series_data[] = $new_series;
        save_series_data($series_data);

        // 3. Incrementa o contador para o próximo registro
        $_SESSION['next_series_id']++;

        // 4. Retornar confirmação de criação
        send_json_response([
            "success" => true,
            "message" => "Registro criado com sucesso",
            "data" => $new_series
        ], 201); // 201 - Criado (Created)
    }
}
?>
