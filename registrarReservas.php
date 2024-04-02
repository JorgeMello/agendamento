<?php

 
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "agendateste");

if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => "Erro de conexão: " . $mysqli->connect_error]);
    exit;
}

$cellId = $_POST['cellId'];
$nome = $_POST['nome'];
$cor = $_POST['cor'];
$tabela = $_POST['tabela'];
// Lista de tabelas válidas para prevenir injeção SQL
$tabelasValidas = ['sala1', 'sala2', 'sala3'];

// Verifica se a tabela é válida
if (in_array($tabela, $tabelasValidas)) {
    // Prepara a query usando a variável $tabela
    // ATENÇÃO: Isto só é seguro porque estamos garantindo que o valor de $tabela é controlado e validado
    $query = sprintf("INSERT INTO %s (cell_id, nome, cor) VALUES (?, ?, ?)", $tabela);

    // Prepare a consulta com o mysqli
    if ($stmt = $mysqli->prepare($query)) {
        // Vincula os parâmetros (exemplo: 'sss' indica que os três valores são strings)
        $stmt->bind_param('sss', $cellId, $nome, $cor);

// Defina os valores de $cellId, $nome, $cor

       // Executa a consulta
if ($stmt->execute()) {
    // Alterado para retornar uma resposta JSON
    echo json_encode(['success' => true, 'message' => 'Inserção bem-sucedida.']);
} else {
    // Alterado para retornar uma resposta JSON
    echo json_encode(['success' => false, 'message' => 'Erro ao inserir: ' . $mysqli->error]);
}

$stmt->close(); // Fecha o statement
    } else {
    // Alterado para retornar uma resposta JSON
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $mysqli->error]);
}
} else {
    // Alterado para retornar uma resposta JSON
    echo json_encode(['success' => false, 'message' => 'Nome da tabela inválido.']);
}

$mysqli->close(); // Fecha a conexão
?>
