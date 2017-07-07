<?php
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

//получение текущего времени жизни токена
if(isset($_GET['datarequest']) && $_GET['datarequest'] == 'tokenlive'){
	//что бы исключить глюки и т.д. искуственно уменьшаем время жизни токена на 15 минут (900000 мс)
	function responseTokenLive() {
		if (time() > $_SESSION["query_data"]["ts"] + $_SESSION["query_data"]["expires_in"] - 900) {
			$token_live = 0;
		} else {
			$token_live = $_SESSION["query_data"]["ts"] + $_SESSION["query_data"]["expires_in"] - 900 - time().'000';
		}
	return $token_live;
	}
echo responseTokenLive();
}

//обработка кнопок статуса заказа
//кнопка готовности из списка заказов
if(isset($_GET['btn']) && $_GET['btn'] == 'app_ready'){
	$stmt = $dbConnection->prepare("UPDATE crm_applist SET status = 'app-ready' WHERE num=:num");
	$stmt->bindParam(':num', $_GET['num']);
	$stmt->execute();
		
	if($stmt){	
	echo 'upd_ok'; // статус успешно обновлен
	//добавляем информацию об обновлении
	$status_add = array(
		'app_num' => $_GET['num'],
		'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
		'bitrix24_client_name' => getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name'),
		'status' => 'app-ready',
		'comments' => '',
		'date' => date("Y-m-d H:i:s")
	);  
	$stmt2 = $dbConnection->prepare("INSERT INTO crm_apps_status (
		app_num,
		bitrix24_client_id,
		bitrix24_client_name,
		status,
		comments,
		date
	) values (
		:app_num,
		:bitrix24_client_id,
		:bitrix24_client_name,
		:status,
		:comments,
		:date
	)");  

	$stmt2->execute($status_add);
	
	//оповещение в чат Битрикс 24
	if ($stmt2) {
	bx24_im_mes_status_98(
		"Заказ №".appnumclear($_GET['num'])." ГОТОВ к отгрузке!",
		"#008000",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	}
	}else{
	echo 'die'; // проблемы на сервере, статус не обновлен	
	}
}

//кнопки статуса заказа на странице заявки
//status_btn_ready
//status_btn_production
//status_btn_suspended
//status_btn_stopped
//status_btn_shipped

//кнопка готовности
if(isset($_GET['btn']) && $_GET['btn'] == 'status_btn_ready'){
	$stmt = $dbConnection->prepare("UPDATE crm_applist SET status = 'app-ready' WHERE num=:num");
	$stmt->bindParam(':num', $_GET['num']);
	$stmt->execute();
		
	if($stmt){	
	echo 'upd_ok'; // статус успешно обновлен
	//добавляем информацию об обновлении
	$status_add = array(
		'app_num' => $_GET['num'],
		'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
		'bitrix24_client_name' => getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name'),
		'status' => 'app-ready',
		'comments' => $_GET['reason'],
		'date' => date("Y-m-d H:i:s")
	);  
	$stmt2 = $dbConnection->prepare("INSERT INTO crm_apps_status (
		app_num,
		bitrix24_client_id,
		bitrix24_client_name,
		status,
		comments,
		date
	) values (
		:app_num,
		:bitrix24_client_id,
		:bitrix24_client_name,
		:status,
		:comments,
		:date
	)");  

	$stmt2->execute($status_add);
	
	//оповещение в чат Битрикс 24
	if ($stmt2) {
	if ($_GET['reason'] == '') {
		$GETreason = '';
	} else {
		$GETreason = $_GET['reason'];
	}
	bx24_im_mes_status_98(
		"[B]Заказ №".appnumclear($_GET['num'])." ГОТОВ к отгрузке![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#008000",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	}
	}else{
	echo 'die'; // проблемы на сервере, статус не обновлен	
	}
}

//кнопка запуска ПРОИЗВОДСТВОМ
if(isset($_GET['btn']) && $_GET['btn'] == 'status_btn_gotobuilding'){
	$stmt = $dbConnection->prepare("UPDATE crm_applist SET status = 'app-gotobuilding' WHERE num=:num");
	$stmt->bindParam(':num', $_GET['num']);
	$stmt->execute();
		
	if($stmt){	
	echo 'upd_ok'; // статус успешно обновлен
	//добавляем информацию об обновлении
	$status_add = array(
		'app_num' => $_GET['num'],
		'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
		'bitrix24_client_name' => getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name'),
		'status' => 'app-gotobuilding',
		'comments' => $_GET['reason'],
		'date' => date("Y-m-d H:i:s")
	);  
	$stmt2 = $dbConnection->prepare("INSERT INTO crm_apps_status (
		app_num,
		bitrix24_client_id,
		bitrix24_client_name,
		status,
		comments,
		date
	) values (
		:app_num,
		:bitrix24_client_id,
		:bitrix24_client_name,
		:status,
		:comments,
		:date
	)");  

	$stmt2->execute($status_add);
	
	//оповещение в чат Битрикс 24
	if ($stmt2) {
	if ($_GET['reason'] == 'без комментария...') {
		$GETreason = 'без комментария...';
	} else {
		$GETreason = $_GET['reason'];
	}
	bx24_im_mes_status_98(
		"[B]Заказ №".appnumclear($_GET['num'])." ЗАПУЩЕН в производство![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#000000",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	
	//оповещение в чат сотрудника
	bx24_im_mes_status_who_user_id(
		$_GET['userid_app_start'],
		"[B]Заказ №".appnumclear($_GET['num'])." ЗАПУЩЕН в производство![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#000000",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	
	}
	}else{
	echo 'die'; // проблемы на сервере, статус не обновлен	
	}
}

//кнопка исполнения
if(isset($_GET['btn']) && $_GET['btn'] == 'status_btn_production'){
	$stmt = $dbConnection->prepare("UPDATE crm_applist SET status = 'app-production' WHERE num=:num");
	$stmt->bindParam(':num', $_GET['num']);
	$stmt->execute();
		
	if($stmt){	
	echo 'upd_ok'; // статус успешно обновлен
	//добавляем информацию об обновлении
	$status_add = array(
		'app_num' => $_GET['num'],
		'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
		'bitrix24_client_name' => getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name'),
		'status' => 'app-production',
		'comments' => $_GET['reason'],
		'date' => date("Y-m-d H:i:s")
	);  
	$stmt2 = $dbConnection->prepare("INSERT INTO crm_apps_status (
		app_num,
		bitrix24_client_id,
		bitrix24_client_name,
		status,
		comments,
		date
	) values (
		:app_num,
		:bitrix24_client_id,
		:bitrix24_client_name,
		:status,
		:comments,
		:date
	)");  

	$stmt2->execute($status_add);
	
	//оповещение в чат Битрикс 24
	if ($stmt2) {
	if ($_GET['reason'] == '') {
		$GETreason = '';
	} else {
		$GETreason = $_GET['reason'];
	}
	bx24_im_mes_status_98(
		"[B]Заказ №".appnumclear($_GET['num'])." ДОБАВЛЕН В ОЧЕРЕДЬ производства![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#000000",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	}
	}else{
	echo 'die'; // проблемы на сервере, статус не обновлен	
	}
}

//кнопка приостановки
if(isset($_GET['btn']) && $_GET['btn'] == 'status_btn_suspended'){
	$stmt = $dbConnection->prepare("UPDATE crm_applist SET status = 'app-suspended' WHERE num=:num");
	$stmt->bindParam(':num', $_GET['num']);
	$stmt->execute();
		
	if($stmt){	
	echo 'upd_ok'; // статус успешно обновлен
	//добавляем информацию об обновлении
	$status_add = array(
		'app_num' => $_GET['num'],
		'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
		'bitrix24_client_name' => getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name'),
		'status' => 'app-suspended',
		'comments' => $_GET['reason'],
		'date' => date("Y-m-d H:i:s")
	);  
	$stmt2 = $dbConnection->prepare("INSERT INTO crm_apps_status (
		app_num,
		bitrix24_client_id,
		bitrix24_client_name,
		status,
		comments,
		date
	) values (
		:app_num,
		:bitrix24_client_id,
		:bitrix24_client_name,
		:status,
		:comments,
		:date
	)");  

	$stmt2->execute($status_add);
	
	//оповещение в чат Битрикс 24
	if ($stmt2) {
	if ($_GET['reason'] == '') {
		$GETreason = '';
	} else {
		$GETreason = $_GET['reason'];
	}
	bx24_im_mes_status_98(
		"[B]Заказ №".appnumclear($_GET['num'])." ПРИОСТАНОВЛЕН![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#FFA500",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	
	//оповещение в чат сотрудника
	bx24_im_mes_status_who_user_id(
		$_GET['userid_app_start'],
		"[B]Заказ №".appnumclear($_GET['num'])." ПРИОСТАНОВЛЕН![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#FFA500",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	}
	}else{
	echo 'die'; // проблемы на сервере, статус не обновлен	
	}
}	

//кнопка отмены
if(isset($_GET['btn']) && $_GET['btn'] == 'status_btn_stopped'){
	$stmt = $dbConnection->prepare("UPDATE crm_applist SET status = 'app-stopped' WHERE num=:num");
	$stmt->bindParam(':num', $_GET['num']);
	$stmt->execute();
		
	if($stmt){	
	echo 'upd_ok'; // статус успешно обновлен
	//добавляем информацию об обновлении
	$status_add = array(
		'app_num' => $_GET['num'],
		'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
		'bitrix24_client_name' => getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name'),
		'status' => 'app-stopped',
		'comments' => $_GET['reason'],
		'date' => date("Y-m-d H:i:s")
	);  
	$stmt2 = $dbConnection->prepare("INSERT INTO crm_apps_status (
		app_num,
		bitrix24_client_id,
		bitrix24_client_name,
		status,
		comments,
		date
	) values (
		:app_num,
		:bitrix24_client_id,
		:bitrix24_client_name,
		:status,
		:comments,
		:date
	)");  

	$stmt2->execute($status_add);
	
	//оповещение в чат Битрикс 24
	if ($stmt2) {
	if ($_GET['reason'] == '') {
		$GETreason = '';
	} else {
		$GETreason = $_GET['reason'];
	}
	bx24_im_mes_status_98(
		"[B]Заказ №".appnumclear($_GET['num'])." ОТМЕНЁН![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#FF69B4",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	}
	}else{
	echo 'die'; // проблемы на сервере, статус не обновлен	
	}
}	

//кнопка отгрузки
if(isset($_GET['btn']) && $_GET['btn'] == 'status_btn_shipped'){
	$stmt = $dbConnection->prepare("UPDATE crm_applist SET status = 'app-shipped' WHERE num=:num");
	$stmt->bindParam(':num', $_GET['num']);
	$stmt->execute();
		
	if($stmt){	
	echo 'upd_ok'; // статус успешно обновлен
	//добавляем информацию об обновлении
	$status_add = array(
		'app_num' => $_GET['num'],
		'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
		'bitrix24_client_name' => getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name'),
		'status' => 'app-shipped',
		'comments' => $_GET['reason'],
		'date' => date("Y-m-d H:i:s")
	);  
	$stmt2 = $dbConnection->prepare("INSERT INTO crm_apps_status (
		app_num,
		bitrix24_client_id,
		bitrix24_client_name,
		status,
		comments,
		date
	) values (
		:app_num,
		:bitrix24_client_id,
		:bitrix24_client_name,
		:status,
		:comments,
		:date
	)");  

	$stmt2->execute($status_add);
	
	//оповещение в чат Битрикс 24
	if ($stmt2) {
	if ($_GET['reason'] == '') {
		$GETreason = '';
	} else {
		$GETreason = $_GET['reason'];
	}
	bx24_im_mes_status_98(
		"[B]Заказ №".appnumclear($_GET['num'])." ОТГРУЖЕН с производства![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#FF69B4",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	
	//оповещение в чат сотрудника
	bx24_im_mes_status_who_user_id(
		$_GET['userid_app_start'],
		"[B]Заказ №".appnumclear($_GET['num'])." ОТГРУЖЕН с производства![/B]",
		"[B]Комментарий: [/B]".$GETreason,
		"#FF69B4",
		$_GET['num'],
		">> открыть заказ в журнате заявок <<"
	);
	}
	}else{
	echo 'die'; // проблемы на сервере, статус не обновлен	
	}
}	

//кнопка отметки полной оплаты по счету
if(isset($_GET['btn']) && $_GET['btn'] == 'postpay_bill'){
	$bill_postpay = $_GET['bill_total'] - $_GET['bill_prepay'];
	$bill_prepay = $_GET['bill_total'];
	$name_manager = getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name');
	$stmt = $dbConnection->prepare("UPDATE crm_bills SET bill_prepay = '".$bill_prepay."',bill_postpay = '".$bill_postpay."', postpay_user_name = '".$name_manager."', postpay_date = '".date("Y-m-d H:i:s")."' WHERE id=:id");
	$stmt->bindParam(':id', $_GET['bill_id']);
	$stmt->execute();
		
	if($stmt){	
	echo 'upd_ok'; // успешно

	}else{
	echo 'die'; // проблемы на сервере
	}
}	


if(isset($_REQUEST["refresh"]) && $_GET['refresh'] == 1) {
	
	$params = array(
		"grant_type" => "refresh_token",
		"client_id" => CLIENT_ID,
		"client_secret" => CLIENT_SECRET,
		"redirect_uri" => 'http://crm.ei-60.online'.$_GET['page'], //REDIRECT_URI,
		"scope" => SCOPE,
		"refresh_token" => $_SESSION["query_data"]["refresh_token"],
	);

	$path = "/oauth/token/";

	$query_data = query("GET", PROTOCOL."://".$_SESSION["query_data"]["domain"].$path, $params);

	if(isset($query_data["access_token"]) && isset($_SESSION["auth_email"]))
	{
		$_SESSION["query_data"] = $query_data;
		$_SESSION["query_data"]["ts"] = time();
		print ("token_ok");
		die();
	}
	else
	{
		$_SESSION["query_data"]["ts"] = 0;
		print ("token_error");
	}
} /* else {
 echo '1234 <br><br>';
 echo $_SERVER['PHP_SELF'];
} */
/********************* /refresh auth ******************************/

// ------------------------------------------------------------ ПРОЧСЕТ ЗАГРУЖЕННОСТИ --------------------------------------------------------------
//кнопка просчета даты текущей загруженности
if(isset($_GET['btn']) && $_GET['btn'] == 'calc_date_ready'){

// Кол-во продукции в сутки 50 (45 основная мощьность, 5 оставляем для мелких заявок)
$output_per_day = 45;
$output_per_day_smallapp = 5;

// Мощность производства в текущем месяце (по основным заявкам)
$output_per_month = count_work_day_factory(date('m')) * $output_per_day;

// Мощность производства в текущем месяце (по мелким заявкам)
$output_per_month_smallapp = count_work_day_factory(date('m')) * $output_per_day_smallapp;

// Мощность производства в текущем месяце (итого)
$output = $output_per_month + $output_per_month_smallapp;

// 

		// Получаем кол-во заявок со статусом "в очереди" и "запущен" -- если за текущий месяц, то MONTH(`date_start`) = MONTH(NOW()) --
		$stmt_1 = $dbConnection->query('SELECT * from crm_applist WHERE status = "app-gotobuilding" OR status = "app-production"');
		$app_to_factory = $stmt_1->fetchAll();
		$app_to_factory_count = $stmt_1->rowCount();
		
		// Считаем сумму дверей по заявкам со статусом "в очереди" и "запущен"
		$app_c_summ = 0;
		$app_c_summ_smallapp = 0;
		foreach($app_to_factory as $app_to_factory_row) {			
			if ($app_to_factory_row['c_summ'] > 5) {
				$app_c_summ = $app_c_summ + $app_to_factory_row['c_summ'];
			} else {
				$app_c_summ_smallapp = $app_c_summ_smallapp + $app_to_factory_row['c_summ'];
			}
		}
		$app_c_summ_all = $app_c_summ + $app_c_summ_smallapp;
		
		// Получаем кол-во заявок со статусом "в очереди" и "запущен" а текущий месяц 
		$stmt_2 = $dbConnection->query('SELECT * from crm_applist WHERE MONTH(`date_start`) = MONTH(NOW()) AND status = "app-gotobuilding" OR status = "app-production"');
		$app_to_factory_month = $stmt_2->fetchAll();
		$app_to_factory_month_count = $stmt_2->rowCount();
		
		// ----------
		// Получаем крайнюю дату отгрузки 
		$stmt_3 = $dbConnection->query('SELECT * from crm_applist WHERE status = "app-gotobuilding" OR status = "app-production" ORDER BY date_ready DESC LIMIT 1');
		$app_end_date_ready = $stmt_3->fetchAll();
		$app_end_date_ready_count = $stmt_3->rowCount();
		// ----------
		
		// Считаем сумму дверей по заявкам со статусом "в очереди" и "запущен"
		$app_c_summ_month = 0;
		$app_c_summ_month_smallapp = 0;
		foreach($app_to_factory_month as $app_to_factory_month_row) {
			if ($app_to_factory_month_row['c_summ'] > 5) {
				$app_c_summ_month = $app_c_summ_month + $app_to_factory_month_row['c_summ'];
			} else {
				$app_c_summ_month_smallapp = $app_c_summ_month_smallapp + $app_to_factory_month_row['c_summ'];
			}
		}
		
// просчеты

$a = count_work_day_factory(date('m')) - date('d'); // сколько осталось рабочих дней в текущем месяце
$b = count_work_day_factory(date('m' + 1)); // сколько рабочих дней в следующем месяце
$c = ceil($app_c_summ_all / ($output_per_day + $output_per_day_smallapp)); // сколько нужно дней на обработку текущих запущенных заказов
$dd = $c - $a; // забили текущий месяц и получили остаток раб дней 
$ee = $b - $dd; // забили следующий месяц и получили номер последнего рабочего дня в следующем месяце

// --- считаем введёные данные пользователем ---
	$val_1 = $_GET['val_1'];
	$val_2 = $_GET['val_2'];
	$val_B = $_GET['val_B'];
	$val_L = $_GET['val_L'];
	$val_F = $_GET['val_F'];
	$val_NONE = $_GET['val_NONE'];
	
if (empty($val_1) || $val_1 == "0" || !is_numeric($val_1)) {
	$val_1 = 0;
}
if (empty($val_2) || $val_2 == "0" || !is_numeric($val_2)) {
	$val_2 = 0;
}
if (empty($val_B) || $val_B == "0" || !is_numeric($val_B)) {
	$val_B = 0;
}
if (empty($val_L) || $val_L == "0" || !is_numeric($val_L)) {
	$val_L = 0;
}
if (empty($val_F) || $val_F == "0" || !is_numeric($val_F)) {
	$val_F = 0;
}
if (empty($val_NONE) || $val_NONE == "0" || !is_numeric($val_NONE)) {
	$val_NONE = 0;
}
	
	$val_SUMM = $val_1 + $val_2 + $val_B + $val_L + $val_F + $val_NONE; // общая сумма
	
// --- считаем введёные данные пользователем ---

$aa1 = working_days_factory($c); // дата завершения производства всех текущих заказов
$bb1 = ceil($val_SUMM / ($output_per_day + $output_per_day_smallapp)); // предполагаемый заказ 82 двери делим на производительность 50 дверей, получаем кол-во рабочих дней на обработку (округляем в большую сторону)
$cc1 = working_days_factory_finish($aa1, $bb1);

$nn1 = count_work_day_factory_date_to_date(date("Y-m-d"), d_format($app_end_date_ready[0]["date_ready"], "f9")); // Кол-во рабочих дней производства от текущей даты до даты готовности крайней заявки
$nn2 = count_work_day_factory_date_to_date(date("Y-m-d"), d_format($aa1, "f9")); // Кол-во рабочих дней производства от текущей даты до фактической даты готовности всего объема с учетом производительности

$date_calc_ready = d_format($cc1, 'f1');
$count_day_calc_ready = $bb1 + $c;

$factory_load = ceil(factory_workload($app_c_summ_all, $nn1, $nn2));

if ($factory_load > 100) {
	$factory_load_progress_bar = 100;
}

$factory_load_pb_color = factory_workload_pb_color($factory_load);


	echo '<h3><b>'.$date_calc_ready.'</b></h3> <h4>рабочих дней: <u>'.$count_day_calc_ready.'</u></h4>'; // выводим результат

}
// ------------------------------------------------------------ ПРОЧСЕТ ЗАГРУЖЕННОСТИ /конец/ ------------------------------------------------------

// --- СРМ_АДМИНКА ---

// права доступа
if(isset($_GET['access']) && $_GET['access'] == 'fullacc'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET fullacc = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}	

if(isset($_GET['access']) && $_GET['access'] == 'unacc'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET unacc = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}	

if(isset($_GET['access']) && $_GET['access'] == 'director'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET director = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}	

if(isset($_GET['access']) && $_GET['access'] == 'director_dev'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET director_dev = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}	

if(isset($_GET['access']) && $_GET['access'] == 'accountant'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET accountant = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}

if(isset($_GET['access']) && $_GET['access'] == 'factory_head'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET factory_head = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}

if(isset($_GET['access']) && $_GET['access'] == 'factory_ec'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET factory_ec = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}

if(isset($_GET['access']) && $_GET['access'] == 'salesteam_manager'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET salesteam_manager = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}

if(isset($_GET['access']) && $_GET['access'] == 'salesteam_manager_the_call'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET salesteam_manager_the_call = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}

if(isset($_GET['access']) && $_GET['access'] == 'salesteam_manager_extended'){
	if ($_GET['crm_access'] == 'toggle-on') {
		$crm_access = 0;
	} if ($_GET['crm_access'] == 'toggle-off') {
		$crm_access = 1;
	}
	
	$stmt = $dbConnection->prepare("UPDATE crm_access SET salesteam_manager_extended = :crm_access WHERE id=:id");
	$stmt->bindParam(':id', $_GET['id']);
	$stmt->bindParam(':crm_access', $crm_access);
	$stmt->execute();

	if($stmt){	
		if ($crm_access == 0) {
			echo 'toggle-off'; // меняем статус на 0 - запрещён
		} if ($crm_access == 1) {
			echo 'toggle-on'; // меняем статус на 1 - разрешён
		}
	}
}

// --- СРМ_АДМИНКА /конец/ ---

// обновляем данные по заказу (на странице информации о заказе - соответствующий блок)
if(isset($_GET['btn']) && $_GET['btn'] == 'edit-btn-info-order'){
	
	echo 'ok_upd';
	
	$stmt = $dbConnection->prepare("UPDATE crm_applist SET
		c_single = :c_single,
		c_double = :c_double,
		c_gates = :c_gates,
		c_hatches = :c_hatches,
		c_transoms = :c_transoms,
		c_others = :c_others,
		c_summ = :c_summ,
		bitrix24_kontragent_id = :kontragent_id,
		bitrix24_kontragent_name = :kontragent_text
		WHERE num=:num");
	$stmt->bindParam(':num', $_GET['num']);
	$stmt->bindParam(':c_single', $_GET['c_single']);
	$stmt->bindParam(':c_double', $_GET['c_double']);
	$stmt->bindParam(':c_gates', $_GET['c_gates']);
	$stmt->bindParam(':c_hatches', $_GET['c_hatches']);
	$stmt->bindParam(':c_transoms', $_GET['c_transoms']);
	$stmt->bindParam(':c_others', $_GET['c_others']);
	$stmt->bindParam(':c_summ', $_GET['c_summ']);
	$stmt->bindParam(':kontragent_id', $_GET['kontragent_id']);
	$stmt->bindParam(':kontragent_text', $_GET['kontragent_text']);
	$stmt->execute();
	
}
?>	