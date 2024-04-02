<?php

session_start(); // Inicia a sessão
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "agendateste");

if ($mysqli->connect_error) {
    echo json_encode(['error' => true, 'message' => "Erro de conexão: " . $mysqli->connect_error]);
    exit;
}

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT id, senha FROM usuarios WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($senha, $row['senha'])) {
        // Senha correta, login bem-sucedido
        $_SESSION['usuario_logado'] = true; // Indica que o usuário está logado
		$_SESSION['usuario_id'] = $row['id']; // Salva o ID do usuário, por exemplo
        echo json_encode(['error' => false, 'message' => 'Login bem-sucedido.', 'redirect' => 'index.php']);
		exit;
		
    } else {
        // Senha incorreta
        echo json_encode(['error' => true, 'message' => " senha incorretos."]);
    }
} else {
    // E-mail não encontrado
    echo json_encode(['error' => true, 'message' => "E-mail ou senha incorretos."]);
}

$stmt->close();
$mysqli->close();
?>
