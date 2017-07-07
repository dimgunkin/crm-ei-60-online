<?php
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = "ОТЛАДКА - ".$CONFIG['title_header'];

//подключаем верстку
include("head.inc.php"); //шапка сайта
include("navbarleft.inc.php"); //боковое меню навигации
include("navbartop.inc.php"); //верхнее меню навигации
	
// Заготовка запроса с внешними переменными
$stmt = $dbConnection->query('SELECT * from crm_applist ORDER BY num DESC');
$applist = $stmt->fetchAll();
		
?>	
<!-- начало тела станицы -->

<?

		


?>	
         <div class="page-heading">
		 
		<!-- панель проверки прав администратора -->
		 	<div class="panel panel-default">
                <div class="panel-body">
                    <center>
					<?
					if ($_SESSION["auth_email"] == 'kt@1dvm.ru')
					{
						echo 'Если видите это - вы администратор портала! <br><br>';
						$date_start = date("Y-m-d");
						echo date($date_start, strtotime("+5 day")); //date("t", strtotime(date("Y-m")));
						
						echo '<br><br>';
					} else {
						echo 'Если видите этот текст - вы пользователь портала!';
					}
					?>
					</center>
                </div>
            </div>
		<!-- конец панель проверки прав администратора -->	
			
        </div>
        <!-- page heading end-->

		
		
	

		<div class="page-heading">
		 	<div class="panel panel-default">
                <div class="panel-body">
				
<center> ######### </center>
<br><br>

<?
$os = array("5", "8", "4", "3");

if (in_array("8", $os)) {
    echo "8 <br>";
}
echo var_export($os);
?>

<br><br>
<a href="javascript:;" class="btn btn-success  btn-sm" id="add-sticky">Уведомление-липучка (пока не закроешь)</a>
	
<br><br>

                </div>
            </div>
        </div>

		
	
		
		
		
		
		
        <!--body wrapper start-->
	<div class="wrapper">
		<div class="row">
			<div class="col-sm-12">
				<section class="panel">
					<header class="panel-heading">
						ОТЛАДКА СИСТЕМЫ
					</header>
					<div class="panel-body">
					
	<ul>
		<li><a href="?test=user.current">Информация о пользователе</a></li>
		<li><a href="?test=contact.list"><b> Выгрузить список контактов</b></a></li>
		<li><a href="?test=user.update">Загрузить новую аватарку пользователя</a></li>
		<li><a href="?test=log.blogpost.add">Опубликовать запись в Живой Ленте</a></li>
		<li><a href="?test=event.bind">Установить обработчик события</a></li>
		-----
		<li><a href="?test=user.get">Список пользователей</a></li>
		<li><a href="?test=department.get">Список подразделений</a></li>
	</ul>
	
	<a href="<?=PATH?>?refresh=1">Обновить данные авторизации</a><br />
	<a href="<?=PATH?>?clear=1">Очистить данные авторизации</a><br />
	
	<br><br>
	
