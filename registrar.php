<?php

 
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "agendateste");

if ($mysqli->connect_error) {
    echo json_encode(['error' => true, 'message' => "Erro de conexão: " . $mysqli->connect_error]);
    exit;
}

$cpf = $_POST['cpf'];
$sql = "SELECT id FROM usuarios WHERE cpf = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $cpf);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['error' => true, 'message' => "Este CPF já está registrado."]);
} elseif ($_POST['senha'] !== $_POST['senhaRepetida']) {
    echo json_encode(['error' => true, 'message' => "As senhas não coincidem."]);
} else {
    $stmt->close();
    $sql = "INSERT INTO usuarios (nome, senha, email, cpf, telefone, cor) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $corAleatoria = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT); // Gera uma cor aleatória
    $stmt->bind_param("ssssss", $_POST['nome'], $senhaHash, $_POST['email'], $cpf, $_POST['telefone'], $corAleatoria);

    if ($stmt->execute()) {
        echo json_encode(['error' => false, 'message' => "Usuário registrado com sucesso."]);
    } else {
        echo json_encode(['error' => true, 'message' => "Erro ao registrar usuário: " . $mysqli->error]);
    }
    $stmt->close();
}

$mysqli->close();
?>
