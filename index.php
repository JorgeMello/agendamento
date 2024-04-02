<?php
 
session_start();
// Esta linha determina se há um usuário logado e passa a informação para o JavaScript
$scriptDeLogin = '<script>var usuarioLogado = ' . (isset($_SESSION['usuario_id']) ? 'true' : 'false') . ';</script>';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Salas</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css">
	<?php echo $scriptDeLogin; ?>
</head>
<body>
    <header>
        <nav id="loginArea">
            <?php if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado']): ?>
            <button id="logoutBtn">Logout</button>
            <?php else: ?>
            <button id="loginBtn">Login</button>
            <button id="registerBtn">Registrar</button>
            <?php endif; ?>
            
        </nav>
        <h3>Agendamento do Salão de Festas, Quadras de Futebol e Churrasqueiras</h3>
        
    </header>
    <main>
        <div id="weekNavigation">
            <button id="prevWeek">Agenda da Semana Anterior</button>
            <span id="weekLabel">Semana de 1°/04 até 7/04</span>
            <button id="nextWeek">Próxima Semana</button>
        </div>
		<div>
			<span>Selecione o que gostaria de RESERVAR:</span>
			<select id="salaSelect" class="form-control form-control-lg bg-light text-dark">
				<option value="sala1">Salão de Festas</option>
				<option value="sala2">Quadra de Futebol</option>
				<option value="sala3">Churrasqueiras</option>
			</select>
		</div>
        <table id="scheduleTable">
            <thead>
                <tr>
                    <th>Horário</th>
                    <th>Segunda</th>
                    <th>Terça</th>
                    <th>Quarta</th>
                    <th>Quinta</th>
                    <th>Sexta</th>
                </tr>
            </thead>
            <tbody>
                <!-- Os horários serão inseridos aqui pelo JavaScript -->
            </tbody>
        </table>
    </main>
	<!-- <div class="modal fade" id="salaModal" tabindex="-1" role="dialog" aria-labelledby="salaModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="salaModalLabel">Selecione uma Sala</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
					<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<select class="form-control" id="salaSelect">
					<!-- As opções serão preenchidas aqui pelo JavaScript 
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="confirmarSalaBtn">Confirmar</button>
				</div>
			</div>
		</div>
	</div> -->

    <div id="registerModal" class="modal-backdrop">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="errorMessage" style="color: red;"></div> <!-- Elemento para exibir mensagens de erro -->
            <form id="registerForm" method="POST">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
                <br>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
                <br>
                <label for="senhaRepetida">Repita a Senha:</label>
                <input type="password" id="senhaRepetida" name="senhaRepetida" required>
                <br>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
                <br>
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required>
                <br>
                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" required>
                <br>
                <input type="submit" value="Registrar">
            </form>
        </div>
    </div>
    <!-- Modal para o formulário de login -->
    <div id="loginModal" class="modal-backdrop">
        <div class="modal-content">
            <span class="close-login-modal">&times;</span>
            <form id="loginForm">
                <label for="emailLogin">E-mail:</label>
                <input type="email" id="emailLogin" name="email" required>

                <label for="senhaLogin">Senha:</label>
                <input type="password" id="senhaLogin" name="senha" required>

                <input type="submit" value="Login">
            </form>
        </div>
    </div>

    <footer class="text-center mt-4">
		<p>Desenvolvido por <strong>Jorge mello</strong></p>
		<p>Engenheiro de software com paixão por criar soluções inovadoras e eficientes.</p>
		<p>Entre em contato: <a href="mailto:jorge_mello@hotmail.com">jorge_mello@hotmail.com</a></p>
		<p>Conecte-se comigo: <a href="https://www.osidosos.com.br" target="_blank">LinkedIn</a> | <a href="https://github.com/JorgeMello" target="_blank">GitHub PÚBLICO</a></p>
	</footer>
    <script src="script.js"></script>
    <script>
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Previne o comportamento padrão de submissão do formulário
            
                var formData = new FormData(this); // Cria um FormData com os dados do formulário
            
                // Faz a requisição AJAX para o servidor
                fetch('registrar.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json()) // Converte a resposta em JSON
                .then(data => {
                    if (data.error) {
                        document.getElementById('errorMessage').innerText = data.message; // Exibe a mensagem de erro
                    } else {
                        alert(data.message); // Ou redirecione o usuário, mostre uma mensagem de sucesso, etc.
                        window.location.reload(); // Por exemplo, para recarregar a página ou redirecionar
                    }
                })
                .catch(error => console.error('Erro:', error));
            });
                    // Função para abrir o modal de registro
            function openModal() {
                document.getElementById('registerModal').style.display = 'block';
            }

            // Função para fechar o modal de registro
            function closeModal() {
                document.getElementById('registerModal').style.display = 'none';
            }

            // Adiciona o evento de clique para abrir o modal
            
			document.addEventListener('DOMContentLoaded', function() {
			var registerBtn = document.getElementById('registerBtn');
			if (registerBtn) {
				registerBtn.addEventListener('click', openModal);
			}
			});

            // Adiciona o evento de clique no botão de fechar (x) para fechar o modal
            document.querySelector('.close-modal').addEventListener('click', closeModal);

            // Fecha o modal se o usuário clicar fora do conteúdo
            window.onclick = function(event) {
                var modal = document.getElementById('registerModal');
                if (event.target === modal) {
                    closeModal();
                }
            }
                        // Função para abrir o modal de login
            function openLoginModal() {
                document.getElementById('loginModal').style.display = 'block';
            }

            // Função para fechar o modal de login
            function closeLoginModal() {
                document.getElementById('loginModal').style.display = 'none';
            }

			document.addEventListener('DOMContentLoaded', function() {
			var loginBtn = document.getElementById('loginBtn');
			if (loginBtn) {
				loginBtn.addEventListener('click', openLoginModal);
			}
			});
            document.querySelector('.close-login-modal').addEventListener('click', closeLoginModal);

            // Fechar o modal ao clicar fora dele
            window.onclick = function(event) {
                if (event.target === document.getElementById('loginModal')) {
                    closeLoginModal();
                }
            }
            document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            fetch('login.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.message); // Exibe a mensagem de erro
                } else {
                    alert(data.message); // Exibe a mensagem de sucesso
					window.location.href = data.redirect;
                    document.getElementById('loginModal').style.display = 'none'; // Fecha o modal de login
                    document.getElementById('loginBtn').style.display = 'none'; // Esconde o botão de login
                    document.getElementById('registerBtn').style.display = 'none'; // Esconde o botão de registro
					
                }
            })
            .catch(error => console.error('Erro:', error));
            });
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    window.location.href = 'logout.php';
                });
            }
    </script>
</body>
</html>
