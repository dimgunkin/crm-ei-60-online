<?php
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = $CONFIG['title_header'];

//подключаем верстку
include("head.inc.php"); //шапка сайта
include("navbarleft.inc.php"); //боковое меню навигации
include("navbartop.inc.php"); //верхнее меню навигации
			
?>	
<!-- начало тела станицы -->

        <!--body wrapper start-->
	<div class="wrapper">
		<div class="row">
			<div class="col-sm-12">
				<section class="panel">
					<header class="panel-heading">
						Системное сообщение
					</header>
					<div class="panel-body">
						<!-- <button class="btn btn-success btn-lg btn-block" type="button"><i class="fa fa-plus"></i> Запустить новый заказ </button> 
						<a class="btn btn-success btn-lg btn-block">
                            <i class="fa fa-plus"></i> зппззппзпззп
                        </a> -->
						
					<br>
					
					
<?
//добавление нового заказа ----------------------------------------------------------------------------------------------------------
if (isset($_POST['app_add_btn'])) {
					//echo getUserInfo($_SESSION["auth_id"], 'name').', ваш заказ успешно запущен в производство';

					
					

	//определяем номер следующего заказа
	$stmt = $dbConnection->query('SELECT * from crm_applist ORDER BY num DESC');
	$next_app_num = $stmt->fetchAll();
	$next_app_num = appnumclear($next_app_num[0]['num']);
	$next_app_num_clear = $next_app_num + 1;
	$next_app_num = $next_app_num_clear.date("Y");
	
	//определяем id следующего счета
	$stmt = $dbConnection->query('SELECT * from crm_bills ORDER BY id DESC');
	$next_bill_id = $stmt->fetchAll();
	$next_bill_id = $next_bill_id[0]['id'] + 1;
	
	//переменная с контрагентом в Битриксе 24 (ид_название)
	$bitrix24_kontragent = explode("_", $_POST["bitrix24_kontragent"]); 

	//имя менеджера (фио)
	$name_manager = getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name').' '.getUserInfo($_SESSION["auth_id"], 'second_name');
	//имя менеджера (ф и.)
	$name_manager_short = getUserInfo($_SESSION["auth_id"], 'last_name').' '.substr(getUserInfo($_SESSION["auth_id"], 'name'), 0, 2).'.';
	
	
	
	

/* 	//добавляем новый счет в Битрикс24
	$btx24_bill_add = call($_SESSION["query_data"]["domain"], "crm.invoice.add", array(
	"auth" => $_SESSION["query_data"]["access_token"],
	"fields" => array(
				"ORDER_TOPIC" => "Заказ №".$next_app_num_clear,
                "STATUS_ID" => "Y",
                "DATE_INSERT" => date("m.d.y"),
                "PAY_VOUCHER_DATE" => date("m.d.y"),
                "PAY_VOUCHER_NUM" => "",
                "DATE_MARKED" => date("m.d.y"),
                "REASON_MARKED" => "Счёт оплачен сразу.",
                "COMMENTS" => "комментарий менеджера",
                "USER_DESCRIPTION" => "комментарий для клиента",
                "DATE_BILL" => date("m.d.y"),
                "DATE_PAY_BEFORE" => date("m.d.y"),
                "RESPONSIBLE_ID" => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'), //id ответственного в битрикс 24
				"UF_DEAL_ID" =>  10,
                "UF_COMPANY_ID" =>  2,
                "UF_CONTACT_ID" =>  0,
                "PERSON_TYPE_ID" =>  2,
				"UF_CRM_1491412798" => $bill_number, //счет в 1С
				"UF_CRM_1491418099" => "http://crm.ei-60.online/order_view.php?id=".$next_app_num_clear."", //ссылка на заказ в ЖУРНАЛЕ
                "PAY_SYSTEM_ID" => 2,
				"INVOICE_PROPERTIES" => array(
                    "COMPANY" => "",                              // Название компании
                    "COMPANY_ADR" => "",   // Юридический адрес
                    "INN" => "",                                                          // ИНН
                    "KPP" => "",                                                          // КПП
                    "CONTACT_PERSON" => "",                                  // Контактное лицо
                    "EMAIL" => "",                                  // E-Mail
                    "PHONE" => "",                                       // Телефон
                    "FAX" => "",                                                          // Факс
                    "ZIP" => "",                                                          // Индекс
                    "CITY" => "",                                                         // Город
                    "LOCATION" => "",                                                     // Местоположение
                    "ADDRESS" => ""                                                       // Адрес доставки
                ),
                "PRODUCT_ROWS" => array("row" =>
                    array("ID" => 1, "PRODUCT_ID" => 2, "PRODUCT_NAME" => "Продукция", "QUANTITY" => 1, "PRICE" => $_POST["bill_total"], "DISCOUNT_PRICE" => 0),
					array("ID" => 2, "PRODUCT_ID" => 2, "PRODUCT_NAME" => "Оплачено", "QUANTITY" => 1, "PRICE" => $_POST["bill_prepay"], "DISCOUNT_PRICE" => 0),
					array("ID" => 3, "PRODUCT_ID" => 2, "PRODUCT_NAME" => "Доставка", "QUANTITY" => 1, "PRICE" => $_POST["bill_transfer"], "DISCOUNT_PRICE" => 0),
					array("ID" => 4, "PRODUCT_ID" => 2, "PRODUCT_NAME" => "Монтаж", "QUANTITY" => 1, "PRICE" => $_POST["bill_bulding"], "DISCOUNT_PRICE" => 0),
					array("ID" => 5, "PRODUCT_ID" => 2, "PRODUCT_NAME" => "Бонус менеджера", "QUANTITY" => 1, "PRICE" => $_POST["bill_bonus"], "DISCOUNT_PRICE" => 0))

)));	//echo '<pre>'; var_export($btx24_bill_add); echo '</pre>'; */



//добавляем информацию о счете в базу
//проверка на пустоту полей инфо о счетах
if ($_POST["pay_metod"] == 'bill') {
	$bill_number = $_POST["bill_number"];
} else {
	$bill_number = 'Без счета';
}

$bill_total = $_POST["bill_total"];
$bill_prepay = $_POST["bill_prepay"];
$bill_transfer = $_POST["bill_transfer"];
$bill_bulding = $_POST["bill_bulding"];
$bill_bonus = $_POST["bill_bonus"];

if (empty($bill_total) || $bill_total == "0" || !is_numeric($bill_total)) {
	$bill_total = 1;
}
if (empty($bill_prepay) || $bill_prepay == "0" || !is_numeric($bill_prepay)) {
	$bill_prepay = 0;
}
if (empty($bill_transfer) || $bill_transfer == "0" || !is_numeric($bill_transfer)) {
	$bill_transfer = 0;
}
if (empty($bill_bulding) || $bill_bulding == "0" || !is_numeric($bill_bulding)) {
	$bill_bulding = 0;
}
if (empty($bill_bonus) || $bill_bonus == "0" || !is_numeric($bill_bonus)) {
	$bill_bonus = 0;
}

//составляем запрос
$bill_add = array(
		'bitrix24_id' => 0,
		'app_id' => $next_app_num,
		'pay_metod' => $_POST["pay_metod"],
		'bill_number' => $bill_number,
		'bill_total' => $bill_total,
		'bill_prepay' => $bill_prepay,
		'bill_postpay' => 0,
		'postpay_user_name' => NULL,
		'postpay_date' => '0000-00-00 00:00:00',
		'bill_transfer' => $bill_transfer,
		'bill_bulding' => $bill_bulding,
		'bill_bonus' => $bill_bonus
);  

$stmt = $dbConnection->prepare("INSERT INTO crm_bills (
		bitrix24_id,
		app_id,
		pay_metod,
		bill_number,
		bill_total,
		bill_prepay,
		bill_postpay,
		postpay_user_name,
		postpay_date,
		bill_transfer,
		bill_bulding,
		bill_bonus
) values (
		:bitrix24_id,
		:app_id,
		:pay_metod,
		:bill_number,
		:bill_total,
		:bill_prepay,
		:bill_postpay,
		:postpay_user_name,
		:postpay_date,
		:bill_transfer,
		:bill_bulding,
		:bill_bonus
)");  

$stmt->execute($bill_add);


	//определяем файл заявки и переименуем его
	function trim_to_dot($string) {
		$pos = strrpos($string, '.'); // поиск позиции точки с конца строки
		if (!$pos) { return $string; }// если точка не найдена - возвращаем строку
	return substr($string, $pos + 1); } //substr($string, 0, $pos + 1);
	
	$app_file = basename($_FILES['app_file']['name']);
	$app_file_excel = $next_app_num_clear.'_blank_'.date("Y_m_d_H_i_s").'.'.trim_to_dot($app_file);

	
		// загружаем файлы на сервер...
		// создаем каталог с номером заявки
		mkdir($_SERVER['DOCUMENT_ROOT']."/uploads/apps_files/".date('Y')."/".$next_app_num_clear."/", 0700);
		
		// указываем путь загрузки файлов (созданный каталог)
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/uploads/apps_files/'.date('Y').'/'.$next_app_num_clear.'/';
		
		// переменные полного пути файлов
		$app_excel = $uploaddir.'upload_'.$app_file_excel;
		
		// переменные tmp файлов
		$app_excel_source = $_FILES['app_file']['tmp_name'];
		
		// Копируем файл из каталога для временного хранения файлов в основной каталог:
		move_uploaded_file($app_excel_source, $app_excel);
		

// работа над загруженным excel файлом-заявки ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// добавляем информацию в бланк заявки
		// --- подключаем библиотеку --- 
			include_once 'classes/PHPExcel.php';
			include 'classes/PHPExcel/IOFactory.php';
			$objPHPExcel = new PHPExcel();
		// --- открываем файл-шаблон --- 
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');
			$objPHPExcel = $objReader->load($app_excel);
			$objPHPExcel->setActiveSheetIndex(0);
		// --- заполняем таблицу --- 
			$objPHPExcel->getActiveSheet()->SetCellValue('E1', $next_app_num_clear); 				//Бланк №
			$objPHPExcel->getActiveSheet()->SetCellValue('E2', $bill_number);						//Счет №
			$objPHPExcel->getActiveSheet()->SetCellValue('E3', $name_manager_short);				//Менеджер
			$objPHPExcel->getActiveSheet()->SetCellValue('E4', date("d.m.Y H:i"));					//Дата запуска
			$objPHPExcel->getActiveSheet()->SetCellValue('E5', d_format(working_days($_POST["date_ready"]), 'f6'));	//Дата отгрузки
			$objPHPExcel->getActiveSheet()->SetCellValue('K1', "-");								//Дата обновления
			$objPHPExcel->getActiveSheet()->SetCellValue('K2', "-");								//Обновил(а)
			$objPHPExcel->getActiveSheet()->SetCellValue('K3', "-");								//Комментарий
		// --- записываем результат и сохраняем (бланк со всеми данными + системный лист) --- 	 
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save($uploaddir.$app_file_excel);
			
// добавили информацию
// считываем данные по дверям
$objPHPExcel2 = new PHPExcel();
// Открываем файл
$objPHPExcel2 = PHPExcel_IOFactory::load($uploaddir.$app_file_excel);
$objPHPExcel2->setActiveSheetIndex(0);
$objPHPExcel2 = $objPHPExcel2->getActiveSheet();		

$c_single = 0;
$c_double = 0;
$c_gates = 0;
$c_hatches = 0;
$c_transoms = 0;
$c_others = 0;

for ($i = 8; $i <= $objPHPExcel2->getHighestRow(); $i++) {
	$cellValue = $objPHPExcel2->getCell('B'.$i)->getValue();
	//проверка цикла
	//echo $i.' - '.$cellValue.'<br><br><br>';
	if ($cellValue == '' || $cellValue == null) {
		break;
	} else {
		if ($cellValue == 'Дверь EI-60') {
			$stvorka_count = $objPHPExcel2->getCell('E'.$i)->getValue();
			if ($stvorka_count == '') {
				$c_single = $c_single + $objPHPExcel2->getCell('N'.$i)->getValue();
			} else {
				$c_double = $c_double + $objPHPExcel2->getCell('N'.$i)->getValue();
			}
			
		}
		if ($cellValue == 'Дверь EIS-60') {
			$stvorka_count = $objPHPExcel2->getCell('E'.$i)->getValue();
			if ($stvorka_count == '') {
				$c_single = $c_single + $objPHPExcel2->getCell('N'.$i)->getValue();
			} else {
				$c_double = $c_double + $objPHPExcel2->getCell('N'.$i)->getValue();
			}
			
		}
		if ($cellValue == 'Дверь Техническая') {
			$stvorka_count = $objPHPExcel2->getCell('E'.$i)->getValue();
			if ($stvorka_count == '') {
				$c_single = $c_single + $objPHPExcel2->getCell('N'.$i)->getValue();
			} else {
				$c_double = $c_double + $objPHPExcel2->getCell('N'.$i)->getValue();
			}
		}
		if ($cellValue == 'Дверь Тех в исп EI-60') {
			$stvorka_count = $objPHPExcel2->getCell('E'.$i)->getValue();
			if ($stvorka_count == '') {
				$c_single = $c_single + $objPHPExcel2->getCell('N'.$i)->getValue();
			} else {
				$c_double = $c_double + $objPHPExcel2->getCell('N'.$i)->getValue();
			}
		}
		if ($cellValue == 'Ворота EI-60') {
				$c_gates = $c_gates + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Ворота EIS-60') {
				$c_gates = $c_gates + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Ворота Технические') {
				$c_gates = $c_gates + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Люк EI-60') {
				$c_hatches = $c_hatches + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Люк EIS-60') {
				$c_hatches = $c_hatches + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Люк Технический') {
				$c_hatches = $c_hatches + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Фрамуга EI-60') {
				$c_transoms = $c_transoms + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Фрамуга Техническая') {
				$c_transoms = $c_transoms + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if(!in_array($cellValue, $APP_NAMES_DOORS, true)) {
			$c_others = $c_others + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
	}
}

//считаем общую сумму позиций в заказе
$c_summ = $c_single + $c_double + $c_gates + $c_hatches + $c_transoms + $c_others;

//echo $c_single.' /1<br>';
//echo $c_double.' /2<br>';
//echo $c_gates.' /В<br>';
//echo $c_hatches.'/Л <br>';
//echo $c_transoms.'/Ф <br>';
//echo $c_others.'/Прочее <br>';
//echo '<br><br>';
//$c_summ = $c_single + $c_double + $c_gates + $c_hatches + $c_transoms + $c_others;
//echo $c_summ;
// работа над загруженным excel файлом-заявки ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


//добавляем новый заказ
$date_start_order = date("Y-m-d H:i:s");
$app_add = array(
  'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
  'bitrix24_client_name' => $name_manager,
  'employees_id' => $_SESSION["auth_id"],
  'num' => $next_app_num,
  'bill_id' => $next_bill_id,
  'bitrix24_kontragent_id' => $bitrix24_kontragent[0],
  'bitrix24_kontragent_name' => $bitrix24_kontragent[1],
  'status' => 'app-production',
  'c_single' => $c_single,
  'c_double' => $c_double,
  'c_gates' => $c_gates,
  'c_hatches' => $c_hatches,
  'c_transoms' => $c_transoms,
  'c_others' => $c_others,
  'c_summ' => $c_summ,
  'date_start' => $date_start_order,
  'date_ready' => working_days($_POST["date_ready"]),
  'date_ready_workload' => $_POST["date_ready_workload"],
  'work_day_workload' => $_POST["work_day_workload"],
  'percentage_workload' => $_POST["percentage_workload"],
  'comments' => $_POST["manager_comment"],
  'app_file_excel' => $app_file_excel,
  'app_file_excel_date' => $date_start_order
);  

$stmt2 = $dbConnection->prepare("INSERT INTO crm_applist (
  bitrix24_client_id,
  bitrix24_client_name,
  employees_id,
  num,
  bill_id,
  bitrix24_kontragent_id,
  bitrix24_kontragent_name,
  status,
  c_single,
  c_double,
  c_gates,
  c_hatches,
  c_transoms,
  c_others,
  c_summ,
  date_start,
  date_ready,
  date_ready_workload,
  work_day_workload,
  percentage_workload,
  comments,
  app_file_excel,
  app_file_excel_date
) values (
  :bitrix24_client_id,
  :bitrix24_client_name,
  :employees_id,
  :num,
  :bill_id,
  :bitrix24_kontragent_id,
  :bitrix24_kontragent_name,
  :status,
  :c_single,
  :c_double,
  :c_gates,
  :c_hatches,
  :c_transoms,
  :c_others,
  :c_summ,
  :date_start,
  :date_ready,
  :date_ready_workload,
  :work_day_workload,
  :percentage_workload,
  :comments,
  :app_file_excel,
  :app_file_excel_date
)");  

$stmt2->execute($app_add);

//если все ок, то рассылаем оповещения и выводим текст ответа пользователю + добавляем инфу о статусе заказа
if ($stmt2) {
	
//добавляем информацию о статусе заказа
	$status_add = array(
		'app_num' => $next_app_num,
		'bitrix24_client_id' => getUserInfo($_SESSION["auth_id"], 'bitrix24_id'),
		'bitrix24_client_name' => getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name'),
		'status' => 'app-production',
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

	echo '<center><h4>
	Заказ <b>№'.$next_app_num_clear.'</b> успешно запущен в производство!
	<br><br>
	</h4>
	<a role="button" href="http://crm.ei-60.online/order_info.php?num='.$next_app_num.'" class="btn btn-info">Открыть страницу заказа</a> <a role="button" href="/order_list.php" class="btn btn-primary">Вернуться в журнал</a>
	</center>
	<br><br>';
	
	//отправляем оповещение в чат Битрикс 24 (id чата 98) 
	bx24_im_mes_add_file_98(
			"[B]Заказ №".$next_app_num_clear." ДОБАВЛЕН В ОЧЕРЕДЬ производства![/B]",
			"#000000",
			$app_file_excel,
			$CONFIG['hostname'].'uploads/apps_files/'.date('Y').'/'.$next_app_num_clear.'/'.$app_file_excel,
			">> открыть заказ в журнале заявок <<",
			$next_app_num,
			$name_manager,
			d_format(working_days($_POST["date_ready"]), 'f6'),
			$_POST["manager_comment"]
	);
	
	// отправка файла заявки на почту производства ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	setlocale(LC_ALL, 'ru_RU.UTF8');
	require_once('classes/SMTPMail/smtp_class.php'); //подключение класса
	$smtp = new SMTP('ssl://smtp.yandex.ru', 465, 'dveri@1dvm.ru', '123456', '●Битрикс24● Журнал', 'imap.yandex.ru', 993); // задаем конфиг для подключения к почте, последние два параметра для сохранения в исходящих
	if ($_POST["manager_comment"] == '') {
		$comments_app_email = 'без комментария...';
	} else {
		$comments_app_email = $_POST["manager_comment"];
	}
	$mail_text = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta name="x-apple-disable-message-reformatting"> 
    <title></title> 
    <style>

        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        div[style*="margin: 16px 0"] {
            margin:0 !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }

        img {
            -ms-interpolation-mode:bicubic;
        }

        *[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .x-gmail-data-detectors,
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
        }

        .a6S {
	        display: none !important;
	        opacity: 0.01 !important;
        }
        img.g-img + div {
	        display:none !important;
	   	}

        .button-link {
            text-decoration: none !important;
        }

        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */
            .email-container {
                min-width: 375px !important;
            }
        }

    </style>
    <style>

        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td:hover,
        .button-a:hover {
            background: #555555 !important;
            border-color: #555555 !important;
        }

        @media screen and (max-width: 480px) {

            .fluid {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }

            .stack-column-center {
                text-align: center !important;
            }

            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }
        }

    </style>

</head>
<body width="100%" bgcolor="#494949" style="margin: 0; mso-line-height-rule: exactly;">
    <center style="width: 100%; background: #494949; text-align: left;">

        <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;">
            Менеджер: '.$name_manager.' (рабочий телефон: '.getUserInfo($_SESSION["auth_id"], 'work_phone').') >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        </div>

        <div style="max-width: 680px; margin: auto;" class="email-container">

		
            <!-- Email Header : BEGIN -->
            <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;">
                <tr>
                    <td style="padding: 10px 0; text-align: center">
                    </td>
                </tr>
            </table>
            <!-- Email Header : END -->
		
            <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;">

                <tr>
                    <td bgcolor="#ffffff">
                        <img src="http://crm.ei-60.online/images/email_banner.png" aria-hidden="true" width="680" height="" alt="alt_text" border="0" align="center" class="fluid" style="width: 100%; max-width: 680px; height: auto; background: #ffffff; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;" class="g-img">
                    </td>
                </tr>

                <tr>
                    <td bgcolor="#ffffff">
                        <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="padding: 40px; text-align: left; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                    Номер заявки: <b>'.$next_app_num_clear.'</b><br>
									Дата добавления в очередь: <b>'.date("d.m.Y H:i").'</b><br>
									Дата готовности: <b>'.d_format(working_days($_POST["date_ready"]), 'f6').'</b><br>
									Менеджер: <b>'.$name_manager.'</b><br>
									Связь с менеджером: <b>'.getUserInfo($_SESSION["auth_id"], 'work_phone').'</b><br>
									Комментарий: <b>'.$comments_app_email.'</b>
                                    <br>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td height="20" style="font-size: 0; line-height: 0;">
                        &nbsp;
                    </td>
                </tr>

            </table>
 
        </div>
    </center>
</body>
</html>
	';
	
	$sended=$smtp->send_mail($_SESSION["auth_email"], 'Заказ №'.$next_app_num_clear.' ДОБАВЛЕН В ОЧЕРЕДЬ производства', $mail_text, array($uploaddir.$app_file_excel, 'smtp_class.php'));
	$sended=$smtp->send_mail('head.factory@1dvm.ru', 'Заказ №'.$next_app_num_clear.' ДОБАВЛЕН В ОЧЕРЕДЬ производства', $mail_text, array($uploaddir.$app_file_excel, 'smtp_class.php'));
	$sended=$smtp->send_mail('ec.factory@1dvm.ru', 'Заказ №'.$next_app_num_clear.' ДОБАВЛЕН В ОЧЕРЕДЬ производства', $mail_text, array($uploaddir.$app_file_excel, 'smtp_class.php'));

//стираем все данные массива $_POST
unset($_POST);
	
}

}








//ОБНОВЛЕНИЕ бланка заказа ----------------------------------------------------------------------------------------------------------
if (isset($_POST['app_update_btn'])) {

//проверим прикрепление файла обновленной заявки, а так же заполнение комментария
if (isset($_FILES['app_file_upd']['name']) && isset($_POST["manager_upd_comment"])) {
	
//запросы\загрузка
//$_POST["manager_upd_comment"];

//номер заказа
$app_num_update = $_POST["app_num_update"];
//имя актуального бланк заказа (имя.расширение)
$app_old_blank = $_POST["app_old_blank"];
//имя актуального уже не актуального бланк заказа (old_имя.расширение)
$app_old_blank_name = 'old_'.$app_old_blank;
//дата готовности
$app_date_ready_update = $_POST["app_date_ready_update"];
//имя менеджера (ф и о)
$name_manager = getOrderInfo($app_num_update, 'bitrix24_client_name');
//имя обновившего менеджера (ф и о)
$name_manager_update = getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name');
//имя менеджера (ф и)
$name_manager_fi = getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name');
//id (в системе битрикс24) авторизованного сотрудника
$bitrix24_client_id = getUserInfo($_SESSION["auth_id"], 'bitrix24_id');
//имя фамалия сотрудника в битриксе
$bitrix24_client_name = $name_manager_fi;
//имя менеджера (ф и.)
$name_manager_short = getUserInfo($_SESSION["auth_id"], 'last_name').' '.substr(getUserInfo($_SESSION["auth_id"], 'name'), 0, 2).'.';
//0 - дата обновления (для таблицы qsl) //1 - дата обнолвения для файла //2 - дата обновления для заполнения бланка excel
$upd_date_0 = date("Y-m-d H:i:s");	//0
$upd_date_1 = date("Y_m_d_H_i_s");	//1
$upd_date_2 = date("d.m.Y H:i");	//2


//ЗАГРУЖАЕМ НОВЫЙ БЛАНК -> РАЗБИРАЕМ ЕГО В ЭКСЕЛЕ + ДОБАВЛЯЕМ ИНФУ ПО ОБНОВЛЕНИЮ -> ПЕРЕИМЕНУЕМ СТАРЫЙ БЛАНК -> ДОБАВЛЯЕМ ОБНОВЛЕННУЮ ИНФОРМАЦИЮ В БАЗУ (по заказу и в лог обновлений) -> 
//определяем файл заявки и переименуем его
function trim_to_dot($string) {
$pos = strrpos($string, '.'); // поиск позиции точки с конца строки
if (!$pos) { return $string; }// если точка не найдена - возвращаем строку
return substr($string, $pos + 1); } //substr($string, 0, $pos + 1);

$app_file_upd = basename($_FILES['app_file_upd']['name']);
$app_file_upd_excel = 'upd_'.appnumclear($app_num_update).'_blank_'.$upd_date_1.'.'.trim_to_dot($app_file_upd);


// РАБОТА С ОБНОВЛЕННЫМ БЛАНКОМ...
// указываем путь загрузки файлов
$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/uploads/apps_files/'.date('Y').'/'.appnumclear($app_num_update).'/';

// переменные полного пути файлов
$app_excel_upd = $uploaddir.'upload_'.$app_file_upd_excel;

// переменные tmp файлов
$app_excel_upd_source = $_FILES['app_file_upd']['tmp_name'];

// Копируем файл из каталога для временного хранения файлов в основной каталог:
move_uploaded_file($app_excel_upd_source, $app_excel_upd);

// РАБОТА СО СТАРЫМ БЛАНКОМ
// Переименовываем старый бланк добавляя приставку "old_"
rename($uploaddir.$app_old_blank, $uploaddir.$app_old_blank_name);

		

		
		
		
		
// работа над загруженным excel обнрвлённым файлом-заявки ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// добавляем информацию в блок ОБНОВЛЕНИЯ бланка
// --- подключаем библиотеку --- 
include_once 'classes/PHPExcel.php';
include 'classes/PHPExcel/IOFactory.php';
$objPHPExcel = new PHPExcel();
// --- открываем файл-шаблон --- 
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load($app_excel_upd);
$objPHPExcel->setActiveSheetIndex(0);
// --- заполняем таблицу --- 
$objPHPExcel->getActiveSheet()->SetCellValue('K1', $upd_date_2); 					//Дата обновления
$objPHPExcel->getActiveSheet()->SetCellValue('K2', $name_manager_short);			//Обновил(а)
$objPHPExcel->getActiveSheet()->SetCellValue('K3', $_POST["manager_upd_comment"]);	//Комментарий
// --- записываем результат и сохраняем (бланк со всеми данными + системный лист) --- 	 
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($uploaddir.$app_file_upd_excel);
			
			
// добавили информацию
// считываем данные по дверям
$objPHPExcel2 = new PHPExcel();
// Открываем файл
$objPHPExcel2 = PHPExcel_IOFactory::load($uploaddir.$app_file_upd_excel);
$objPHPExcel2->setActiveSheetIndex(0);
$objPHPExcel2 = $objPHPExcel2->getActiveSheet();		

// --- считываем номер заказа из загруженного бланка --- 
$num_to_upload_blank = $objPHPExcel2->getCell('E1')->getValue();
// ----------------------------------------------------- 

$c_single = 0;
$c_double = 0;
$c_gates = 0;
$c_hatches = 0;
$c_transoms = 0;
$c_others = 0;

for ($i = 8; $i <= $objPHPExcel2->getHighestRow(); $i++) {
	$cellValue = $objPHPExcel2->getCell('B'.$i)->getValue();
	//проверка цикла
	//echo $i.' - '.$cellValue.'<br><br><br>';
	if ($cellValue == '' || $cellValue == null) {
		break;
	} else {
		if ($cellValue == 'Дверь EI-60') {
			$stvorka_count = $objPHPExcel2->getCell('E'.$i)->getValue();
			if ($stvorka_count == '') {
				$c_single = $c_single + $objPHPExcel2->getCell('N'.$i)->getValue();
			} else {
				$c_double = $c_double + $objPHPExcel2->getCell('N'.$i)->getValue();
			}
			
		}
		if ($cellValue == 'Дверь EIS-60') {
			$stvorka_count = $objPHPExcel2->getCell('E'.$i)->getValue();
			if ($stvorka_count == '') {
				$c_single = $c_single + $objPHPExcel2->getCell('N'.$i)->getValue();
			} else {
				$c_double = $c_double + $objPHPExcel2->getCell('N'.$i)->getValue();
			}
			
		}
		if ($cellValue == 'Дверь Техническая') {
			$stvorka_count = $objPHPExcel2->getCell('E'.$i)->getValue();
			if ($stvorka_count == '') {
				$c_single = $c_single + $objPHPExcel2->getCell('N'.$i)->getValue();
			} else {
				$c_double = $c_double + $objPHPExcel2->getCell('N'.$i)->getValue();
			}
		}
		if ($cellValue == 'Дверь Тех в исп EI-60') {
			$stvorka_count = $objPHPExcel2->getCell('E'.$i)->getValue();
			if ($stvorka_count == '') {
				$c_single = $c_single + $objPHPExcel2->getCell('N'.$i)->getValue();
			} else {
				$c_double = $c_double + $objPHPExcel2->getCell('N'.$i)->getValue();
			}
		}
		if ($cellValue == 'Ворота EI-60') {
				$c_gates = $c_gates + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Ворота EIS-60') {
				$c_gates = $c_gates + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Ворота Технические') {
				$c_gates = $c_gates + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Люк EI-60') {
				$c_hatches = $c_hatches + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Люк EIS-60') {
				$c_hatches = $c_hatches + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Люк Технический') {
				$c_hatches = $c_hatches + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Фрамуга EI-60') {
				$c_transoms = $c_transoms + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if ($cellValue == 'Фрамуга Техническая') {
				$c_transoms = $c_transoms + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
		if(!in_array($cellValue, $APP_NAMES_DOORS, true)) {
			$c_others = $c_others + $objPHPExcel2->getCell('N'.$i)->getValue();
		}
	}
}

//считаем общую сумму позиций в заказе
$c_summ = $c_single + $c_double + $c_gates + $c_hatches + $c_transoms + $c_others;



//НАЧИНАЕМ ПРОВЕРКУ ВСЕХ ДАННЫХ И ИХ ОБНОВЛЕНИЕ
if ($num_to_upload_blank == appnumclear($app_num_update)) {
//ЕСЛИ НОМЕР БЛАНКА СООТВЕТСТВУЕТ ЗАКАЗУ

//Сохраняем текущие данные по кол-ву дверей в заказе
$c_summ_old = getOrderInfo($app_num_update, 'c_summ');
$c_single_old = getOrderInfo($app_num_update, 'c_single');
$c_double_old = getOrderInfo($app_num_update, 'c_double');
$c_gates_old = getOrderInfo($app_num_update, 'c_gates');
$c_hatches_old = getOrderInfo($app_num_update, 'c_hatches');
$c_transoms_old = getOrderInfo($app_num_update, 'c_transoms');
$c_others_old = getOrderInfo($app_num_update, 'c_others');

//Обновляем информацию по кол-ву и позициям дверей
$stmt = $dbConnection->prepare('UPDATE crm_applist SET								
						c_single=:c_single,
						c_double=:c_double,
						c_gates=:c_gates,
						c_hatches=:c_hatches,
						c_transoms=:c_transoms,
						c_others=:c_others,
						app_file_excel=:app_file_excel,
						app_file_excel_date=:app_file_excel_date,
						c_summ=:c_summ
	WHERE num=:num'
);
$stmt -> execute(array(':c_single' => $c_single,
						':c_double' => $c_double,
						':c_gates' => $c_gates,
						':c_hatches' => $c_hatches,
						':c_transoms' => $c_transoms,
						':c_others' => $c_others,
						':c_summ' => $c_summ,
						':app_file_excel' => $app_file_upd_excel,
						':app_file_excel_date' => $upd_date_0,
						':num' => $app_num_update
));

//Добавляем информацию по обновлению бланка в соответствующую таблицу
$blank_upd = array(
		'app_num' => $app_num_update,
		'bitrix24_client_id' => $bitrix24_client_id,
		'bitrix24_client_name' => $bitrix24_client_name,
		'old_blank_link' => $app_old_blank_name,
		'old_date_ready' => $app_date_ready_update,
		'upd_date' => $upd_date_0,
		'comment_upd' => $_POST["manager_upd_comment"],
		'c_single' => $c_single_old,
		'c_double' => $c_double_old,
		'c_gates' => $c_gates_old,
		'c_hatches' => $c_hatches_old,
		'c_transoms' => $c_transoms_old,
		'c_others' => $c_others_old,
		'c_summ' => $c_summ_old
);  
$stmt_blank_upd = $dbConnection->prepare("INSERT INTO crm_apps_blank_upd (
		app_num,
		bitrix24_client_id,
		bitrix24_client_name,
		old_blank_link,
		old_date_ready,
		upd_date,
		comment_upd,
		c_single,
		c_double,
		c_gates,
		c_hatches,
		c_transoms,
		c_others,
		c_summ
) values (
		:app_num,
		:bitrix24_client_id,
		:bitrix24_client_name,
		:old_blank_link,
		:old_date_ready,
		:upd_date,
		:comment_upd,
		:c_single,
		:c_double,
		:c_gates,
		:c_hatches,
		:c_transoms,
		:c_others,
		:c_summ
)");  
$stmt_blank_upd->execute($blank_upd);

//отправляем оповещение в чат Битрикс 24 (id чата 98) 
bx24_im_mes_add_file_upd_98(
		"[B]ВНИМАНИЕ!!! Заказ №".appnumclear($app_num_update)." ОБНОВЛЁН БЛАНК!!![/B]",
		"#ff0000",
		$app_file_upd_excel,
		$CONFIG['hostname'].'uploads/apps_files/'.date('Y').'/'.appnumclear($app_num_update).'/'.$app_file_upd_excel, //редактить
		">> открыть заказ в журнале заявок <<",
		$app_num_update,
		$name_manager,
		$name_manager_update,
		d_format($app_date_ready_update, 'f1'),
		$_POST["manager_upd_comment"]
);

// отправка файла заявки на почту производства ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
setlocale(LC_ALL, 'ru_RU.UTF8');
require_once('classes/SMTPMail/smtp_class.php'); //подключение класса
$smtp = new SMTP('ssl://smtp.yandex.ru', 465, 'dveri@1dvm.ru', '123456', '●Битрикс24● Журнал', 'imap.yandex.ru', 993); // задаем конфиг для подключения к почте, последние два параметра для сохранения в исходящих
if ($_POST["manager_upd_comment"] == '') {
	$comments_app_update = 'без комментария...';
} else {
	$comments_app_update = $_POST["manager_upd_comment"];
}
$mail_text = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta name="x-apple-disable-message-reformatting"> 
    <title></title> 
    <style>

        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        div[style*="margin: 16px 0"] {
            margin:0 !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }

        img {
            -ms-interpolation-mode:bicubic;
        }

        *[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        .x-gmail-data-detectors,
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
        }

        .a6S {
	        display: none !important;
	        opacity: 0.01 !important;
        }
        img.g-img + div {
	        display:none !important;
	   	}

        .button-link {
            text-decoration: none !important;
        }

        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */
            .email-container {
                min-width: 375px !important;
            }
        }

    </style>
    <style>

        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td:hover,
        .button-a:hover {
            background: #555555 !important;
            border-color: #555555 !important;
        }

        @media screen and (max-width: 480px) {

            .fluid {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }

            .stack-column-center {
                text-align: center !important;
            }

            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }
        }

    </style>

</head>
<body width="100%" bgcolor="#494949" style="margin: 0; mso-line-height-rule: exactly;">
    <center style="width: 100%; background: #494949; text-align: left;">

        <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;">
            Менеджер: '.$name_manager_update.' (рабочий телефон: '.getUserInfo($_SESSION["auth_id"], 'work_phone').') >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        </div>

        <div style="max-width: 680px; margin: auto;" class="email-container">

		
            <!-- Email Header : BEGIN -->
            <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;">
                <tr>
                    <td style="padding: 10px 0; text-align: center">
                    </td>
                </tr>
            </table>
            <!-- Email Header : END -->
		
            <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;">

                <tr>
                    <td bgcolor="#ffffff">
                        <img src="http://crm.ei-60.online/images/email_banner.png" aria-hidden="true" width="680" height="" alt="alt_text" border="0" align="center" class="fluid" style="width: 100%; max-width: 680px; height: auto; background: #ffffff; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;" class="g-img">
                    </td>
                </tr>

                <tr>
                    <td bgcolor="#ffffff">
                        <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="padding: 40px; text-align: left; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                    Номер заявки: <b>'.appnumclear($app_num_update).'</b><br>
									<u>Обновленная</u> дата готовности: <b>'.d_format($app_date_ready_update, 'f1').'</b><br>
									Менеджер: <b>'.$name_manager.'</b><br>
									Кто обновил: <b>'.$name_manager_update.'</b><br>
									Номер для связи: <b>'.getUserInfo($_SESSION["auth_id"], 'work_phone').'</b><br>
									Комментарий: <b>'.$comments_app_update.'</b>
                                    <br>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td height="20" style="font-size: 0; line-height: 0;">
                        &nbsp;
                    </td>
                </tr>

            </table>
 
        </div>
    </center>
</body>
</html>
	';

//отправляем оповещение и обновленный бланк на почту
$sended=$smtp->send_mail($_SESSION["auth_email"], 'Заказ №'.appnumclear($app_num_update).' ОБНОВЛЁН БЛАНК ЗАКАЗА!!!', $mail_text, array($uploaddir.$app_file_upd_excel, 'smtp_class.php'));
$sended=$smtp->send_mail('head.factory@1dvm.ru', 'Заказ №'.appnumclear($app_num_update).' ОБНОВЛЁН БЛАНК ЗАКАЗА!!!', $mail_text, array($uploaddir.$app_file_upd_excel, 'smtp_class.php'));
$sended=$smtp->send_mail('ec.factory@1dvm.ru', 'Заказ №'.appnumclear($app_num_update).' ОБНОВЛЁН БЛАНК ЗАКАЗА!!!', $mail_text, array($uploaddir.$app_file_upd_excel, 'smtp_class.php'));

//стираем все данные массива $_POST
unset($_POST);
//выводим системное ссобщение
echo '<center><h4>
Бланк заказа <b>№'.appnumclear($app_num_update).'</b> успешно обновлён!
<br><br>
</h4>
<a role="button" href="http://crm.ei-60.online/order_info.php?num='.$app_num_update.'" class="btn btn-info">Вернуться на страницу заказа</a>
</center>
<br><br>';

} else {
//ЕСЛИ НОМЕР БЛАНКА НЕ СООТВЕТСТВУЕТ ЗАКАЗУ
//стираем все данные массива $_POST
unset($_POST);
//выводим системное ссобщение
echo '<center><h4>
Бланк заказа <b>№'.appnumclear($app_num_update).'</b> не может быть обновлен!
<br>
<h5>Возможно вы загрузили некорректный бланк! Скачайте актуальный бланк для этого заказа, измените его и повторите попытку!<h5>
<br><br>
</h4>
<a role="button" href="http://crm.ei-60.online/order_info.php?num='.$app_num_update.'" class="btn btn-info">Вернуться на страницу заказа</a>
</center>
<br><br>';
}



} else {
//ЕСЛИ НЕ БЫЛ ПРИКРЕПЛЁН БЛАНК ЗАКАЗА ИЛИ НЕ НАПИСАН КОММЕНТАРИЙ
//стираем все данные массива $_POST
unset($_POST);
//выводим системное ссобщение
echo '<center><h4>
Упс... Ошибочка вышла...
<br>
<h5>Вы не прикрепили обновлённый бланк заказа или не написали комментарий!<h5>
<br><br>
</h4>
<a role="button" href="http://crm.ei-60.online/order_info.php?num='.$app_num_update.'" class="btn btn-info">Вернуться на страницу заказа №'.appnumclear($app_num_update).'</a>
</center>
<br><br>';
}


}


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