<?php
//подключаем файл конфигурации
include_once("config.inc.php");

//создаем подключение к базе данных
$dbConnection = new PDO(
    'mysql:host='.$CONFIG_DB['host'].';dbname='.$CONFIG_DB['db_name'],
    $CONFIG_DB['username'],
    $CONFIG_DB['password'],
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //выключение эмуляции подготавливаемых запросов (использовать стандартные драйверы)
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //включаем режим сообщений об ошибках и выбрасываем исключения (см. справку)

//помещаем в массив общие настройки ситемы (пример вызова параметра: $CONFIG['имя_строки_таблицы'])
$CONFIG = array (
	'title_header'	=> getConfigValue('title_header'),
	'hostname'		=> getConfigValue('hostname'),
	'mail'			=> getConfigValue('mail'),
	'city'			=> getConfigValue('city'),
	'system_name'	=> getConfigValue('system_name'),
	'year'			=> getConfigValue('year'),
	'url_bitrix24'	=> getConfigValue('url_bitrix24'),
	'yandex_oauth'	=> getConfigValue('yandex_oauth'), /* disabled/enabled */
	'its_work'		=> getConfigValue('its_work') /* 1/0 */
	);
	
//функция проверки прав доступа (авторизованного пользователя) к блокам системы
//check_access('', getUserInfo($_SESSION["work_position_access"], 'name'))
function check_access($item_name, $work_position_access) { 
global $dbConnection;
$stmt = $dbConnection->prepare('SELECT * FROM crm_access WHERE item_name=:item_name');
$stmt -> execute(array(':item_name' => $item_name));
$stmt_fetch = $stmt->fetch(PDO::FETCH_ASSOC);
$access = $stmt_fetch[$work_position_access];
	
// полный доступ :: fullacc
// неопределено :: unacc
// директор :: director
// директор по развитию :: director_dev
// бухгалтер :: accountant
// начальник производства :: factory_head
// инженер-конструктор :: factory_ec
// начальник отдела продаж :: salesteam_head
// менеджер отдела продаж :: salesteam_manager
// менеджер отдела продаж по прозвону :: salesteam_manager_the_call

	switch($access)
	{	
		// доступен
		case '1':
		return true;
		break;
		// недоступен
		case '0':
		return false;
		break;

		default: return false;
		break;
	}
} 

//Статус заявки (по русски), модель - 'имя_стиля\значение_в_бд' => 'русское_название_статуса'
$APP_STATUS = array (
	'app-gotobuilding' => 'запущен',
	'app-production' => 'в очереди',
	'app-ready'		 => 'готов',
	'app-suspended'	 => 'остановлен',
	'app-stopped'	 => 'отменен',
	'app-shipped'	 => 'отгружен'
	);
	
//Наименования изделий из бланка
$APP_NAMES_DOORS = array (
	'Дверь EI-60',
	'Дверь EIS-60',
	'Дверь Техническая',
	'Ворота EI-60',
	'Ворота EIS-60',
	'Ворота Технические',
	'Люк EI-60',
	'Люк EIS-60',
	'Люк Технический',
	'Фрамуга EI-60',
	'Фрамуга Техническая'
	);
	
//функция возвращает оставшееся время жизни токена БХ24
//что бы исключить глюки и т.д. искуственно уменьшаем время жизни токена на 15 минут (900000 мс)
function getTokenLive() {
	if (time() > $_SESSION["query_data"]["ts"] + $_SESSION["query_data"]["expires_in"] - 900) {
		$token_live = 0;
	} else {
		$token_live = $_SESSION["query_data"]["ts"] + $_SESSION["query_data"]["expires_in"] - 900 - time().'000';
	}
return $token_live;
}
	
//функция для получения CONFIG (получаем основные настройки системы)
function getConfigValue($in) {
	global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT value FROM crm_config where param=:in');
    $stmt -> execute(array(':in' => $in));
    $stmt_fetch = $stmt->fetch(PDO::FETCH_ASSOC);
return $stmt_fetch['value'];
}

//функция получения информации о пользователе
function getUserInfo($id, $column) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM crm_employees WHERE id=:id');
    $stmt -> execute(array(':id' => $id));
    $stmt_fetch = $stmt->fetch(PDO::FETCH_ASSOC);
return $stmt_fetch[$column];
}

