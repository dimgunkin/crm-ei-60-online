<?php
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = "Заказы - ".$CONFIG['title_header'];

//подключаем верстку
include("head.inc.php"); //шапка сайта
include("navbarleft.inc.php"); //боковое меню навигации
include("navbartop.inc.php"); //верхнее меню навигации

// -------------------------------------- ПОСТРАНИЧНАЯ НАВИГАЦИЯ --------------------------------------------

// --- Показываем заявки по иерархии / '.$app_view.' / --- 		СТАРАЯ ВЕРСИЯ
// руководители отделов продаж
// Гехтман Алексей, Прошина Виктория, Анциферова Наталья
/* $head_dep_ids = '8, 20, 24';

switch(getUserInfo($_SESSION["auth_id"], 'work_position_access')) {
	
		case 'fullacc': 
			$app_view = '';
		break;
		case 'director_dev': 
			$app_view = '';
		break;
		case 'director': 
			$app_view = '';
		break;
		case 'accountant': 
			$app_view = '';
		break;
		case 'factory_head': 
			$app_view = '';
		break;
		case 'factory_ec': 
			$app_view = '';
		break;
		
		
		case 'salesteam_manager': 
			$app_view = 'WHERE employees_id = '.$_SESSION["auth_id"];
		break;
		
		case 'salesteam_manager_the_call': 
			$app_view = '';
		break;
		
		case 'salesteam_manager_extended': 
			$app_view = '';
		break;
		
		case 'salesteam_head': // руководитель отдела продаж
			$bx_24_dep_id = getUserInfo($_SESSION["auth_id"], 'bitrix24_department_id');
			$stmt_employees_dep = $dbConnection->query('SELECT * from crm_employees WHERE bitrix24_department_id = "'.$bx_24_dep_id.'"');
			$employees_dep = $stmt_employees_dep->fetchAll();
			$total1 = count($employees_dep);
			$counter1 = 0;
			$dep_id_massive = '';
			if ($total1 == 0) {
				$dep_id_massive = '';
			} else {
			foreach($employees_dep as $employees_dep_row) {
			$counter1++;
			  if($counter1 == $total1) {
				// делаем что-либо с последним элементом
				$dep_id_massive = $dep_id_massive.$employees_dep_row['id'];
			  }
			  else{
				// делаем что-либо с каждым элементом
				$dep_id_massive = $dep_id_massive.$employees_dep_row['id'].',';
				}
			}
			$dep_id_massive_start = 'array (';
			$dep_id_massive_end = ')';
			$app_view = $dep_id_massive_start.$dep_id_massive.$dep_id_massive_end;
			}
		break;
		
		default:
			$app_view = '';
		break;
		
}
 */
// --- Показываем заявки по иерархии /конец/ ---		СТАРАЯ ВЕРСИЯ

// Устанавливаем количество записей, которые будут выводиться на одной странице
// Поставьте нужное вам число. Для примера я указал одну запись на страницу
$quantity=150;

// Ограничиваем количество ссылок, которые будут выводиться перед и
// после текущей страницы
$limit=50;

// Объявляем переменную page, если она пустая, присваиваем 1
if(isset($_GET['page'])) {
	$page = $_GET['page'];
} else {
	$page = 1;
}
	
// Если значение page= не является числом, то показываем
// пользователю первую страницу
if(!is_numeric($page)) $page=1;

// Если пользователь вручную поменяет в адресной строке значение page= на нуль,
// то мы определим это и поменяем на единицу, то-есть отправим на первую
// страницу, чтобы избежать ошибки
if ($page<1) $page=1;

// Узнаем количество всех доступных записей 
$stmt2_app_count_all = $dbConnection->query('SELECT * from crm_applist ORDER BY num DESC');
$app_all = $stmt2_app_count_all->fetchAll();
$app_all_count = $stmt2_app_count_all->rowCount();

// Вычисляем количество страниц, чтобы знать сколько ссылок выводить
$pages = $app_all_count/$quantity;

// Округляем полученное число страниц в большую сторону
$pages = ceil($pages);

// Здесь мы увеличиваем число страниц на единицу чтобы начальное значение было
// равно единице, а не нулю. Значение page= будет
// совпадать с цифрой в ссылке, которую будут видеть посетители
$pages++; 

// Если значение page= больше числа страниц, то выводим первую страницу
if ($page>$pages) $page = 1;

// Переменная $list указывает с какой записи начинать выводить данные.
// Если это число не определено, то будем выводить
// с самого начала, то-есть с нулевой записи
if (!isset($list)) $list=0;

// Чтобы у нас значение page= в адресе ссылки совпадало с номером
// страницы мы будем его увеличивать на единицу при выводе ссылок, а
// здесь наоборот уменьшаем чтобы ничего не нарушить.
$list=--$page*$quantity;

// Делаем запрос подставляя значения переменных $quantity и $list
$stmt = $dbConnection->query('SELECT * from crm_applist ORDER BY num DESC LIMIT '.$quantity.' OFFSET '.$list.'');
// Считаем количество полученных записей
$applist = $stmt->fetchAll();
$applist_count = $stmt->rowCount();



// -------------------------------------- ПОСТРАНИЧНАЯ НАВИГАЦИЯ /конец/ -------------------------------------
		
?>	
<!-- начало тела станицы -->

<?

		


