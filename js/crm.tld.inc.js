// обработчик отметки готовности задачи от сотрудника (отправка на модерацию)
//$('#moder_send').click(function() { 
$('button[id^="moder_send_"]').click(function() { 

	//alert($(this.id).attr("name"));
	
	var TodoChName = $('#'+this.id).attr("name");
	var TodoChValue = $('#'+this.id).attr("value");
	var TodoMSG = prompt('Можете прокомментировать выполнение задачи', ' ');

	if (TodoMSG == ' ') {
		TodoMSG = 'Я выполнил задачу! Жду решения!';
	}
	
	if (TodoMSG == null) {
		
	} else {
		$.ajax({
			url: 'action.inc.php', // Обработчик
			type: 'GET',       // Отправляем методом GET
			data: {'td_id': TodoChValue, 'td_name': TodoChName, 'todo_msg': TodoMSG},
			cache: false,			
		success: function(response){
			
			if(response == 'moder_todo_ok'){ // Смотрим ответ от сервера и выполняем соответствующее действие
				$('#todo_status_'+TodoChValue).html('<span class="label label-warning label-mini">Проверяется</span>');
				$('#todo_status_'+TodoChValue).effect('pulsate', {times: 4}, 4000);
				$('button[name="todo_'+TodoChValue+'"]').attr('disabled',true);
				//$('input[name="todo_'+TodoChValue+'"]').attr('checked',false);
				return false;
			}else{
				alert('Ошибочка...');
				}
			}
		});
	}
});


// обработчик премодерации (задание выполнено)
//$('#moder_send').click(function() { 

$('button[id^="btn_ok_"]').click(function() { 
	
	//alert($(this.id).attr("name"));
	
	var TodoOKChName = $('#'+this.id).attr("value");
	var TodoMSG = $('textarea[id^="todo_msg_direct_"]').val();

$.ajax({
	url: 'action.inc.php', // Обработчик
	type: 'GET',       // Отправляем методом GET
	data: {'todo_mod': 'ok', 'todo_mod_id': TodoOKChName, 'todo_mod_msg': TodoMSG},
	cache: false,			
success: function(response){
	if(response == 'moder_todo_ok_modal'){ // Смотрим ответ от сервера и выполняем соответствующее действие
		$('tr[id="del_'+TodoOKChName+'"]').remove().slideUp('slow');
		return false;
	}else{
		alert('Ошибочка...');
		}
	}
});	
});


// обработчик премодерации (задание вернуть на доработку)
//$('#moder_send').click(function() { 

$('button[id^="btn_no_"]').click(function() { 
	
	//alert($(this.id).attr("name"));
	
	var TodoOKChName = $('#'+this.id).attr("value");
	var TodoMSG = $('textarea[id^="todo_msg_direct_"]').val();
	
	if (TodoMSG == null) {
		TodoMSG = 'Результат не соответствует поставленной задаче. Потрудитесь ещё!';
	}
	
$.ajax({
	url: 'action.inc.php', // Обработчик
	type: 'GET',       // Отправляем методом GET
	data: {'todo_mod': 'no', 'todo_mod_id': TodoOKChName, 'todo_mod_msg': TodoMSG},
	cache: false,			
success: function(response){
	if(response == 'moder_todo_no_modal'){ // Смотрим ответ от сервера и выполняем соответствующее действие
		$('tr[id="del_'+TodoOKChName+'"]').remove().slideUp('slow');
		return false;
	}else{
		alert('Ошибочка...');
		}
	}
});	
});



$('button[id^="save_todo_"]').hide();  



///////////////////////////////////////////////////////
///////////
//////


// Обработчик добавления задачи
$(function(){
$('#todo_create').submit(function(e){
//отменяем стандартное действие при отправке формы
e.preventDefault();
//берем из формы метод передачи данных
var m_method=$(this).attr('method');
//получаем адрес скрипта на сервере, куда нужно отправить форму
var m_action=$(this).attr('action');
//получаем данные, введенные пользователем в формате input1=value1&input2=value2...,
//то есть в стандартном формате передачи данных формы
var m_data=$(this).serialize();


	$('#todo_create').animate({opacity:'0.3'},1);
	//$('button[id="todo_create"]').hide();
	$('button[id="todo_create"]').text('... идет обработка запроса ...');

$.ajax({
type: m_method,
url: m_action,
data: m_data+"&action=todosend",
success: function(result){
	if(result == 'add_todo_ok'){ // Смотрим ответ от сервера и выполняем соответствующее действие
		setTimeout(function() { 
		$('#todo_create').animate({opacity:'1.0'},1);
		$('button[id="todo_create"]').attr("disabled", true);
		$('button[id="todo_create"]').text('Задача успешно отправлена!').effect('pulsate', {times: 4}, 4000);
				setTimeout(function() { 
				$('button[id="todo_create"]').removeAttr('disabled');
				//$('#employees_select :nth-child(1)').attr('selected', 'selected');
				$("#emp_check_all_not input:checkbox:enabled").removeAttr('checked');
				$('#deadline_input').val('2017-01-01 15:00');
				$('#task_textarea').val('');
				$('#todo_file').val('');
				$('#task_textarea').attr('placeholder','Изложите суть задачи...')
				$('button[id="todo_create"]').text('Отправить задачу');
				 }, 6000);
		 }, 6000);
	}
	if(result == 'add_todo_die'){
		$('#todo_create').animate({opacity:'1.0'},1);
		//$('#emp_warning').effect('pulsate', {times: 3}, 3000);
		$('#emp_check_all_not').effect('pulsate', {times: 3}, 3000);
		$('button[id="todo_create"]').removeAttr('disabled');
		$('button[id="todo_create"]').text('Отправить задачу');
	}
}
});	
	


 
 
	

});
});


//$('td.hidden-phone').mouseenter(
//function() {
		 
		 // навели курсор на объект
//		 $('.fa, .fa-info-circle').show();
//});

//$('td.hidden-phone').mouseleave(
//function(){ 
		
		// отвели курсор с объекта
//		$('.fa, .fa-info-circle').hide();
//});