//функция получения информации о пользователе (c помощью id_bx24)
function getUserInfo_BX24ID($id, $column) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM crm_employees WHERE bitrix24_id=:id');
    $stmt -> execute(array(':id' => $id));
    $stmt_fetch = $stmt->fetch(PDO::FETCH_ASSOC);
return $stmt_fetch[$column];
}

//функция получения информации о заявке
function getOrderInfo($num, $column) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM crm_applist WHERE num=:num');
    $stmt -> execute(array(':num' => $num));
    $stmt_fetch_bill = $stmt->fetch(PDO::FETCH_ASSOC);
return $stmt_fetch_bill[$column];
}

//функция получения информации о счете
function getBillInfo($num, $column) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT * FROM crm_bills WHERE app_id=:app_id');
    $stmt -> execute(array(':app_id' => $num));
    $stmt_fetch_bill = $stmt->fetch(PDO::FETCH_ASSOC);
return $stmt_fetch_bill[$column];
}

//функция выбирает активную позицию пункта подменю (легово меню)
function echoActiveClassSubMenu($requestUri)
{
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    $file = $_SERVER['REQUEST_URI'];
    $file = explode("?", basename($file));
    $current_file_name=$file[0];

    if ($current_file_name == $requestUri)
        echo 'class="active"';
}

//функция выбирает активную позицию пункта меню (легово меню)
function echoActiveClassMenu($menu_array)
{
	$menu = explode(", ", $menu_array);
	$current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    $file = $_SERVER['REQUEST_URI'];
    $file = explode("?", basename($file));
    $current_file_name=$file[0];
	if (in_array($current_file_name, $menu)){
		echo 'nav-active';
	}
}

//функция выводит номер заявки без года запуска (изначально номер заявки хххх_2017)
function appnumclear($appnum) {
//    $nameshort = preg_replace('/(\w+) (\w)\w+ (\w)\w+/iu', '$1 $2. $3.', $appnum);
	$appnumclear = substr($appnum, 0, -4);
    return $appnumclear;
}

//функция проверяет авторизацию на сайте (наличие данных в сессии)
function authsessionCheck() {
	if(!isset($_SESSION["auth_status"]) && !isset($_SESSION["auth_id"]) && !isset($_SESSION["auth_email"])) {
		return header('Location: index.php');
	} else {
		
	}
}

////////////////////////функция форматирования даты, которая выводится из бд////////////////////////
// если кода формата не будет найдено, функция вернёт стандартный формат в виде 01.01.2016 24:00:00
function d_format($date_db, $format) {
	$d_format = new DateTime($date_db);
	$rus_months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	switch($format) {
		case 'f1': //01 января 2016
			$num_month = $d_format->format('n');
			$d_format_1 = $d_format->format('j '.$rus_months[$num_month-1].' ');
			$d_format_1 .= $d_format->format('Y');
		break;
		case 'f2': //01.01.2016 00:00
			$d_format_1 = $d_format->format('d.m.Y H:i');
		break;
		case 'f3': //01 января 2016 00:00
			$num_month = $d_format->format('n');
			$d_format_1 = $d_format->format('j '.$rus_months[$num_month-1].' ');
			$d_format_1 .= $d_format->format('Y H:i');	
		break;
		case 'f4': //31-12-2016 12:00
			$d_format_1 = $d_format->format('d-m-Y H:i');	
		break;
		case 'f5': //31-12-2016
			$d_format_1 = $d_format->format('d-m-Y');	
		break;
		case 'f6': //31.12.2016
			$d_format_1 = $d_format->format('d.m.Y');	
		break;
		case 'f7': //31.12.2016 00:00
			$d_format_1 = $d_format->format('d.m.Y H:i');	
		break;
		case 'f8': //01 января
			$num_month = $d_format->format('n');
			$d_format_1 = $d_format->format('j '.$rus_months[$num_month-1].'');
		break;
		case 'f9': //2016-12-31 (для таблицы загруженности)
			$d_format_1 = $d_format->format('Y-m-d');	
		break;
		case 'f10': //00:00 (для таблицы загруженности)
			$d_format_1 = $d_format->format('H:i');	
		break;
		default: $d_format_1 = $d_format->format('d.m.Y H:i:s');
	}
return $d_format_1;
}