<?
	$data = $_SESSION["query_data"];
	//echo '<pre>'; var_export($data); echo '</pre>';
	echo '<pre>'; var_export($data); echo '</pre>';
	
	
	// --------------------------------------------- //
		$test = isset($_REQUEST["test"]) ? $_REQUEST["test"] : "";
	switch($test)
	{
		case 'user.current': // test: user info

			$data = call($_SESSION["query_data"]["domain"], "crm.company.fields", array(
				"auth" => $_SESSION["query_data"]["access_token"]));

		break;
		
		case 'department.get':

			$data = call($_SESSION["query_data"]["domain"], "department.get", array(
				"auth" => $_SESSION["query_data"]["access_token"])
			);

		break;
		
		case 'user.get':

			$data = call($_SESSION["query_data"]["domain"], "user.get", array(
				"auth" => $_SESSION["query_data"]["access_token"],
			));

		break;
		
		case 'contact.list': 
		
													/*	
														$data = call($_SESSION["query_data"]["domain"], "crm.contact.list", array(
														"auth" => $_SESSION["query_data"]["access_token"],
														"order" => array("DATE_CREATE: ASC"),
														"filter" => array("TYPE_ID: CLIENT"),
														"select" => array("ID", "NAME", "LAST_NAME", "TYPE_ID", "SOURCE_ID")
														)); 
														
														ПОКАЗЫВАЕМ ПОЛЯ ФУНКЦИЙ
														$fields = call($_SESSION["query_data"]["domain"], "crm.invoice.fields", array(
														"auth" => $_SESSION["query_data"]["access_token"]
														));		
													*/
														
														$data = call($_SESSION["query_data"]["domain"], "crm.company.list", array(
														"auth" => $_SESSION["query_data"]["access_token"],
														"order" => array("DATE_CREATE: ASC"),
														"filter" => array("TYPE_ID: CLIENT"),
														"select" => array("ID", "TITLE", "CURRENCY_ID", "REVENUE")
														));
		
		break;


		case 'user.update': // test batch&files

			$fileContent = file_get_contents(dirname(__FILE__)."/images/MM35_PG189a.jpg");

			$batch = array(
				'user' => 'user.current',
				'user_update' => 'user.update?'
					.http_build_query(array(
						'ID' => '$result[user][ID]',
						'PERSONAL_PHOTO' => array(
							'avatar.jpg',
							base64_encode($fileContent)
						)
					))
			);

			$data = call($_SESSION["query_data"]["domain"], "batch", array(
				"auth" => $_SESSION["query_data"]["access_token"],
				"cmd" => $batch,
			));

		break;
		
		case 'im.notify': // bind event handler

			$data = call($_SESSION["query_data"]["domain"], "im.notify", array(
				"auth" => $_SESSION["query_data"]["access_token"],
				"to" => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
				"message" => "Привет"
			));

		break;
///////////////////////////////////////////////	
// #F8F8F8 (для чата Bx24 - #000000) - app-gotobuilding - светло-серый (в производстве)	
// #ffffff (для чата Bx24 - #000000) - app-production - белый (в производстве)	
// #e7f9e1 (для чата Bx24 - #008000) - app-ready - зелёный (готов)
// #f8d9ac (для чата Bx24 - #FFA500) - app-suspended - ораньжевый (приостановлен)
// #fce2dc (для чата Bx24 - #FF69B4) - app-stopped - розовый (отменён)
// #e8cff8 (для чата Bx24 - #800080) - app-shipped - фиолетовый (отгружен)
//
// Сообщения чата:
// 1) Запущена заявка №___. Отгрузка __-__-____.
		case 'im.message.add': //чат с заявками id 98

		bx24_im_mes_add_file_98("Тест__", "#000000", "Тест__имя", "http://crm.ei-60.online/uploads/apps_files/2017/7/7_blank_2017_04_06_14_14_20.xls", "20", "Заказ №20 [нажмите, что бы открыть подробности заказа]");
		//bx24_im_mes_add_98("Тест");
		
/* $data = call($_SESSION["query_data"]["domain"], "im.message.add", Array(
   "auth" => $_SESSION["query_data"]["access_token"],	
   "CHAT_ID" => 98,
   "MESSAGE" => "Проверка загрузки бланка заявки",
   "ATTACH" => Array(
      "ID" => 1,
      "COLOR" => "#FF4500",
      "BLOCKS" => Array(
Array("FILE" => Array(
    Array(
        "NAME" => "Бланк на заявку №7",
        "LINK" => "http://crm.ei-60.online/uploads/apps_files/2017/7/7_blank_2017_04_06_14_14_20.xls")
)),)
))); */

		break;
		

//////////////////////////////////////	
		case 'im.chat.add':

			$data = call($_SESSION["query_data"]["domain"], "im.chat.add", array(
				"auth" => $_SESSION["query_data"]["access_token"],
				"TYPE" => "CHAT",
				"TITLE" => "Заявки в производстве",
				"DESCRIPTION" => "Здесь вы найдете все изменения по заявкам в производстве и сможете их обсудить!",
				"COLOR" => "PINK",
				"MESSAGE" => "Здесь вы найдете все изменения по заявкам в производстве и сможете их обсудить!",
				"USERS" => Array(1,20)
			));

		break;
		
		case 'event.bind': // bind event handler

			$data = call($_SESSION["query_data"]["domain"], "event.bind", array(
				"auth" => $_SESSION["query_data"]["access_token"],
				"EVENT" => "ONCRMLEADADD",
				"HANDLER" => REDIRECT_URI."event.php",
			));

		break;

		case 'log.blogpost.add': // add livefeed entry

			$fileContent = file_get_contents(dirname(__FILE__)."/images/MM35_PG189a.jpg");

			$data = call($_SESSION["query_data"]["domain"], "log.blogpost.add", array(
 				"auth" => $_SESSION["query_data"]["access_token"],
				"POST_TITLE" => "Hello world!",
				"POST_MESSAGE" => "Goodbye, cruel world :-(",
				"FILES" => array(
					array(
						'minotaur.jpg',
						base64_encode($fileContent)
					)
				),

 			));

 		break;


		default:

			$data = $_SESSION["query_data"];

		break;
	}
	echo '<pre>'; var_export($data); echo '</pre>';
	// --------------------------------------------- //
?>
					
					</div>
				</section>
			</div>
		</div>		
	</div>
	
	
<!-- конец тела станицы -->
<?
		include("footer.inc.php"); // подвал страницы
	//}
//}
//else 
//{
//	include 'auth.php'; //страница входа в систему
//}
?>
<script type="text/javascript">
$(document).ready(function () {

$('.form-horizontal').keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
      }
});

$("input[name='bill_number']").change(function () {
    if ($("input[name='bill_number']").val() != '')
		if($("input[name='app_file']").val() != '')
		$("button[name='app_add_btn']").removeAttr('disabled');
		else
		$("button[name='app_add_btn']").attr('disabled', true); 
    else
        $("button[name='app_add_btn']").attr('disabled', true); 
});

$("input[name='app_file']").change(function () {
    if ($("input[name='app_file']").val() != '')
		if($("input[name='bill_number']").val() != '')
		$("button[name='app_add_btn']").removeAttr('disabled');
		else
		$("button[name='app_add_btn']").attr('disabled', true); 
    else
        $("button[name='app_add_btn']").attr('disabled', true); 
});

	
$('#pay_metod').change(function () {
var sel_groups = $(this).val();
if (sel_groups == 'cash') {
$("input[name='bill_number']").attr('value', 'Без счета');
$("input[name='bill_number']").attr('readonly', true);
}
if (sel_groups == 'bill') {
$("input[name='bill_number']").attr('readonly', false);
$("input[name='bill_number']").attr('value', '');
$("input[name='bill_number']").attr('placeholder', 'Номер счета');
}
});

//обработчик отметки готовности заказа
$('button[id^="app-ready_"]').click(function() {
	var btn_app_num = $('#'+this.id).attr("value");
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'app_ready', 'num': btn_app_num},
			cache: false,			
		success: function(response){
				$('button[id="app-ready_'+btn_app_num+'"]').attr('disabled', true);
				$('tr[id="'+btn_app_num+'"]').removeClass();
				$('tr[id="'+btn_app_num+'"]').addClass('app-ready');
				$('span[id="'+btn_app_num+'"]').html('<?=$APP_STATUS['app-ready'];?>');			
			}
		});
	//}		
	});

});
</script>