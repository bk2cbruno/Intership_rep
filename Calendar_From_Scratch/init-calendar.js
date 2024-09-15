// Variável global para armazenar a data de início selecionada
var startDate = null;

// Função auxiliar para formatar a data no formato dd/mm/yyyy
function formatDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1; // Mês é baseado em zero, então adicione 1
    var year = date.getFullYear();

    return (day < 10 ? '0' : '') + day + '/' + (month < 10 ? '0' : '') + month + '/' + year;
}


$(document).ready(function(){

	// Add hover effect on day cards
	$('.day-card').hover(function() {
		$(this).addClass('bg-info text-white');
	}, function() {
		$(this).removeClass('bg-info text-white');
	});


	// Handle change event on the month and year select
	$('#month, #year').change(function() {
		var selectedMonth = $('#month').val();
		var selectedYear = $('#year').val();
		var currentUrl = window.location.href.split('?')[0]; // Obtém o URL atual sem os parâmetros de consulta
		var newUrl = currentUrl + '?view=bydas_hr_calendar&month=' + selectedMonth + '&year=' + selectedYear; // Constrói o novo URL com os parâmetros necessários

		// Redireciona para o novo URL
		window.location.href = newUrl;
	});


	$("#bydas_hr_calendar .calendar-month .day-card").click(function(e) {
		e.preventDefault();
		
		// Limpar o conteúdo do modal
		$("#modalNotesBody").empty();
		
		// Obter informações da data clicada
		var day = $(this).attr("data-day");
		var month = $(this).attr("data-month");
		var year = $(this).attr("data-year");


		 // Store the selected date globally
		 selectedDate = new Date(year, month - 1, day);

		 // Update the start date input field with the clicked date
		 updateStartDateInput();
 
		 // Log the selected start date
		 console.log('Selected start date: ' + day + '/' + month + '/' + year);
 
		 // Convert to "yyyy-MM-dd" format
		 var formattedDate = year + '-' + ('0' + month).slice(-2) + '-' + ('0' + day).slice(-2);
 
		 // Fill the start date field with the clicked date
		 $('#startDate').val(formattedDate);
	
		 // Obter o nome do mês com base no número do mês
		 var monthNames = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
		 var monthName = monthNames[parseInt(month) - 1]; // Arrays em JavaScript são baseados em zero, então subtrai 1 do número do mês para obter o índice correto no array
	 
		 // Atualizar o título do modal para incluir o dia e o mês
		 $("#modalDayTitle").text(`Dia ${day} de ${monthName}`);
	
		// Guardar a data no modal de escolha para uso posterior
		$("#bydas_hr_calendar_card_modal").data("day", day).data("month", month).data("year", year);
	
		// Chamada AJAX para obter as notas do servidor
		$.get(ENDPOINT, {
			action: "bydas_hr_calendar_get_notes",
			day: day,
			month: month,
			year: year
		})
		.done(function(response) {
			if (!response || response.trim() === '') {
				$("#modalNotesBody").text("Ainda não existe nota para este dia.");
				return;
			}
	
			let data;
	
			try {
				data = JSON.parse(response);
			} catch (e) {
				console.error('Erro ao analisar JSON:', e);
				$("#modalNotesBody").text("Erro ao processar a resposta do servidor.");
				return;
			}
	
			if (data.status === 1) {
				if (!Array.isArray(data.notes) || data.notes.length === 0) {
					$("#modalNotesBody").text("Ainda não existe nota para este dia.");
					return;
				}
	
				// Extrair as notas
				let notes = data.notes;
	
				// Criar uma string para exibir as notas e títulos
				let notesDisplay = "";
	
				notes.forEach(note => {
					if (note.title && note.notes) {
						notesDisplay += `Título: ${note.title}<br>Nota: ${note.notes}<br><br>`;
					} else {
						console.warn('Nota ou título ausente:', note);
					}
				});
	
				// Atualizar o conteúdo do modal com as notas
				$("#modalNotesBody").html(notesDisplay);
			} else {
				console.error('Erro do servidor:', data.msg);
				$("#modalNotesBody").text(data.msg);
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			console.error('Erro na solicitação AJAX:', textStatus, errorThrown);
			$("#modalNotesBody").text('Erro: Não foi possível conectar ao servidor.');
		});
	
		// Exibir o modal de escolha de ação
		$("#bydas_hr_calendar_card_modal").modal("show");


		// Função para atualizar o campo de data de início com a data selecionada
		function updateStartDateInput() {
			if (selectedDate) {
				var formattedDate = formatDate(selectedDate);
				$('#startDate').val(formattedDate);
			}
		}

	});
	

	// Botão para Adicionar Entrada
	$("#btn_add_entry").click(function() {
		// Obter informações do modal de escolha
		var day = $("#bydas_hr_calendar_card_modal").data("day");
		var month = $("#bydas_hr_calendar_card_modal").data("month");
		var year = $("#bydas_hr_calendar_card_modal").data("year");
	
		// Preencher campos do modal de adicionar entrada
		$("#bydas_hr_calendar_modal").modal("show");
		$("#bydas_hr_calendar_modal input[name=day]").val(day);
		$("#bydas_hr_calendar_modal input[name=month]").val(month);
		$("#bydas_hr_calendar_modal input[name=year]").val(year);
	
	$("#bydas_hr_calendar_modal button.btn-primary").off('click').on('click', function(e) {
			e.preventDefault();
			$.post(ENDPOINT, {
				action: "bydas_hr_calendar_add",
				id_users: $("#bydas_hr_calendar_modal select[name=id_users]").val(),
				title: $("#bydas_hr_calendar_modal input[name=title]").val(),
				notes: $("#bydas_hr_calendar_modal textarea[name=notes]").val(),
				day: day,
				month: month,
				year: year
		})
		.done(function(data) {
			data = JSON.parse(data);
			if (data.status == 1) {
				location.reload(); // Atualiza a página após sucesso na adição
			} else {
				displayError(data.msg); // Mostra uma mensagem de erro, se necessário
			}
		})
		.fail(function() {
			displayError("Erro de comunicação"); // Lida com falhas na comunicação
		});
	});

	// Fecha o modal de escolha de ação
	$("#bydas_hr_calendar_card_modal").modal("hide");
	});


	// Botão para Ver Notas
	$("#btn_view_notes").click(function() {
		var day = $("#bydas_hr_calendar_card_modal").data("day");
		var month = $("#bydas_hr_calendar_card_modal").data("month");
		var year = $("#bydas_hr_calendar_card_modal").data("year");
	
		$.get(ENDPOINT, {
			action: "bydas_hr_calendar_get_notes",
			day: day,
			month: month,
			year: year
		})
		.done(function(response) {
			if (!response || response.trim() === '') {
				alert('Erro: A resposta do servidor está vazia.');
				return;
			}
		
			let data;
		
			try {
				data = JSON.parse(response);
			} catch (e) {
				console.error('Erro ao analisar JSON:', e);
				alert('Erro ao processar a resposta do servidor.');
				return;
			}
		
			if (data.status === 1) {
				if (!Array.isArray(data.notes) || data.notes.length === 0) {
					alert('Nenhuma nota encontrada para a data especificada.');
					return;
				}
		
				// Extrair as notas
				let notes = data.notes;
		
				// Criar uma string para exibir as notas e títulos
				let notesDisplay = "";
		
				notes.forEach(note => {
					if (note.title && note.notes) {
						notesDisplay += `Título: ${note.title}<br>Nota: ${note.notes}<br><br>`; // Usar <br> para quebra de linha em HTML
					} else {
						console.warn('Nota ou título ausente:', note);
					}
				});
		
				// Atualizar o conteúdo do modal com as notas
				$("#notesContent").html(notesDisplay); // Usar .html() para interpretar <br>
		
				// Exibir o modal
				$("#notesModal").modal("show");
			} else {
				console.error('Erro do servidor:', data.msg);
				alert(data.msg);
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			console.error('Erro na solicitação AJAX:', textStatus, errorThrown);
			alert('Erro: Não foi possível conectar ao servidor.');
		});
	
		$("#bydas_hr_calendar_card_modal").modal("hide");
	});


	$('#btn_select_range').click(function() {

		// Definir a data de início selecionada como o valor inicial do campo de data de início
		if (startDate) {
			$('#startDate').val(formatDate(startDate));
		}
		$('#bydas_hr_calendar_modal').modal('show');
	});

	
	$("#btn_confirm_range").click(function() {
		// Verificar se a data de início está selecionada
		if (!selectedDate) {
			alert('Por favor, clique em um dia do calendário para selecionar a data de início.');
			return;
		}
	
		// Obter a data final selecionada
		var endDate = $('#endDate').val();
	
		// Converter as datas para objetos de data JavaScript
		var start = selectedDate;
		var end = new Date(endDate);
	
		// Verificar se a data de início é anterior à data final
		if (start > end) {
			alert('A data de início deve ser anterior à data final.');
			return;
		}
	
		// Fazer uma única chamada AJAX para adicionar uma entrada no calendário para todo o intervalo de datas
		// Esta chamada deve ser movida para fora do loop para evitar chamadas repetidas
		// Loop através de cada dia no intervalo
		var currentDate = new Date(start);
		var requests = []; // Array para armazenar todas as chamadas AJAX
	
		while (currentDate <= end) {
			var day = currentDate.getDate();
			var month = currentDate.getMonth() + 1;
			var year = currentDate.getFullYear();
	
			// Adicionar a chamada AJAX ao array de solicitações
			requests.push($.post('index.php', {
				action: 'bydas_hr_calendar_add',
				id_users: $("#bydas_hr_calendar_modal select[name=id_users]").val(),
				title: $("#bydas_hr_calendar_modal input[name=title]").val(),
				notes: $("#bydas_hr_calendar_modal textarea[name=notes]").val(),
				day: day,
				month: month,
				year: year
			}));
			
			// Avançar para o próximo dia
			currentDate.setDate(currentDate.getDate() + 1);
		}
	
		// Usar $.when.apply para executar todas as chamadas AJAX em paralelo
		$.when.apply($, requests)
		.done(function() {
			// Processar a resposta, se necessário
			console.log('Entradas adicionadas com sucesso para o intervalo de datas.');
			location.reload(); // Recarregar a página após o sucesso na adição
		})
		.fail(function() {
			console.error('Erro na chamada AJAX para adicionar entradas no calendário.');
			// Lidar com falha na chamada AJAX, se necessário
		});
	});
	
	
	// Handle click event on the "Add Entry" button
	$('#addEntryBtn').click(function(e) {
		
		e.preventDefault();

		$("#bydas_hr_calendar_modal_btn").modal("show")
		
		$("#bydas_hr_calendar_modal_btn button.btn-primary").click(function(e){
			e.preventDefault();
			$.post(ENDPOINT, { 
			 	action: "bydas_hr_calendar_add", 
                id_users: $("#bydas_hr_calendar_modal_btn select[name=id_users]").val(),
                title: $("#bydas_hr_calendar_modal_btn input[name=title]").val(),
                notes: $("#bydas_hr_calendar_modal_btn textarea[name=notes]").val(),
				day: $("#bydas_hr_calendar_modal_btn select[name=day]").val(),
				month: $("#bydas_hr_calendar_modal_btn select[name=month]").val(),
		        year: $("#bydas_hr_calendar_modal_btn select[name=year]").val()
               
			 })
			.done(function (data) {

				data=JSON.parse(data);

				if (data.status==1){

					location.reload();

				} else {

					displayError( data.msg );

				}
			})
			.fail(function (data) {
				displayError( "erro de comunicação");
			})

		
		})
	});
})