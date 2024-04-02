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
 
document.addEventListener('DOMContentLoaded', function() {
	
	let savedWeekStart = localStorage.getItem('currentWeekStart');
    let currentWeekStart;
    if (savedWeekStart) {
        currentWeekStart = new Date(savedWeekStart); // Usa a data salva
    } else {
        currentWeekStart = getCurrentWeekStart(); // Calcula a data de início da semana atual
    }
	let dateStr;
    var usuarioLogado = false;
    var nomeUsuario = '';
    var corUsuario = '';

	// Tentativa de recuperar o valor selecionado do localStorage
    var tabela = localStorage.getItem('tabelaSelecionada') || 'sala1'; // 'sala1' é o valor padrão
	
	// Atualiza o valor do select com o valor recuperado
    document.getElementById('salaSelect').value = tabela;

	// Ouvinte de evento para mudanças no selecionador de salas
    document.getElementById('salaSelect').addEventListener('change', function() {
        // Atualiza a variável 'tabela' com o valor selecionado
        tabela = this.value;
        
        // Armazena a seleção atual no localStorage para persistência
        localStorage.setItem('tabelaSelecionada', tabela);

        // Chama a função para atualizar os dados com base na nova seleção
        updateTableForWeek(currentWeekStart);
    });
	
    fetch('verificaLogin.php')
    .then(response => response.json())
    .then(data => {
        if (data.logado) {
            usuarioLogado = true;
            nomeUsuario = data.nome;
            corUsuario = data.cor;
        }
		updateTableForWeek(currentWeekStart);
    })
	
	function updateTableForWeek(startOfWeek) {
    const diasDaSemana = ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];
    const tableHead = document.getElementById('scheduleTable').getElementsByTagName('thead')[0];
    const tableBody = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
	
	fetch('getReservas.php', {
    method: 'POST', // Especifica que a requisição é do tipo POST
    headers: {
        'Content-Type': 'application/json' // Indica o tipo do conteúdo enviado
    },
    body: JSON.stringify({ tabela: tabela }) // Envia os dados no corpo da requisição
})
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na busca dos dados: ' + response.status);
        }
        return response.json();
    })
    .then(reservas => {
    tableBody.innerHTML = ''; 
    tableHead.innerHTML = '<tr><th>Horário</th></tr>'; 
    let headerRow = tableHead.rows[0];
    for (let i = 0; i < 7; i++) {
        let date = new Date(startOfWeek);
        date.setDate(date.getDate() + i);
        let dayHeaderCell = document.createElement('th');
        dayHeaderCell.textContent = `${diasDaSemana[date.getDay()]} ${formatDate(date)}`;
        headerRow.appendChild(dayHeaderCell);
    }
    for (let hour = 8; hour <= 18; hour++) {
        let row = tableBody.insertRow();
        let timeCell = row.insertCell(0);
        timeCell.textContent = `${hour}:00`;

        for (let i = 0; i < 7; i++) {
			let date = new Date(startOfWeek);
			date.setDate(date.getDate() + i);
			// Formata a data para 'YYYY-MM-DD'
			dateStr = formatDateForId(date);
            let cell = row.insertCell(i + 1);
            cell.textContent = 'Disponível';
            cell.className = 'available';
            
            // Use o índice da linha e da coluna para criar o cellId
            cell.id = 'cell-' + hour + '-' + dateStr;

            let reserva = reservas.find(r => r.cell_id === cell.id);
            if (reserva) {
                cell.style.backgroundColor = reserva.cor;
                cell.textContent = reserva.nome;
                cell.classList.remove('available');
            }
				// Remova todos os eventos de clique anteriores
				cell.replaceWith(cell.cloneNode(true));
				cell = row.cells[i + 1];

				// Adicione o evento de clique aqui
				cell.addEventListener('click', function() {
					if (!usuarioLogado) {
						alert('Por favor, faça login para agendar este horário.');
						return;
					}
					
					// Se a célula já estiver reservada por outro usuário, não permita a interação
					if (!this.classList.contains('available') && this.textContent !== nomeUsuario) {
						alert('Esta reserva já foi feita por outro usuário.');
						return;
					}

					let cellId = this.id;
					let dados = new FormData();
					dados.append('cellId', cellId);
					dados.append('tabela', tabela);

					// Se a célula já estiver reservada pelo usuário logado, pergunte se ele quer remover a reserva
					if (this.textContent === nomeUsuario) {
						if (confirm('Tem certeza de que deseja remover esta reserva?')) {
							this.style.backgroundColor = '';  
							this.textContent = 'Disponível'; 
							this.classList.add('available'); 

							fetch('removerReserva.php', {
								method: 'POST',
								body: dados,
							})
							.then(response => response.json())
							.then(data => {
								if (!data.success) {
									alert('Houve um erro ao remover a reserva.');
								}
							})
							.catch(error => console.error('Erro ao remover reserva:', error));
						}
						return
					}							
						dados.append('nome', nomeUsuario);
						dados.append('cor', corUsuario);
						fetch('registrarReservas.php', {
							method: 'POST',
							body: dados,
						})
						.then(response => response.json())
						.then(data => {
							if (!data.success) {
								alert('Houve um erro ao registrar a reserva.');
							}
						})
						.catch(error => console.error('Erro ao registrar reserva:', error));
						location.reload();
						
				});				
		}
	}
    let monthName = startOfWeek.toLocaleString('pt-BR', { month: 'long' }).toUpperCase();
    let year = startOfWeek.getFullYear();
    year = year > 2030 ? 2030 : year; 

    document.getElementById('weekLabel').innerHTML = `<strong>${monthName}</strong> ${year}<br>Semana de ${formatDate(startOfWeek)} a ${formatDate(new Date(startOfWeek.getFullYear(), startOfWeek.getMonth(), startOfWeek.getDate() + 6))}`;
})

    .catch(error => console.error(error));
	}
	// Função para formatar a data no formato 'YYYY-MM-DD' para usar no ID
        function formatDateForId(date) {
            let year = date.getFullYear();
            let month = ('0' + (date.getMonth() + 1)).slice(-2); // Adiciona um zero à esquerda se necessário
            let day = ('0' + date.getDate()).slice(-2); // Adiciona um zero à esquerda se necessário
            return `${year}-${month}-${day}`;
        }
		
		document.getElementById('nextWeek').addEventListener('click', function() {
			currentWeekStart.setDate(currentWeekStart.getDate() + 7);
			localStorage.setItem('currentWeekStart', currentWeekStart.toISOString()); // Armazena a data atualizada
			updateTableForWeek(currentWeekStart);
		});

		document.getElementById('prevWeek').addEventListener('click', function() {
			currentWeekStart.setDate(currentWeekStart.getDate() - 7);
			localStorage.setItem('currentWeekStart', currentWeekStart.toISOString()); // Armazena a data atualizada
			updateTableForWeek(currentWeekStart);
		});
});

function getCurrentWeekStart(date = new Date()) {
    const day = date.getDay() || 7; // Converte domingo de 0 para 7
    if(day !== 1) 
        date.setHours(-24 * (day - 1)); // Ajusta para a última segunda-feira
    return new Date(date.setHours(0, 0, 0, 0)); // Zera a hora
}


function formatDate(date) {
    return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}`;
}