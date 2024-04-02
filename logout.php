<?php
 
session_start(); // Inicia a sessão

// Destrói todas as variáveis da sessão
$_SESSION = array();

// Se desejar destruir a sessão completamente, apague também o cookie de sessão.
// Isso vai destruir a sessão, e não apenas os dados da sessão!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão.
session_destroy();

// Redireciona o usuário para a página de login ou página inicial
header("Location: index.php");
exit;
?>
