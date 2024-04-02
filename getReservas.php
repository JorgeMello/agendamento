<?php
 
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "agendateste");

if ($mysqli->connect_error) {
    echo json_encode(['error' => true, 'message' => "Erro de conexão: " . $mysqli->connect_error]);
    exit;
}

header('Content-Type: application/json');
$dados = json_decode(file_get_contents('php://input'), true); // Decodifica o JSON enviado
$tabela = $dados['tabela'];

// Lista de tabelas válidas para prevenir injeção SQL
$tabelasValidas = ['sala1', 'sala2', 'sala3'];

// Verifica se a tabela recebida está na lista de tabelas válidas
if (!in_array($tabela, $tabelasValidas)) {
    echo json_encode(['error' => true, 'message' => "Nome de tabela inválido."]);
    exit;
}

// Prepara a consulta SQL usando o nome da tabela validado
$query = "SELECT * FROM " . $tabela;

$result = $mysqli->query($query);

if ($result) {
    $reservas = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($reservas);
} else {
    echo json_encode(['error' => true, 'message' => "Erro ao buscar reservas."]);
}

$mysqli->close();
?>