//функция добавляет к текущей дате указанное кол-во рабочих дней
function working_days($count) { 
    $date              = date( 'd-m-Y' ); 
    $day_week          = date( 'N', strtotime( $date ) ); 
    $day_count         = $count + $day_week; 
    $week_count        = floor($day_count/5); 
    $holiday_count     = ( $day_count % 5 > 0 ) ? 0 : 2; 
    $week_day          = $week_count * 7 - $day_week + ( $day_count % 5 ) - $holiday_count; 
    $date_end          = date( "d-m-Y", strtotime( $date . " + $week_day day " ) ); 
    $date_end_count    = date( 'N', strtotime( $date_end ) ); 
    $holiday_shift     = $date_end_count > 5 ? 7 - $date_end_count + 1 : 0; 
    return date("Y-m-d 00:00:00", strtotime($date_end . " + $holiday_shift day "));
} 

//функция добавляет к текущей дате указанное кол-во рабочих дней (5 раб дней для производства)
function working_days_factory($count) { 
    $date              = date( 'd-m-Y' ); 
    $day_week          = date( 'N', strtotime( $date ) ); 
    $day_count         = $count + $day_week; 
    $week_count        = floor($day_count/5); 
    $holiday_count     = ( $day_count % 5 > 0 ) ? 0 : 2; 
    $week_day          = $week_count * 7 - $day_week + ( $day_count % 5 ) - $holiday_count; 
    $date_end          = date( "d-m-Y", strtotime( $date . " + $week_day day " ) ); 
    $date_end_count    = date( 'N', strtotime( $date_end ) ); 
    $holiday_shift     = $date_end_count > 5 ? 7 - $date_end_count + 1 : 0; 
    return date("Y-m-d", strtotime($date_end . " + $holiday_shift day "));
} 

//функция добавляет к указанной дате указанное кол-во рабочих дней (5 раб дней для производства)
function working_days_factory_finish($date_finish, $count) { 
    $date              = $date_finish;  //date( 'd-m-Y' ); 
    $day_week          = date( 'N', strtotime( $date ) ); 
    $day_count         = $count + $day_week; 
    $week_count        = floor($day_count/5); 
    $holiday_count     = ( $day_count % 5 > 0 ) ? 0 : 2; 
    $week_day          = $week_count * 7 - $day_week + ( $day_count % 5 ) - $holiday_count; 
    $date_end          = date( "d-m-Y", strtotime( $date . " + $week_day day " ) ); 
    $date_end_count    = date( 'N', strtotime( $date_end ) ); 
    $holiday_shift     = $date_end_count > 5 ? 7 - $date_end_count + 1 : 0; 
    return date("Y-m-d", strtotime($date_end . " + $holiday_shift day "));
} 

