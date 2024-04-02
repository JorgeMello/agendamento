<?php
/**
 * ====================================================================
 * Sistema de Agendamento de Salas
 * Desenvolvido por Pablo Gafanha
 * 
 * Este sistema foi criado para facilitar o agendamento e a utilização
 * de salas de forma eficiente e organizada. Através de uma interface
 * amigável e intuitiva, usuários podem marcar horários em salas
 * disponíveis, visualizar agendamentos e receber lembretes.
 * 
 * Se você encontrar este código útil para seus projetos, por favor,
 * dê os devidos créditos, mencionando o autor original.
 * 
 * Para mais informações, sugestões ou colaborações, você pode entrar
 * em contato através do e-mail: devopsgafanha@gmail.com
 * 
 * Agradeço pelo apoio e espero que este sistema contribua positivamente
 * para seus projetos e necessidades.
 * 
 * Pablo Gafanha
 * ====================================================================
 */
 
session_start();
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "USUARIO_DO_BANCO", "SENHA", "BANCO_DE_DADOS");

if ($mysqli->connect_error) {
    echo json_encode(['logado' => false, 'mensagem' => "Erro de conexão: " . $mysqli->connect_error]);
    exit;
}

if (isset($_SESSION['usuario_id'])) {
    $usuarioId = $_SESSION['usuario_id'];
    
    $query = $mysqli->prepare("SELECT nome, cor FROM usuarios WHERE id = ?");
    $query->bind_param("i", $usuarioId);
    $query->execute();
    $resultado = $query->get_result();
    
    if ($row = $resultado->fetch_assoc()) {
        // Extrai o primeiro nome
        $partesDoNome = explode(' ', $row['nome']);
        $primeiroNome = $partesDoNome[0];
        
        // Converte o primeiro nome para minúsculas e depois capitaliza a primeira letra
        $primeiroNome = ucfirst(strtolower($primeiroNome));

        echo json_encode(['logado' => true, 'nome' => $primeiroNome, 'cor' => $row['cor']]);
    } else {
        echo json_encode(['logado' => false, 'mensagem' => 'Usuário não encontrado.']);
    }
    
    $query->close();
} else {
    echo json_encode(['logado' => false, 'mensagem' => 'Usuário não logado.']);
}

$mysqli->close();
?>