?>	
         <div class="page-heading">

<!--             <section class="panel" style="width: 50%;">
                        <div class="carousel slide auto panel-body" id="c-slide">
                            <ol class="carousel-indicators out">
                                <li data-target="#c-slide" data-slide-to="0" class="active"></li>
                                <li data-target="#c-slide" data-slide-to="1" class=""></li>
                                <li data-target="#c-slide" data-slide-to="2" class=""></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="item text-center active">
                                    <h3>AdminEx is an Awesome Dashboard</h3>
                                    <p>Awesome admin template</p>
                                    <small class="text-muted">Based on Latest Bootstrap 3.1.0</small>
                                </div>
                                <div class="item text-center">
                                    <h3>Well Organized</h3>
                                    <p>Awesome admin template</p>
                                    <small class="text-muted">Huge UI Elements</small>
                                </div>
                                <div class="item text-center">
                                    <h3>Well Documentation</h3>
                                    <p>Awesome admin template</p>
                                    <small class="text-muted">Very Easy to Use</small>
                                </div>
                            </div>
                            <a class="left carousel-control" href="#c-slide" data-slide="prev">
                                <i class="fa fa-angle-left"></i>
                            </a>
                            <a class="right carousel-control" href="#c-slide" data-slide="next">
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </div>
                    </section>
-->

        </div>
        <!-- page heading end-->

        <!--body wrapper start-->
	 <!-- <center> <b style="font-size: 24px;">Ориентировочная информация о готовности заказов на ближайшие 5 дней</b> </center> -->
	 

	 
	<div class="wrapper">

		<div class="row">
			<div class="col-sm-12">
				<section class="panel">
					<header class="panel-heading">
						Ориентировочная информация о готовности заказов на ближайшие 5 дней
						<span class="tools pull-right">
							<a class="fa fa-chevron-down" href="javascript:;"></a>
						</span>
					</header>
					<div class="panel-body">

