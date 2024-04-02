<?php
 
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "agendateste");

if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => "Erro de conexão: " . $mysqli->connect_error]);
    exit;
}

$cellId = $_POST['cellId'];
$tabela = $_POST['tabela'];
// Lista de tabelas permitidas para prevenir injeção SQL
$tabelasPermitidas = ['sala1', 'sala2', 'sala3'];

// Verifica se a tabela recebida está na lista de permitidas
if (!in_array($tabela, $tabelasPermitidas)) {
    echo json_encode(['success' => false, 'message' => "Nome de tabela inválido."]);
    exit;
}

// Prepara a consulta SQL dinamicamente usando o nome da tabela validado
$query = sprintf("DELETE FROM %s WHERE cell_id = ?", $mysqli->real_escape_string($tabela));

$stmt = $mysqli->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => "Erro ao preparar a consulta: " . $mysqli->error]);
    exit;
}

$stmt->bind_param("s", $cellId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => "Erro ao remover reserva: " . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