//функция рассчитывает % загруженности производства на текущий момент
function factory_workload($door_count, $work_day_to_end_date, $work_day_to_fact_date) {
/*
$door_count - всего дверей со статусом "в очереди" и "запущен"
$work_day_to_end_date - кол-во раб дней до крайней даты готовности в журнале из заказов со статусом "в очереди" и "запущен"
$work_day_to_fact_date - кол-во раб дней нужных на обработку всего объема
*/
	$a = $door_count / $work_day_to_end_date;
	$b = $door_count / $work_day_to_fact_date;
	$repcent = ($a * 100) / $b;

    return $repcent;
} 
//функция определяет цвет прогресс-бара загруженности производства
function factory_workload_pb_color($persent) {
	if ($persent <= 50) {
		$pb_color = 'success';
	}
	if ($persent >= 51 && $persent <= 80) {
		$pb_color = 'warning';
	}
	if ($persent >= 81 && $persent <= 100) {
		$pb_color = 'danger';
	}
	if ($persent > 100) {
		$pb_color = 'danger';
	}
    return $pb_color;
} 

//функция считает колличество дней в указанном месяце за минусом всех воскресений (производство работает 6 рабочих дней пн - сб)
function count_work_day_factory($current_month) { 
/* $current_month - номер месяца */
$startdate = strtotime(date('Y-'.$current_month.'-01'));
$enddate = strtotime(date('Y-'.$current_month.'-t'));
$dow = 0; /* где вс = 0, сб = 6 */
$dow2 = 6; /* где вс = 0, сб = 6 */
$i = 0; 

while($startdate<=$enddate) {  
	if(date('w',$startdate)==$dow || date('w',$startdate)==$dow2) $i++; 
	$startdate+=86400; 
} 

$i = date('t') - $i;
return $i; 
} 

//функция считает колличество рабочих дней между двумя датами
function count_work_day_factory_date_to_date($startdate, $enddate) { 
/*
*/
$startdate_1 = strtotime($startdate); 
$enddate_1 = strtotime($enddate);
$dow = 0; /* где вс = 0, сб = 6 */
$dow2 = 6; /* где вс = 0, сб = 6 */
$i = 0; 

while($startdate_1<=$enddate_1) {  
	if(date('w',$startdate_1)==$dow || date('w',$startdate_1)==$dow2) $i++; 
	$startdate_1+=86400; 
} 

$count_day_date_to_date = (strtotime($enddate) - strtotime($startdate)) / 86400;

$i_work_day = $count_day_date_to_date - $i;

return $i_work_day; 
} 

//функция выводит фамилию и имя
// ФИО = Фамилия Имя
function name_format_l_n($in) { 
	$user_fio_string = explode(" ", $in); 
	$f = $user_fio_string[0];
	$i = $user_fio_string[1];
	$o = $user_fio_string[2];
	$user_fio = $f.' '.$i;
    return $user_fio;
} 

//Функция добавления оповещения в чат о смене статуса заявки (c файлом)
function bx24_im_mes_add_file_98($message, $color, $blank_name, $blank_link, $order_name, $order_num, $manager_name, $date_end, $comments) { 

if ($comments == '') {
	$comments = 'без комментария...';
} else {
	$comments = $comments;
}

$data = call($_SESSION["query_data"]["domain"], "im.message.add", Array(
   "auth" => $_SESSION["query_data"]["access_token"],	
   "CHAT_ID" => 98,
   "MESSAGE" => $message,
   "ATTACH" => Array(
      "ID" => 1,
      "COLOR" => $color,
      "BLOCKS" => Array(
		Array("LINK" => Array(
			"NAME" => $order_name, //имя ссылки
			//"DESC" => "Необходимо реализовать к релизу!", //описание под ссылкой
			"LINK" => "http://crm.ei-60.online/order_info.php?num=".$order_num //http://crm.ei-60.online/order_info.php?id=
		)),
		Array("GRID" => Array(
		Array(
			"NAME" => "Менеджер",
			"VALUE" => $manager_name,
			"DISPLAY" => "COLUMN"
		),
		Array(
			"NAME" => "Дата готовности",
			"VALUE" => $date_end,
			"DISPLAY" => "COLUMN"
		),
		Array(
			"NAME" => "Комментарий",
			"VALUE" => $comments,
			"DISPLAY" => "COLUMN"
		),
		)),
		Array("FILE" => Array(
			"NAME" => $blank_name,
			"LINK" => $blank_link    //http://crm.ei-60.online/uploads/apps_files/2017/7/7_blank_2017_04_06_14_14_20.xls
		)),
))));
} 