<!-- БЛОК block_expired_app -->	
<?
	// Запрос
	$stmt_day_sort_bad = $dbConnection->query('SELECT * from crm_applist WHERE date_ready < "'.date("Y-m-d").' 00:00:00" AND status = "app-gotobuilding" OR "app-production" ORDER BY date_ready ASC'); 
	$applist_day_sort_bad = $stmt_day_sort_bad->fetchAll();
	if (check_access('block_expired_app', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{
?>
		<div class="row states-info">
            <div class="col-md-12">
                <div class="panel gray-bg">
                    <div class="panel-body">
                        <div class="row">
                            <div style="text-align: center;" class="col-xs-1">
                                 <i class="fa fa-ban" aria-hidden="true"></i>
                            </div>
                            <div class="col-xs-11">
                                <span class="state-title"> <b style="font-size: 15px;">Просроченные заказы</b> </span>
                                <h4>
								<?
								$totalbad = count($applist_day_sort_bad); 
								$counterbad = 0;
								if ($totalbad == 0) {
									echo 'нет заявок...';
								} else {
								foreach($applist_day_sort_bad as $applist_day_sort_bad_row) {
								$counterbad++;
								  if($counterbad == $totalbad) {
									// делаем что-либо с последним элементом
									echo '<a class="name" style="color: #FFFFFF;" href="order_info.php?num='.$applist_day_sort_bad_row['num'].'">'.appnumclear($applist_day_sort_bad_row['num']).' <i style="font-size: 12px;">('.d_format($applist_day_sort_bad_row['date_ready'], 'f1').')</i></a>';
								  }
								  else{
									// делаем что-либо с каждым элементом
									echo '<a class="name" style="color: #FFFFFF;" href="order_info.php?num='.$applist_day_sort_bad_row['num'].'">'.appnumclear($applist_day_sort_bad_row['num']).' <i style="font-size: 12px;">('.d_format($applist_day_sort_bad_row['date_ready'], 'f1').')</i></a>, ';
								  }
								}
								}
								?>
								</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
<?	
	} 
?>
<!-- БЛОК   конец -->



<?
// Запрос на заявки по дням
$stmt_day_sort_1 = $dbConnection->query('SELECT * from crm_applist WHERE date_ready = "'.date("Y-m-d").' 00:00:00" AND status != "app-stopped" OR "app-shipped" ORDER BY date_ready ASC');
$applist_day_sort_1 = $stmt_day_sort_1->fetchAll();

$stmt_day_sort_2 = $dbConnection->query('SELECT * from crm_applist WHERE date_ready = "'.date("Y-m-d", strtotime("+1 day")).' 00:00:00" AND status != "app-stopped" OR "app-shipped" ORDER BY date_ready ASC');
$applist_day_sort_2 = $stmt_day_sort_2->fetchAll();

$stmt_day_sort_3 = $dbConnection->query('SELECT * from crm_applist WHERE date_ready = "'.date("Y-m-d", strtotime("+2 day")).' 00:00:00" OR date_ready = "'.date("Y-m-d", strtotime("+3 day")).' 00:00:00" OR date_ready = "'.date("Y-m-d", strtotime("+4 day")).' 00:00:00" AND status != "app-stopped" OR "app-shipped" ORDER BY date_ready ASC');
$applist_day_sort_3 = $stmt_day_sort_3->fetchAll();
?>	
		<div class="row states-info">
            <div class="col-md-4">
                <div class="panel red-bg">
                    <div class="panel-body">
                        <div class="row">
                            <div style="text-align: center;" class="col-xs-3">
                                 <i class="fa fa-warning" aria-hidden="true"></i>
                            </div>
                            <div class="col-xs-9">
                                <span class="state-title"> <b style="font-size: 15px;">Сегодня</b> </span>
                                <h4>
								<?
								$total1 = count($applist_day_sort_1);
								$counter1 = 0;
								if ($total1 == 0) {
									echo 'нет заявок...';
								} else {
								foreach($applist_day_sort_1 as $applist_day_sort_1_row) {
								$counter1++;
								  if($counter1 == $total1) {
									// делаем что-либо с последним элементом
									echo '<a class="name" style="color: #FFFFFF;" href="order_info.php?num='.$applist_day_sort_1_row['num'].'">'.appnumclear($applist_day_sort_1_row['num']).'</a>';
								  }
								  else{
									// делаем что-либо с каждым элементом
									echo '<a class="name" style="color: #FFFFFF;" href="order_info.php?num='.$applist_day_sort_1_row['num'].'">'.appnumclear($applist_day_sort_1_row['num']).'</a>, ';
								  }
								}
								}
								?>
								</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel blue-bg">
                    <div class="panel-body">
                        <div class="row">
                            <div style="text-align: center;" class="col-xs-3">
                                <i class="fa fa-star-half-o" aria-hidden="true"></i>
                            </div>
                            <div class="col-xs-9">
                                <span class="state-title">  <b style="font-size: 15px;">Завтра</b>  </span>
                                <h4>
								<?
								$total2 = count($applist_day_sort_2);
								$counter2 = 0;
								if ($total2 == 0) {
									echo 'нет заявок...';
								} else {
								foreach($applist_day_sort_2 as $applist_day_sort_2_row) {
								$counter2++;
									  if($counter2 == $total2) {
										// делаем что-либо с последним элементом
										echo '<a class="name" style="color: #FFFFFF;" href="order_info.php?num='.$applist_day_sort_2_row['num'].'">'.appnumclear($applist_day_sort_2_row['num']).'</a>';
									  } else {
										// делаем что-либо с каждым элементом
										echo '<a class="name" style="color: #FFFFFF;" href="order_info.php?num='.$applist_day_sort_2_row['num'].'">'.appnumclear($applist_day_sort_2_row['num']).'</a>, ';
									  }
									}
								}
								?>
								</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel green-yes-bg">
                    <div class="panel-body">
                        <div class="row">
                            <div style="text-align: center;" class="col-xs-3">
                                <i class="fa fa-star" aria-hidden="true"></i>
                            </div>
                            <div class="col-xs-9">
                                <span class="state-title">  <b>На <?=d_format(date("Y-m-d H:i:s", strtotime("+2 day")), 'f8');?>, <?=d_format(date("Y-m-d H:i:s", strtotime("+3 day")), 'f8');?> и <?=d_format(date("Y-m-d H:i:s", strtotime("+4 day")), 'f8');?></b>  </span>
                                <h4 style="color: #FFFFFF;">
								<?
								$total3 = count($applist_day_sort_3);
								$counter3 = 0;
								if ($total3 == 0) {
									echo 'нет заявок...';
								} else {
								foreach($applist_day_sort_3 as $applist_day_sort_3_row) {
								$counter3++;
								  if($counter3 == $total3){
									// делаем что-либо с последним элементом
									echo '<a class="name" style="color: #FFFFFF;" href="order_info.php?num='.$applist_day_sort_3_row['num'].'">'.appnumclear($applist_day_sort_3_row['num']).'</a>';
								  }
								  else{
									// делаем что-либо с каждым элементом
									echo '<a class="name" style="color: #FFFFFF;" href="order_info.php?num='.$applist_day_sort_3_row['num'].'">'.appnumclear($applist_day_sort_3_row['num']).'</a>, ';
								  }
								}
								}
								?>
								</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
			
			
					
					</div>
				</section>
			</div>		
		</div>
		
<?
// -- проверяем, включен ли вход по запасному шлюзу авторизации
if ($CONFIG['yandex_oauth'] == 'disabled') {
	if (check_access('btn_app_start', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{
?>

<?
// -- ПРОЧСЕТ ЗАГРУЖЕННОСТИ --
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

$aa1 = working_days_factory($c); // дата завершения производства всех текущих заказов
$bb1 = ceil(1 / ($output_per_day + $output_per_day_smallapp)); // предполагаемый заказ 82 двери делим на производительность 50 дверей, получаем кол-во рабочих дней на обработку (округляем в большую сторону)
$cc1 = working_days_factory_finish($aa1, $bb1);

$nn1 = count_work_day_factory_date_to_date(date("Y-m-d"), d_format($app_end_date_ready[0]["date_ready"], "f9")); // Кол-во рабочих дней производства от текущей даты до даты готовности крайней заявки
$nn2 = count_work_day_factory_date_to_date(date("Y-m-d"), d_format($aa1, "f9")); // Кол-во рабочих дней производства от текущей даты до фактической даты готовности всего объема с учетом производительности

$factory_load = ceil(factory_workload($app_c_summ_all, $nn1, $nn2));

if ($factory_load > 100) {
	$factory_load_progress_bar = 100;
} else {
	$factory_load_progress_bar = $factory_load;
}

$factory_load_pb_color = factory_workload_pb_color($factory_load);

?>	

		<div class="col-md-12">
			<div class="row">
				<div class="panel">
					<div class="panel-body"> 
						<div class="col-sm-5">
						
<!-- КНОПКА+ФОРМА btn_app_start -->		
						<a class="btn btn-success btn-lg btn-block" data-toggle="modal" href="#orderSTART">
                            <i class="fa fa-plus"></i>&nbsp;&nbsp;Запустить новый заказ
                        </a>
						
						<!-- форма запуска нового заказа -->
						<div class="modal fade" id="orderSTART" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
									<form action="action.inc.php" method="post" enctype="multipart/form-data" class="form-horizontal">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Данные нового заказа</h4>
                                        </div>
                                        <div class="modal-body">
                                            
                                                <div class="form-group">
                                                    <label class="control-label col-md-4"> Контрагент</label>
                                                    <div class="col-md-7">
														<select class="form-control m-bot15" name="bitrix24_kontragent">
														<?
														//получаем список организаций с учетом структуры компании в Битрикс24 (за 1 раз call может выдать массив с 50 компаниями) 
														$start_bx24_id = 0;
														do
														{
														$crm_company_list = call($_SESSION["query_data"]["domain"], "crm.company.list", array(
															"auth" => $_SESSION["query_data"]["access_token"],
															"order" => array("DATE_CREATE: ASC"),
															"filter" => array("TYPE_ID: CLIENT"),
															"select" => array("ID", "TITLE", "ASSIGNED_BY_ID", "REVENUE", "UF_CRM_1492615945"),
															"start" => $start_bx24_id
														));
														
														$next_list = isset($crm_company_list["next"]) ? true : false; //проверка на наличие продолжения списка
														
														//обрабатываем данные и вывыодим список клиентов.
														foreach ($crm_company_list as $keys => $arrays)
														{
														
															if(isset($arrays) && is_array($arrays)) foreach ($arrays as $key => $value)
															{
																//генерируем выпадающий сисок клиентов
																echo '<option value="'.$value["ID"].'_'.$value["TITLE"].'">'.$value["TITLE"].' ('.getUserInfo_BX24ID($value["ASSIGNED_BY_ID"], 'last_name').' '.substr(getUserInfo_BX24ID($value["ASSIGNED_BY_ID"], 'name'), 0, 2).')</option>';
															}  
															
														}
														
														//проверка на наличие продолжения списка (если список продолжается, укажем с какой позиции)
														if ($next_list) {
															$start_bx24_id = $crm_company_list["next"];
														}
														
														}
														while ($next_list == true);
														
														
														?>
														</select>
                                                    </div>
                                                </div>
												
												<div class="form-group">
                                                    <label class="control-label col-md-4"> Оплата</label>
                                                    <div class="col-md-7">
														<select class="form-control m-bot15" name="pay_metod" id="pay_metod">
														<option value="bill">Безналичный расчет</option>
														<option value="cash">Наличный расчет</option>
														</select>
														<input type="text" class="form-control" name="bill_number" onkeyup="check();" style="width: 100%" placeholder="Номер счета">
<br>                                                   				
																			<div class="input-group m-bot15">
																				<input type="text" name="bill_total" class="form-control" placeholder="Сумма заказа">
																				<span class="input-group-addon">.руб</span>
																			</div>
                                                  				
																			<div class="input-group m-bot15">
																				<input type="text" name="bill_prepay" class="form-control" placeholder="Сумма оплаты">
																				<span class="input-group-addon">.руб</span>
																			</div>
                                                  				
																			<div class="input-group m-bot15">
																				<input type="text" name="bill_transfer" class="form-control" placeholder="Доставка">
																				<span class="input-group-addon">.руб</span>
																			</div>
																			
																			<div class="input-group m-bot15">
																				<input type="text" name="bill_bulding" class="form-control" placeholder="Монтаж">
																				<span class="input-group-addon">.руб</span>
																			</div>
																			
																			<div class="input-group m-bot15">
																				<input type="text" name="bill_bonus" class="form-control" placeholder="Бонус менеджера">
																				<span class="input-group-addon">.руб</span>
																			</div>
																			
													</div>
													


													


                                                </div>

												<div class="form-group">
													<label class="control-label col-md-4">Срок производства <br> <label style="font-size: 7pt; color: #EF0000;"><b><?=$c + 1;?></b> - рекомендуемое кол-во рабочих дней с учетом <b><?=$factory_load;?>%</b> загруженности производства</label></label>
													<div class="col-md-7">
														<div id="spinner1">
															<div class="input-group input-small">
																<input type="text" name="date_ready" class="spinner-input form-control" value="<?=$c + 1;?>" maxlength="3" readonly="">
																<div class="spinner-buttons input-group-btn btn-group-vertical">
																	<button type="button" class="btn spinner-up btn-xs btn-default">
																		<i class="fa fa-angle-up"></i>
																	</button>
																	<button type="button" class="btn spinner-down btn-xs btn-default">
																		<i class="fa fa-angle-down"></i>
																	</button>
																</div>
															</div>
														</div>
													 <!-- <span class="help-block">
													  <i>дата отгрузки: <u>13-02-1993</u></i>
													 </span> -->
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-md-4">Бланк заказа</label>
													<div class="controls col-md-7">
														<div class="fileupload fileupload-new" data-provides="fileupload"><input type="hidden">
																<span class="btn btn-default btn-file">
																<span class="fileupload-new"><i class="fa fa-cloud-upload"></i> загрузить бланк в формате MS Excel </span>
																<span class="fileupload-exists"><i class="fa fa-undo"></i> выбрать другой бланк заказа </span>
																<input type="file" name="app_file" style="width: 300px;" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="default">
																</span>
															<span class="fileupload-preview" style="margin-left:0px; margin-top:3px; font-size: 8pt;"></span>
															<a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none; margin-left:5px;"></a>
														</div>
													</div>
												</div>
												
												<div class="form-group">
                                                    <label class="control-label col-md-4"> Комментарий к заказу <br> <label style="font-size: 7pt;">если необходимо дополнить заявку важной ифнормацией</label></label>
                                                    <div class="col-md-7">
														<textarea rows="4" name="manager_comment" class="form-control"></textarea>
                                                    </div>
                                                </div>
												
												
												
                                            
                                        </div>
										
										<input type="hidden" name="work_day_workload" value="<?=$c + 1;?>">
										<input type="hidden" name="percentage_workload" value="<?=$factory_load;?>">
										<input type="hidden" name="date_ready_workload" value="<?=working_days_factory_finish($aa1, $bb1);?> 00:00:00">
										
                                        <div class="modal-footer">
                                            <!-- <button data-dismiss="modal" class="btn btn-primary" type="button">Close</button> -->
											<button disabled class="btn btn-success" name="app_add_btn" type="submit">Запустить в производство</button>
                                        </div>
									</form>
                                    </div>
                                </div>
                            </div>
						<!-- форма запуска нового заказа /конец/ -->

<!-- КНОПКА+ФОРМА btn_app_start конец -->			
						</div>
						
						<div class="col-sm-7">
<!-- КНОПКА+ФОРМА КАЛЬКУЛЯТОР btn_app_start -->
						<a class="btn btn-warning btn-lg btn-block" data-toggle="modal" href="#orderDATEREADYCALC">
                            <i class="fa fa-calculator"></i>&nbsp;&nbsp;Ориентировочная дата готовности
                        </a>
			 			
												<!-- форма запуска нового заказа -->
						<div class="modal fade" id="orderDATEREADYCALC" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
									
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Окно огорчения менеджера</h4>
                                        </div>
                                        <div class="modal-body">	
										
											<div class="form-group">
												<div class="col-lg-12">
													<center>
														<h4><b>
															Информация о загруженности производства <br><br>
														</b></h4>
													</center>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-md-12">
													<div class="row">
														<div class="panel">
															
																<div class="col-sm-6">
																	<div class="panel" style="background-color: #f0f0f0">
																		<div class="panel-body">
																			<center>
																			<b>Дверей в работе</b> <br> <i><?=$app_c_summ_all;?> шт</i>
																			</center>
																		</div>
																	</div>
																</div>
																
																<div class="col-sm-6">
																	<div class="panel" style="background-color: #f0f0f0">
																		<div class="panel-body">
																			<center>
																			<b>До</b> <br> <i><?=d_format($aa1, 'f1');?></i>
																			</center>
																		</div>
																	</div>
																</div>
															
														</div>
													</div>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-md-12">
													<div class="row">
														<div class="panel"> 
															<div class="col-sm-12">
																<div class="progress progress-striped active progress-sm">
																	<div style="width: <?=$factory_load_progress_bar;?>%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?=$factory_load_progress_bar;?>" role="progressbar" class="progress-bar progress-bar-<?=$factory_load_pb_color;?>">
																		<span class="sr-only">загруженность <?=$factory_load;?>%</span>
																		загруженность <?=$factory_load;?>%
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-md-12">
													<center>
													<div class="text-center" style="font-size: 9px;">последнее обновление данных <?=d_format(date("Y-m-d H:i:s"), 'f3');?></div><br><br>
													</center>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-lg-12">
													<center>
														<h4><b>
															Количество дверей в вашем заказе <br><br>
														</b></h4>
													</center>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-lg-12">
													<div class="row">
														<div class="col-lg-2">
															<input type="text" class="form-control" id="val_1" placeholder="1">
														</div>
														<div class="col-lg-2">
															<input type="text" class="form-control" id="val_2" placeholder="2">
														</div>
														<div class="col-lg-2">
															<input type="text" class="form-control" id="val_B" placeholder="В">
														</div>
														<div class="col-lg-2">
															<input type="text" class="form-control" id="val_L" placeholder="Л">
														</div>
														<div class="col-lg-2">
															<input type="text" class="form-control" id="val_F" placeholder="Ф">
														</div>
														<div class="col-lg-2">
															<input type="text" class="form-control" id="val_NONE" placeholder="Ъ">
														</div>
													</div>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-md-12">
													<center>
													<br> <b><i id="print_calc_date"> ---------------- </i></b> <br><br>
													</center>
												</div>
											</div>
											
                                        </div>

                                        <div class="modal-footer-noborder">
										
                                            <!-- <button data-dismiss="modal" class="btn btn-primary" type="button">Close</button> -->
											<button class="btn btn-warning" id="calc_date_ready" type="submit" style="width: 100%;">Получить дату</button>
                                        </div>
									
                                    </div>
                                </div>
                            </div>
						<!-- форма запуска нового заказа /конец/ -->

<!-- КНОПКА+ФОРМА КАЛЬКУЛЯТОР btn_app_start конец -->
						</div>
					</div>
				</div>
			</div>
		</div>
		
<?
	}
}
?>
	
		<div class="row">
			<div class="col-sm-12">
				<section class="panel">
					<header class="panel-heading">
						Заказы в производстве
					</header>
					<div class="panel-body"> 
			
						<table class="table  table-hover general-table">
							<thead>
							<tr>
<?
if (check_access('hidden_date_factory', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{							
?>
								<th style="width: 260px;">Контрагент </th>
<?
	}							
?>
								<th style="text-align: center; width: 80px;">Заказ</th> <!-- class="hidden-phone" -->
<?
if (check_access('hidden_date_factory', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{							
?>
								<th style="text-align: center;">Счет</th>
<?
	}							
?>
								<th style="text-align: center; background: #f9f9f9; border-right: 1px solid #dddddd; border-top: 1px solid #dddddd; border-left: 1px solid #dddddd;">1</th>
								<th style="text-align: center; background: #f9f9f9; border-right: 1px solid #dddddd; border-top: 1px solid #dddddd; border-left: 1px solid #dddddd;">2</th>
								<th style="text-align: center; background: #f9f9f9; border-right: 1px solid #dddddd; border-top: 1px solid #dddddd; border-left: 1px solid #dddddd;">В</th>
								<th style="text-align: center; background: #f9f9f9; border-right: 1px solid #dddddd; border-top: 1px solid #dddddd; border-left: 1px solid #dddddd;">Л</th>
								<th style="text-align: center; background: #f9f9f9; border-right: 1px solid #dddddd; border-top: 1px solid #dddddd; border-left: 1px solid #dddddd;">Ф</th>
								<th style="text-align: center; background: #f9f9f9; border-right: 1px solid #dddddd; border-top: 1px solid #dddddd; border-left: 1px solid #dddddd;">Ъ</th>
								<th style="text-align: center;">В очередь</th>
								<th style="text-align: center;">Менеджер</th>
								<th style="text-align: center;">Готовность</th>
<?
// -- проверяем, включен ли вход по запасному шлюзу авторизации
if ($CONFIG['yandex_oauth'] == 'disabled') {
?>
							<th style="text-align: center; width: 80px;"></th>
<?
}
?>
							</tr>
							</thead>
							<tbody>
							<?php	
							
// Гехтман Алексей, Прошина Виктория, Анциферова Наталья
$head_dep_ids = '8, 20, 24';

$stmt_employees_dep_ALL = $dbConnection->query('SELECT * from crm_employees');
$employees_dep_ALL = $stmt_employees_dep_ALL->fetchAll();

$dep_id_massive_ALL = array("0");
foreach($employees_dep_ALL as $employees_dep_row_ALL) {
	// добавляем элемент в массив
	array_push($dep_id_massive_ALL, '"'.$employees_dep_row_ALL['bitrix24_id'].'"');
}
$app_view_ALL = $dep_id_massive_ALL;

switch(getUserInfo($_SESSION["auth_id"], 'work_position_access')) {
		
		case 'fullacc': 
			$app_view = $app_view_ALL;
		break;
		case 'director_dev': 
			$app_view = $app_view_ALL;
		break;
		case 'director': 
			$app_view = $app_view_ALL;
		break;
		case 'accountant': 
			$app_view = $app_view_ALL;
		break;
		case 'factory_head': 
			$app_view = $app_view_ALL;
		break;
		case 'factory_ec': 
			$app_view = $app_view_ALL;
		break;
		case 'salesteam_manager': 
			$app_view = array('"'.getUserInfo($_SESSION["auth_id"], 'bitrix24_id').'"');
		break;
		
		case 'salesteam_manager_the_call': 
			$app_view = array('"'.getUserInfo($_SESSION["auth_id"], 'bitrix24_id').'"');
		break;
		
		case 'salesteam_manager_extended': 
			$app_view = array('"'.getUserInfo($_SESSION["auth_id"], 'bitrix24_id').'"');
		break;
		
		case 'salesteam_head': // руководитель отдела продаж
			$bx_24_dep_id = getUserInfo($_SESSION["auth_id"], 'bitrix24_department_id');
			$stmt_employees_dep = $dbConnection->query('SELECT * from crm_employees WHERE bitrix24_department_id = "'.$bx_24_dep_id.'"');
			$employees_dep = $stmt_employees_dep->fetchAll();	
			$total1 = count($employees_dep);
			$counter1 = 0;
			$dep_id_massive = array("0");
			if ($total1 == 0) {
				$dep_id_massive = array("0");
			} else {
			foreach($employees_dep as $employees_dep_row) {
				// добавляем элемент в массив
				array_push($dep_id_massive, '"'.$employees_dep_row['bitrix24_id'].'"');
			}
			$app_view = $dep_id_massive;
			}
		break;
		
		default:
			$app_view = '';
		break;
}

								foreach($applist as $applist_row) {
								//$applist_row['lock_by'];
								//$applist_row['ok_by'];
								// <span class="badge badge-success" style="font-size: 8px;">100%</span>
								// <span class="badge badge-default" style="font-size: 8px;">70%</span>
								// <span class="label" style="background: #a6e691;">частично отгружена</span>
									//определяем id следующего счета
							//$stmt_rbi = $dbConnection->query('SELECT * from crm_bills WHERE app_id = '.$applist_row['num'].'');
							//$real_bill_id = $stmt_rbi->fetchAll();
							//$real_bill_id = $real_bill_id[1]['bill_number'];
							
							$stmt_rbi = $dbConnection->prepare('SELECT bill_number FROM crm_bills WHERE app_id = :num');
							$stmt_rbi -> execute(array('num' => $applist_row['num']));
							$bill_value = $stmt_rbi->fetchColumn();
							
							if ($bill_value == 'Без счета') {
								$bill_value = 'б\с';
							}
							
							//процент оплаты
							$stmt_rbi1 = $dbConnection->prepare('SELECT bill_prepay FROM crm_bills WHERE app_id = :num');
							$stmt_rbi1 -> execute(array('num' => $applist_row['num']));
							$bill_prepay = $stmt_rbi1->fetchColumn();
							$stmt_rbi2 = $dbConnection->prepare('SELECT bill_total FROM crm_bills WHERE app_id = :num');
							$stmt_rbi2 -> execute(array('num' => $applist_row['num']));
							$bill_total = $stmt_rbi2->fetchColumn();
							if ($bill_total == 0) {
							$bill_prepay_percent == 0;
							} else {
							$bill_prepay_percent = ($bill_prepay/$bill_total)*100;
							$bill_prepay_percent = round($bill_prepay_percent, 0);
							}
							
							if ($applist_row['status'] == 'app-ready') {
								$status_btn = 'disabled';
							} if ($applist_row['status'] == 'app-gotobuilding') {
								$status_btn = '';
							} if ($applist_row['status'] == 'app-production') {
								$status_btn = 'disabled';
							} if ($applist_row['status'] == 'app-suspended') {
								$status_btn = 'disabled';
							} if ($applist_row['status'] == 'app-stopped') {
								$status_btn = 'disabled';
							} if ($applist_row['status'] == 'app-shipped') {
								$status_btn = 'disabled';
							}
							
//Уточнение дней отгрузки (сколько осталось до отгрузки) date("Y-m-d H:i:s") $applist_row['date_start'] $applist_row['date_ready']
if ($applist_row['date_ready'] == date("Y-m-d").' 00:00:00') {
	$date_ready_table = '<b style="color: #DF0000;">СЕГОДНЯ</b>';
} else if ($applist_row['date_ready'] == date("Y-m-d", strtotime("+1 day")).' 00:00:00') {
	$date_ready_table = '<b style="color: #0087E8;">ЗАВТРА</b>';
} else {
	$date_ready_table = d_format($applist_row['date_ready'], 'f1');
}


if ($applist_row['work_day_workload'] == 0 || $applist_row['percentage_workload'] == 0) {
	$ignore_workload = '';
} else {
	if ($applist_row['date_ready'] < $applist_row['date_ready_workload'] && $applist_row['work_day_workload'] != 0) {
		$ignore_workload = '<span class="label label-warning" style="font-size: 8px;">без учета загруженности</span>';
	} else {
		$ignore_workload = '';
	}
}


							?>
							<tr id="<?=$applist_row['num'];?>" class="<?=$applist_row['status'];?>" style="font-size: 13px;">
<?
if (check_access('hidden_date_factory', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{	

		// отображаем информацию в зависимости от иерархии и доступности
		if (in_array('"'.$applist_row['bitrix24_client_id'].'"', $app_view)) {
?>
								<td style="border-bottom: 1px solid #dddddd; color: #000000;"><a href="https://dverim.bitrix24.ru/crm/company/show/<?=$applist_row['bitrix24_kontragent_id'];?>/" target="_blank"><?=$applist_row['bitrix24_kontragent_name'];?></a></td>
<?
		} else {
?>
								<td style="border-bottom: 1px solid #dddddd; color: #bbbbbb;"><i class="fa fa-lock fa-1x" aria-hidden="true"> нет доступа</i></td>
<?
		}
	}
?>
								<td style="text-align: center; border-bottom: 1px solid #dddddd;"><a href="order_info.php?num=<?=$applist_row['num'];?>" style="font-size: 14px;"><b><?=appnumclear($applist_row['num']);?></b></a> <br> <b style="font-size: 6.5pt; color: #000000;"><span id="<?=$applist_row['num'];?>"><?=$APP_STATUS[$applist_row['status']];?></span></b> <!-- <span class="label label-<?//=$applist_row['status'];?>" style="margin: 3em;"><?//=$APP_STATUS[$applist_row['status']];?></span> --></td>
<?
if (check_access('hidden_date_factory', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{							

		// отображаем информацию в зависимости от иерархии и доступности
		if (in_array('"'.$applist_row['bitrix24_client_id'].'"', $app_view)) {
?>
								<td style="text-align: center; border-bottom: 1px solid #dddddd;"><?=$bill_value;?><br> <span class="badge badge-default" style="font-size: 8px;"><?=$bill_prepay_percent;?>%</span></td>
<?
		} else {
?>
								<td style="text-align: center; border-bottom: 1px solid #dddddd; color: #bbbbbb;"><i style="font-size: 10px;" class="fa fa-lock fa-1x" aria-hidden="true"> <br>нет доступа</i><br></td>
<?
		}
	}
?>
								<td style="text-align: center; border: 1px solid #dddddd; font-weight: bold; color: #000000;"><?=$applist_row['c_single'];?></td>
								<td style="text-align: center; border: 1px solid #dddddd; font-weight: bold; color: #000000;"><?=$applist_row['c_double'];?></td>
								<td style="text-align: center; border: 1px solid #dddddd; font-weight: bold; color: #000000;"><?=$applist_row['c_gates'];?></td>
								<td style="text-align: center; border: 1px solid #dddddd; font-weight: bold; color: #000000;"><?=$applist_row['c_hatches'];?></td>
								<td style="text-align: center; border: 1px solid #dddddd; font-weight: bold; color: #000000;"><?=$applist_row['c_transoms'];?></td>
								<td style="text-align: center; border: 1px solid #dddddd; font-weight: bold; color: #000000;"><?=$applist_row['c_others'];?></td>
								<td style="text-align: center; border-bottom: 1px solid #dddddd;"><?=d_format($applist_row['date_start'], 'f3');?></td> 
								<td style="text-align: center; border-bottom: 1px solid #dddddd;"><?=name_format_l_n($applist_row['bitrix24_client_name']);?></td>
								<td style="text-align: center; border-bottom: 1px solid #dddddd;"><?=$date_ready_table;?><br><?=$ignore_workload;?></td>
<?
// -- проверяем, включен ли вход по запасному шлюзу авторизации
if ($CONFIG['yandex_oauth'] == 'disabled') {
?>
								<td style="text-align: center; border-bottom: 1px solid #dddddd;">
								<button <?=$status_btn;?> id="app-ready_<?=$applist_row['num'];?>" value="<?=$applist_row['num'];?>" class="btn btn-app-ready tooltips" style="padding: 7px 17px;" type="button" data-toggle="tooltip " data-placement="bottom" title="" data-original-title="Готов"><i class="fa fa-check" aria-hidden="true"></i></button>
								</td>
<?
}
?>
							</tr>
							<?php
							}
							?>	
							</tbody>
						</table>
					</div>

<!-- постраничная навигация -->

<center>
<div>
	<ul class="pagination pagination-sm">

<?
// _________________ начало блока 1 _________________

// Выводим ссылки "назад" и "на первую страницу"
if ($page>=1) {

    // Значение page= для первой страницы всегда равно единице, 
    // поэтому так и пишем
    echo '<li><a href="?page=1">&nbsp;первая страница&nbsp;</a></li>';

    // Так как мы количество страниц до этого уменьшили на единицу, 
    // то для того, чтобы попасть на предыдущую страницу, 
    // нам не нужно ничего вычислять
    echo '<li><a href="?page=' . $page . '">&nbsp;<&nbsp;</a></li>';
}

// __________________ конец блока 1 __________________

// На данном этапе номер текущей страницы = $page+1
$_this = $page+1;

// Узнаем с какой ссылки начинать вывод
$start = $_this-$limit;

// Узнаем номер последней ссылки для вывода
$end = $_this+$limit;

// Выводим ссылки на все страницы
// Начальное число $j в нашем случае должно равнятся единице, а не нулю
for ($j = 1; $j<$pages; $j++) {

    // Выводим ссылки только в том случае, если их номер больше или равен
    // начальному значению, и меньше или равен конечному значению
    if ($j>=$start && $j<=$end) {

        // Ссылка на текущую страницу выделяется жирным
        if ($j==($page+1)) echo '<li class="active"><a href="?page=' . $j . '">&nbsp;' . $j . '&nbsp;</a></li>';

        // Ссылки на остальные страницы
        else echo '<li><a href="?page=' . $j . '">&nbsp;' . $j . '&nbsp;</a></li>';
    }
}

// _________________ начало блока 2 _________________

// Выводим ссылки "вперед" и "на последнюю страницу"
if ($j>$page && ($page+2)<$j) {

    // Чтобы попасть на следующую страницу нужно увеличить $pages на 2
    echo '<li><a href="?page=' . ($page+2) . '">&nbsp;>&nbsp;</a></li>';

    // Так как у нас $j = количество страниц + 1, то теперь 
    // уменьшаем его на единицу и получаем ссылку на последнюю страницу
    echo '<li><a href="?page=' . ($j-1) . '">&nbsp;последняя страница&nbsp;</a></li>';
}

// __________________ конец блока 2 __________________
?>

	</ul>
</div>
</center>

<br>

<!-- постраничная навигация /конец/ -->					
					
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
	
//обработчик расчета даты производства указанных дверей
$('button[id="calc_date_ready"]').click(function() {
	var val_1 = $("input[id='val_1']").attr("value");
	var val_2 = $("input[id='val_2']").attr("value");
	var val_B = $("input[id='val_B']").attr("value");
	var val_L = $("input[id='val_L']").attr("value");
	var val_F = $("input[id='val_F']").attr("value");
	var val_NONE = $("input[id='val_NONE']").attr("value");
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'calc_date_ready', 'val_1': val_1, 'val_2': val_2, 'val_B': val_B, 'val_L': val_L, 'val_F': val_F, 'val_NONE': val_NONE},
			cache: false,			
		success: function(response){
				$('i[id="print_calc_date"]').html(response);	
			}
		});
	//}		
	});

});
</script>