//Функция добавления оповещения в чат об обновлении бланка заказа (c файлом)
function bx24_im_mes_add_file_upd_98($message, $color, $blank_name, $blank_link, $order_name, $order_num, $manager_name, $manager_name_update, $date_end, $comments) { 

if ($comments == '') {
	$comments = 'без комментария...';
} else {
	$comments = $comments;
}

$data = call($_SESSION["query_data"]["domain"], "im.message.add", Array(
   "auth" => $_SESSION["query_data"]["access_token"],	
   "CHAT_ID" => 98,
   "MESSAGE" => $message,
   "ATTACH" => Array(
      "ID" => 1,
      "COLOR" => $color,
      "BLOCKS" => Array(
		Array("LINK" => Array(
			"NAME" => $order_name, //имя ссылки
			//"DESC" => "Необходимо реализовать к релизу!", //описание под ссылкой
			"LINK" => "http://crm.ei-60.online/order_info.php?num=".$order_num //http://crm.ei-60.online/order_info.php?id=
		)),
		Array("GRID" => Array(
		Array(
			"NAME" => "Менеджер",
			"VALUE" => $manager_name,
			"DISPLAY" => "COLUMN"
		),
		Array(
			"NAME" => "Кто обновил",
			"VALUE" => $manager_name_update,
			"DISPLAY" => "COLUMN"
		),
		Array(
			"NAME" => "Обновленная дата готовности",
			"VALUE" => $date_end,
			"DISPLAY" => "COLUMN"
		),
		Array(
			"NAME" => "Комментарий",
			"VALUE" => $comments,
			"DISPLAY" => "COLUMN"
		),
		)),
		Array("FILE" => Array(
			"NAME" => $blank_name,
			"LINK" => $blank_link    //http://crm.ei-60.online/uploads/apps_files/2017/7/7_blank_2017_04_06_14_14_20.xls
		)),
))));
} 


//Функция добавления оповещения в чат об изменениях в заявке
function bx24_im_mes_add_98($message) { 
$data = call($_SESSION["query_data"]["domain"], "im.message.add", Array(
   "auth" => $_SESSION["query_data"]["access_token"],	
   "CHAT_ID" => 98,
   "MESSAGE" => $message,
));
}

//Функция оповещения о смене статуса заявки
function bx24_im_mes_status_98($message, $comment_status, $color, $order_num, $order_name) { 
$data = call($_SESSION["query_data"]["domain"], "im.message.add", Array(
   "auth" => $_SESSION["query_data"]["access_token"],	
   "CHAT_ID" => 98,
   "MESSAGE" => $message."[BR]".$comment_status,
   "ATTACH" => Array(
      "ID" => 1,
      "COLOR" => $color,
      "BLOCKS" => Array(
		Array("LINK" => Array(
			"NAME" => $order_name, 
			"LINK" => "http://crm.ei-60.online/order_info.php?num=".$order_num
		)),
))));
}  

//Функция оповещения о смене статуса заявки в личный чат запустившего
function bx24_im_mes_status_who_user_id($who_user_id, $message, $comment_status, $color, $order_num, $order_name) { 
$data = call($_SESSION["query_data"]["domain"], "im.message.add", Array(
   "auth" => $_SESSION["query_data"]["access_token"],	
   "USER_ID" => $who_user_id,
   "MESSAGE" => $message."[BR]".$comment_status,
   "ATTACH" => Array(
      "ID" => 1,
      "COLOR" => $color,
      "BLOCKS" => Array(
		Array("LINK" => Array(
			"NAME" => $order_name, 
			"LINK" => "http://crm.ei-60.online/order_info.php?num=".$order_num
		)),
))));
} 
?>