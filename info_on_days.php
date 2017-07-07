<?php
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = "Информация по загруженности - ".$CONFIG['title_header'];

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
<!--

         <div class="page-heading">
		 
		<!-- панель проверки прав администратора 
		 	<div class="panel panel-default">
                <div class="panel-body">
                    <center>
					
					</center>
                </div>
            </div>
		<!-- конец панель проверки прав администратора 
			
        </div>
		
		
        <!-- page heading end 
-->
		

        <!--body wrapper start-->
	<div class="wrapper">

	
	<div class="row">
	<div class="col-sm-4">
	<div class="panel panel-default">
		<div class="panel-heading">СТАТИСТИКА ЗА <?=date("Y");?> ГОД</div>
		<?
		// Всего заявок, за исключением ОТМЕНЕННЫХ
		$stmt_app_all = $dbConnection->query('SELECT * from crm_applist WHERE status != "app-stopped"');
		$app_all = $stmt_app_all->fetchAll();
		$app_all_count = $stmt_app_all->rowCount();

		// считаем сумму дверей за исключением ОТМЕНЕННЫХ
		$sumapp_c_summ = 0;
		foreach($app_all as $app_all_row) {
			$sumapp_c_summ = $sumapp_c_summ + $app_all_row['c_summ'];
		}
		?>
		<div class="panel-body">
		<center>
			<h3>Заказов</h3>
			<br>
			<h4><b><?=$app_all_count;?></b></h4>
			<br>
			<h3>Дверей</h3>
			<br>
			<h4><b><?=$sumapp_c_summ;?></b></h4>
		</center>
		<br>
		<div class="text-center" style="font-size: 8px;">последнее обновление данных <?=d_format(date("Y-m-d H:i:s"), 'f3');?> <br> информация предоставляется без учета заказов со статусом "отменён"</div>
		</div>
	</div>
	</div>

	<div class="col-sm-8">
	<div class="panel panel-success">
		<div class="panel-heading">ПОДРОБНАЯ СТАТИСТИКА за <?=date("Y");?> ГОД</div>
		<?
		// Всего заявок
		$stmt_app_all_all = $dbConnection->query('SELECT * from crm_applist WHERE status != "app-stopped"');
		$app_all_all_count = $stmt_app_all_all->rowCount();
		$app_all_all = $stmt_app_all_all->fetchAll();
		// Всего дверей
		$sumdoor_c_summ = 0;
		foreach($app_all_all as $app_all_all_row) {
			$sumdoor_c_summ = $sumdoor_c_summ + $app_all_all_row['c_summ'];
		}
		
		// Функция для подсчета колличества заявок с заданым статусом
		function countAppToStatus($in) {
			global $dbConnection;
			$stmt_app_status = $dbConnection->query('SELECT * from crm_applist WHERE status = "'.$in.'"');
			$app_status_count = $stmt_app_status->rowCount();
		return $app_status_count;
		}
		
		// Функция для подсчета колличества дверей в заявках с заданым статусом
		function countDoorToStatus($in) {
			global $dbConnection;
			$stmt_door_status = $dbConnection->query('SELECT * from crm_applist WHERE status = "'.$in.'"');
			$door_status = $stmt_door_status->fetchAll();
			$door_status_count = $stmt_door_status->rowCount();
			// считаем сумму дверей заявок с заданым статусом
			$sumdoor_c_summ = 0;
			foreach($door_status as $door_status_row) {
				$sumdoor_c_summ = $sumdoor_c_summ + $door_status_row['c_summ'];
			}
		return $sumdoor_c_summ;
		}
		
		
		?>
			<div class="panel-body">

				<b>В очереди</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; заказов: <u><?=countAppToStatus('app-production');?></u> шт. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; дверей: <u><?=countDoorToStatus('app-production');?></u> шт.
				<div class="progress">
					<div style="width: <?=round((countDoorToStatus('app-production')/$sumdoor_c_summ)*100, 2);?>%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?=round((countDoorToStatus('app-production')/$sumdoor_c_summ)*100, 2);?>" role="progressbar" class="progress-bar progress-bar-info">
						<?=round((countDoorToStatus('app-production')/$sumdoor_c_summ)*100, 2);?>%
					</div>
				</div>

				<b>Запущено</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; заказов: <u><?=countAppToStatus('app-gotobuilding');?></u> шт. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; дверей: <u><?=countDoorToStatus('app-gotobuilding');?></u> шт.
				<div class="progress">
					<div style="width: <?=round((countDoorToStatus('app-gotobuilding')/$sumdoor_c_summ)*100);?>%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?=round((countDoorToStatus('app-gotobuilding')/$sumdoor_c_summ)*100, 2);?>" role="progressbar" class="progress-bar">
						<?=round((countDoorToStatus('app-gotobuilding')/$sumdoor_c_summ)*100, 2);?>%
					</div>
				</div>
				<b>Готово</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; заказов: <u><?=countAppToStatus('app-ready');?></u> шт. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; дверей: <u><?=countDoorToStatus('app-ready');?></u> шт.
				<div class="progress">
					<div style="width: <?=round((countDoorToStatus('app-ready')/$sumdoor_c_summ)*100 + 6, 2);?>%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?=round((countDoorToStatus('app-ready')/$sumdoor_c_summ)*100 + 6, 2);?>" role="progressbar" class="progress-bar progress-bar-success">
						<?=round((countDoorToStatus('app-ready')/$sumdoor_c_summ)*100, 2);?>%
					</div>
				</div>
				<b>Отгружено</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; заказов: <u><?=countAppToStatus('app-shipped');?></u> шт. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; дверей: <u><?=countDoorToStatus('app-shipped');?></u> шт.
				<div class="progress">
					<div style="width: <?=round((countDoorToStatus('app-shipped')/$sumdoor_c_summ)*100, 2);?>%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?=round((countDoorToStatus('app-shipped')/$sumdoor_c_summ)*100, 2);?>" role="progressbar" class="progress-bar progress-bar-success">
						<?=round((countDoorToStatus('app-shipped')/$sumdoor_c_summ)*100, 2);?>%
					</div>
				</div>
				<b>Остановлено</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; заказов: <u><?=countAppToStatus('app-suspended');?></u> шт. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; дверей: <u><?=countDoorToStatus('app-suspended');?></u> шт.
				<div class="progress">
					<div style="width: <?=round((countDoorToStatus('app-suspended')/$sumdoor_c_summ)*100 + 6, 2);?>%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?=round((countDoorToStatus('app-suspended')/$sumdoor_c_summ)*100 + 6, 2);?>" role="progressbar" class="progress-bar progress-bar-warning">
						<?=round((countDoorToStatus('app-suspended')/$sumdoor_c_summ)*100, 2);?>%
					</div>
				</div>
				<!--
				<b>Отменено</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; заказов: <u><?=countAppToStatus('app-stopped');?></u> шт. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; дверей: <u><?=countDoorToStatus('app-stopped');?></u> шт.
				<div class="progress">
					<div style="width: <?=round((countAppToStatus('app-stopped')/$app_all_all_count)*100);?>%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?=round((countAppToStatus('app-stopped')/$app_all_all_count)*100);?>" role="progressbar" class="progress-bar progress-bar-danger">
						<?=round((countAppToStatus('app-stopped')/$app_all_all_count)*100);?>%
					</div>
				</div>
				-->
				<div class="text-center" style="font-size: 8px;">последнее обновление данных <?=d_format(date("Y-m-d H:i:s"), 'f3');?> <br> информация предоставляется без учета заказов со статусом "отменён"</div>
			</div>
	</div>
	</div>
	</div>
	
	
	
	
	
		<div class="row">
			<div class="col-sm-12">

<!-- ТЕКУЩАЯ ЗАГРУЖЕННОСТЬ 
info-number-panels
<span class="badge" style="right: 100px;">новое</span>
-->
<section class="panel">
                    <header class="panel-heading">
                        Текущая загруженность производства 
						<span class="tools pull-right">
							<a class="fa fa-chevron-down" href="javascript:;"></a>
						</span>
                    </header>
                    <div class="panel-body">

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
$bb1 = ceil(82 / ($output_per_day + $output_per_day_smallapp)); // предполагаемый заказ 82 двери делим на производительность 50 дверей, получаем кол-во рабочих дней на обработку (округляем в большую сторону)
$cc1 = working_days_factory_finish($aa1, $bb1);

$nn1 = count_work_day_factory_date_to_date(date("Y-m-d"), d_format($app_end_date_ready[0]["date_ready"], "f9")); // Кол-во рабочих дней производства от текущей даты до даты готовности крайней заявки
$nn2 = count_work_day_factory_date_to_date(date("Y-m-d"), d_format($aa1, "f9")); // Кол-во рабочих дней производства от текущей даты до фактической даты готовности всего объема с учетом производительности

$factory_load = ceil(factory_workload($app_c_summ_all, $nn1, $nn2));

$engine_today = $app_c_summ_all / $nn1;

if ($factory_load > 100) {
	$factory_load_progress_bar = 100;
} else {
	$factory_load_progress_bar = $factory_load;
}

$factory_load_pb_color = factory_workload_pb_color($factory_load);

?>	




											
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
												<?
													/*
													echo 'Кол-во рабочих дней производства в текущем месяце - <b>'.count_work_day_factory(date('m')).'</b> <br>';
													echo 'Кол-во рабочих дней необходимых для выполнения заказов со статусом "в очереди" и "запущен" - <b>'.$c.' ('.$nn2.')</b> <br>';
													echo '<br>';
													echo 'Кол-во заказов со статусом "в очереди" и "запущен" - <b>'.$app_to_factory_count.'</b> <br>';
													echo 'Кол-во дверей по заказам со статусом "в очереди" и "запущен" - <b>'.$app_c_summ_all.'</b> <br>';
													echo '<br><br>';
													echo date("Y-m-d").' - '.d_format($app_end_date_ready[0]["date_ready"], "f9");
													echo '<br><br>';
													echo 'Кол-во рабочих дней производства от текущей даты до даты готовности крайней заявки - <b>'.count_work_day_factory_date_to_date(date("Y-m-d"), d_format($app_end_date_ready[0]["date_ready"], "f9")).'</b> <br>';
													*/
												?>
												
												При производственной мощности <b><?=$output_per_day + $output_per_day_smallapp;?> дверей</b> в сутки, объем заказов <b><?=$app_to_factory_count;?> шт </b> прибывающих в статусах "в очереди" и "запущен", общим количеством дверей <b><?=$app_c_summ_all;?> шт </b> будет полностью отработан до <b><?=d_format($aa1, 'f1');?></b> включительно.
												<br><br>
												На данный момент готовность заказов общим количеством дверей <b><?=$app_c_summ_all;?> шт </b> прибывающих в статусах "в очереди" и "запущен", должна состояться до <b><?=d_format($app_end_date_ready[0]["date_ready"], "f1");?></b> включительно, при этом, требуемая производственная мощность ровняется <b><?=ceil($engine_today);?></b> дверей в сутки.
												<br><br>
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
													<div class="text-center" style="font-size: 9px;">последнее обновление данных <?=d_format(date("Y-m-d H:i:s"), 'f3');?></div>
													</center>
												</div>
											</div>

					
                    </div>
                </section>
<!-- ТЕКУЩАЯ ЗАГРУЖЕННОСТЬ /конец/ -->			

<!-- ТАБЛИЦА 1 -->
<section class="panel">
                    <header class="panel-heading">
                        Статистика по запущенным заказам за текущий месяц <!-- &nbsp;&nbsp;<a href="#"><i style="color: #227547;" class="fa fa-file-excel-o" aria-hidden="true"></i></a> -->
						<span class="tools pull-right">
							<a class="fa fa-chevron-up" href="javascript:;"></a>
						</span>
                    </header>
                    <div class="panel-body" style="display: none;">
					<b><i>В статистике учитываются все заказы, кроме заказов со статусом "<u>отменен</u>".</i></b>
					<br><br>
                        <table class="table table-hover">
                            <thead>
<?
//Название месяца (сущ.)
$_monthsNameList = array(
"1"=>"ЯНВАРЬ","2"=>"ФЕВРАЛЬ","3"=>"МАРТ",
"4"=>"АПРЕЛЬ","5"=>"МАЙ", "6"=>"ИЮНЬ",
"7"=>"ИЮЛЬ","8"=>"АВГУСТ","9"=>"СЕНТЯБРЬ",
"10"=>"ОКТЯБРЬ","11"=>"НОЯБРЬ","12"=>"ДЕКАБРЬ"
);
$_month = $_monthsNameList[date("n")].' '.date("Y"); /* имя тесущего месяца и год */
?>
                            <tr>
                                <th style="border-bottom: 2px solid #000000; width: 120px;"><?=$_month;?></th>
								<th style="border-bottom: 2px solid #000000; width: 80px;"></th>
                                <th style="border-bottom: 2px solid #000000; text-align: center; width: 90px;">№ заказа</th>
								<th style="border-bottom: 2px solid #000000; text-align: center; width: 160px;">Готовность</th>
								<th style="border: 2px solid #000000; text-align: center;">1</th>
								<th style="border: 2px solid #000000; text-align: center;">2</th>
								<th style="border: 2px solid #000000; text-align: center;">В</th>
								<th style="border: 2px solid #000000; text-align: center;">Л</th>
								<th style="border: 2px solid #000000; text-align: center;">Ф</th>
								<th style="border: 2px solid #000000; text-align: center;">Ъ</th>
								<th style="background: #f4f4f4; border: 2px solid #000000; text-align: center; width: 100px;">ИТОГО</th>
                            </tr>
                            </thead>
                            <tbody>
<?
//Запрос на получения списка заявок
$stmt_applist_1 = $dbConnection->query('SELECT * from crm_applist WHERE MONTH(`date_start`) = MONTH(NOW()) AND status != "app-stopped" ORDER BY date_start ASC');
$applist = $stmt_applist_1->fetchAll();
// для вывода ИТОГО по последнему дню + всей таблице, посчитаем сколько раз пройдет цикл
$applist_count = $stmt_applist_1->rowCount();
//Выводим в цикле заявки
// переменная контроля даты
$start_date_cycle = 0;
// счетчик для подсчета ИТОГО за день
$i = 0;
// ИТОГО за день
$sum_day_c_single = 0;
$sum_day_c_double = 0;
$sum_day_c_gates = 0;
$sum_day_c_hatches = 0;
$sum_day_c_transoms = 0;
$sum_day_c_others = 0;
$sum_day_c_summ = 0;
// ИТОГО
$sum_c_single = 0;
$sum_c_double = 0;
$sum_c_gates = 0;
$sum_c_hatches = 0;
$sum_c_transoms = 0;
$sum_c_others = 0;
$sum_c_summ = 0;

// цикл
foreach($applist as $applist_row) {
if (d_format($applist_row['date_start'], 'f9') == $start_date_cycle){
?>
                            <tr> <!-- 1 -->
                                <td></td>
								<td>в <?=d_format($applist_row['date_start'], 'f10');?></td>
                                <td style="text-align: center;"><u><?=appnumclear($applist_row['num']);?></u> <br> <i style="font-size: 9pt;"><?=$APP_STATUS[$applist_row['status']];?></i></td>
								<td style="text-align: center;"><?=d_format($applist_row['date_ready'], 'f1');?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_single'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_double'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_gates'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_hatches'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_transoms'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_others'];?></td>
								<td style="background: #f4f4f4; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$applist_row['c_summ'];?></b></td>
                            </tr>
<?
// считаем сумму дверей (по типу) за один день
$sum_day_c_single = $sum_day_c_single + $applist_row['c_single'];
$sum_day_c_double = $sum_day_c_double + $applist_row['c_double'];
$sum_day_c_gates = $sum_day_c_gates + $applist_row['c_gates'];
$sum_day_c_hatches = $sum_day_c_hatches + $applist_row['c_hatches'];
$sum_day_c_transoms = $sum_day_c_transoms + $applist_row['c_transoms'];
$sum_day_c_others = $sum_day_c_others + $applist_row['c_others'];
$sum_day_c_summ = $sum_day_c_summ + $applist_row['c_summ'];
} else {
if (d_format($applist_row['date_start'], 'f9') != $start_date_cycle && $i > 0 || $i == $applist_count + 1) {
?>
                            <tr> <!-- итого за каждый день -->
                                <td colspan="4" style="background: #f4f4f4; border-top: 1px solid #000000; border-left: 1px solid #000000; text-align: center;"><b>ИТОГО ЗА ДЕНЬ</td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_single;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_double;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_gates;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_hatches;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_transoms;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_others;?></b></td>
								<td style="background: #e2e2e2; border: 1px solid #000000; text-align: center; color: #000000; font-size: 16px;"><b><i><?=$sum_day_c_summ;?></i></b></td>
                            </tr>
<?
// считаем сумму дверей (по типу) ИТОГО
$sum_c_single = $sum_c_single + $sum_day_c_single;
$sum_c_double = $sum_c_double + $sum_day_c_double;
$sum_c_gates = $sum_c_gates + $sum_day_c_gates;
$sum_c_hatches = $sum_c_hatches + $sum_day_c_hatches;
$sum_c_transoms = $sum_c_transoms + $sum_day_c_transoms;
$sum_c_others = $sum_c_others + $sum_day_c_others;
$sum_c_summ = $sum_c_summ + $sum_day_c_summ;
// обнуляем сумму дверей (по типу) за один день
$sum_day_c_single = 0;
$sum_day_c_double = 0;
$sum_day_c_gates = 0;
$sum_day_c_hatches = 0;
$sum_day_c_transoms = 0;
$sum_day_c_others = 0;
$sum_day_c_summ = 0;
}
?>
                            <tr> <!-- 2 -->
                                <td style="border-top: 2px solid #000000;"><?=d_format($applist_row['date_start'], 'f1');?></td>
								<td style="border-top: 2px solid #000000;">в <?=d_format($applist_row['date_start'], 'f10');?></td>
                                <td style="border-top: 2px solid #000000; text-align: center;"><u><?=appnumclear($applist_row['num']);?></u> <br> <i style="font-size: 9pt;"><?=$APP_STATUS[$applist_row['status']];?></i></td>
								<td style="border-top: 2px solid #000000; text-align: center;"><?=d_format($applist_row['date_ready'], 'f1');?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_single'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_double'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_gates'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_hatches'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_transoms'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_others'];?></td>
								<td style="background: #f4f4f4; border-top: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$applist_row['c_summ'];?></b></td>
                            </tr>
<?
// считаем сумму дверей (по типу) за один день
$sum_day_c_single = $sum_day_c_single + $applist_row['c_single'];
$sum_day_c_double = $sum_day_c_double + $applist_row['c_double'];
$sum_day_c_gates = $sum_day_c_gates + $applist_row['c_gates'];
$sum_day_c_hatches = $sum_day_c_hatches + $applist_row['c_hatches'];
$sum_day_c_transoms = $sum_day_c_transoms + $applist_row['c_transoms'];
$sum_day_c_others = $sum_day_c_others + $applist_row['c_others'];
$sum_day_c_summ = $sum_day_c_summ + $applist_row['c_summ'];
}

if ($i + 1 == $applist_count) {
// считаем сумму дверей (по типу) ИТОГО     !!! с учетом последнего дня !!!
$sum_c_single = $sum_c_single + $sum_day_c_single;
$sum_c_double = $sum_c_double + $sum_day_c_double;
$sum_c_gates = $sum_c_gates + $sum_day_c_gates;
$sum_c_hatches = $sum_c_hatches + $sum_day_c_hatches;
$sum_c_transoms = $sum_c_transoms + $sum_day_c_transoms;
$sum_c_others = $sum_c_others + $sum_day_c_others;
$sum_c_summ = $sum_c_summ + $sum_day_c_summ;
?>
                            <tr> <!-- итого за последний день -->
                                <td colspan="4" style="background: #f4f4f4; border-top: 1px solid #000000; border-bottom: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><b>ИТОГО ЗА ДЕНЬ</b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_single;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_double;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_gates;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_hatches;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_transoms;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_others;?></b></td>
								<td style="background: #e2e2e2; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; color: #000000; font-size: 16px;"><b><i><?=$sum_day_c_summ;?></i></b></td>
                            </tr>
							
                            <tr> <!-- разрыв -->
                                <td colspan="11" style="text-align: center;" onmouseover="this.style.backgroundColor='#ffffff';" onmouseout="this.style.backgroundColor='#ffffff';"><br><br></td>
							</tr>
							
                            <tr> <!-- ИТОГО -->
                                <td colspan="4" style="background: #f4f4f4; border: 2px solid #000000; text-align: center;"><b>ИТОГО ЗА <u><?=$_month;?></u></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_single;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_double;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_gates;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_hatches;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_transoms;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_others;?></b></td>
								<td style="background: #e2e2e2; border: 2px solid #000000; text-align: center; color: #000000; font-size: 16px;"><b><i><?=$sum_c_summ;?></i></b></td>
                            </tr>
<?
// обнуляем сумму дверей (по типу) за последний день
$sum_day_c_single = 0;
$sum_day_c_double = 0;
$sum_day_c_gates = 0;
$sum_day_c_hatches = 0;
$sum_day_c_transoms = 0;
$sum_day_c_others = 0;
$sum_day_c_summ = 0;
// обнуляем сумму ИТОГО
$sum_c_single = 0;
$sum_c_double = 0;
$sum_c_gates = 0;
$sum_c_hatches = 0;
$sum_c_transoms = 0;
$sum_c_others = 0;
$sum_c_summ = 0;
}
$start_date_cycle = d_format($applist_row['date_start'], 'f9');
$i++;
}
?>
                            </tbody>
                        </table>
                    </div>
                </section>
<!-- ТАБЛИЦА 1 /конец/ -->








<!-- ТАБЛИЦА 2 -->
<section class="panel">
                    <header class="panel-heading">
                        Статистика по готовности заказов <!-- &nbsp;&nbsp;<a href="#"><i style="color: #227547;" class="fa fa-file-excel-o" aria-hidden="true"></i></a> -->
						<span class="tools pull-right">
							<a class="fa fa-chevron-up" href="javascript:;"></a>
						</span>
                    </header>
                    <div class="panel-body" style="display: none;">
					<b><i>В статистике учитываются все заявки со статусами "<u>в очереди</u>", "<u>запущен</u>", "<u>готов</u>".</i></b>
					<br><br>
                        <table class="table table-hover">
                            <thead>
<?
//Название месяца (сущ.)
$_monthsNameList = array(
"1"=>"ЯНВАРЬ","2"=>"ФЕВРАЛЬ","3"=>"МАРТ",
"4"=>"АПРЕЛЬ","5"=>"МАЙ", "6"=>"ИЮНЬ",
"7"=>"ИЮЛЬ","8"=>"АВГУСТ","9"=>"СЕНТЯБРЬ",
"10"=>"ОКТЯБРЬ","11"=>"НОЯБРЬ","12"=>"ДЕКАБРЬ"
);
$_month = $_monthsNameList[date("n")].' '.date("Y"); /* имя тесущего месяца и год */
?>
                            <tr>
                                <th style="border-bottom: 2px solid #000000; width: 130px;">Готовность</th>
                                <th style="border-bottom: 2px solid #000000; text-align: center; width: 110px;">№ заказа</th>
								<th style="border: 2px solid #000000; text-align: center;">1</th>
								<th style="border: 2px solid #000000; text-align: center;">2</th>
								<th style="border: 2px solid #000000; text-align: center;">В</th>
								<th style="border: 2px solid #000000; text-align: center;">Л</th>
								<th style="border: 2px solid #000000; text-align: center;">Ф</th>
								<th style="border: 2px solid #000000; text-align: center;">Ъ</th>
								<th style="background: #f4f4f4; border: 2px solid #000000; text-align: center; width: 100px;">ИТОГО</th>
                            </tr>
                            </thead>
                            <tbody>
<?
//Запрос на получения списка заявок
$stmt_applist_1 = $dbConnection->query('SELECT * from crm_applist WHERE status = "app-gotobuilding" OR status = "app-production" OR status = "app-ready" ORDER BY date_ready ASC');
$applist = $stmt_applist_1->fetchAll();
// для вывода ИТОГО по последнему дню + всей таблице, посчитаем сколько раз пройдет цикл
$applist_count = $stmt_applist_1->rowCount();
//Выводим в цикле заявки
// переменная контроля даты
$start_date_cycle = 0;
// счетчик для подсчета ИТОГО за день
$i = 0;
// ИТОГО за день
$sum_day_c_single = 0;
$sum_day_c_double = 0;
$sum_day_c_gates = 0;
$sum_day_c_hatches = 0;
$sum_day_c_transoms = 0;
$sum_day_c_others = 0;
$sum_day_c_summ = 0;
// ИТОГО
$sum_c_single = 0;
$sum_c_double = 0;
$sum_c_gates = 0;
$sum_c_hatches = 0;
$sum_c_transoms = 0;
$sum_c_others = 0;
$sum_c_summ = 0;

// цикл
foreach($applist as $applist_row) {
if (d_format($applist_row['date_start'], 'f9') == $start_date_cycle){
?>
                            <tr> <!-- 1 -->
                                <td></td>
								<td style="text-align: center;"><u><?=appnumclear($applist_row['num']);?></u> <br> <i style="font-size: 9pt;"><?=$APP_STATUS[$applist_row['status']];?></i></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_single'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_double'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_gates'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_hatches'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_transoms'];?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_others'];?></td>
								<td style="background: #f4f4f4; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$applist_row['c_summ'];?></b></td>
                            </tr>
<?
// считаем сумму дверей (по типу) за один день
$sum_day_c_single = $sum_day_c_single + $applist_row['c_single'];
$sum_day_c_double = $sum_day_c_double + $applist_row['c_double'];
$sum_day_c_gates = $sum_day_c_gates + $applist_row['c_gates'];
$sum_day_c_hatches = $sum_day_c_hatches + $applist_row['c_hatches'];
$sum_day_c_transoms = $sum_day_c_transoms + $applist_row['c_transoms'];
$sum_day_c_others = $sum_day_c_others + $applist_row['c_others'];
$sum_day_c_summ = $sum_day_c_summ + $applist_row['c_summ'];
} else {
if (d_format($applist_row['date_start'], 'f9') != $start_date_cycle && $i > 0 || $i == $applist_count + 1) {
?>
                            <tr> <!-- итого за каждый день -->
                                <td colspan="2" style="background: #f4f4f4; border-top: 1px solid #000000; border-left: 1px solid #000000; text-align: center;"><b>ИТОГО</td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_single;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_double;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_gates;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_hatches;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_transoms;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_others;?></b></td>
								<td style="background: #e2e2e2; border: 1px solid #000000; text-align: center; color: #000000; font-size: 16px;"><b><i><?=$sum_day_c_summ;?></i></b></td>
                            </tr>
<?
// считаем сумму дверей (по типу) ИТОГО
$sum_c_single = $sum_c_single + $sum_day_c_single;
$sum_c_double = $sum_c_double + $sum_day_c_double;
$sum_c_gates = $sum_c_gates + $sum_day_c_gates;
$sum_c_hatches = $sum_c_hatches + $sum_day_c_hatches;
$sum_c_transoms = $sum_c_transoms + $sum_day_c_transoms;
$sum_c_others = $sum_c_others + $sum_day_c_others;
$sum_c_summ = $sum_c_summ + $sum_day_c_summ;
// обнуляем сумму дверей (по типу) за один день
$sum_day_c_single = 0;
$sum_day_c_double = 0;
$sum_day_c_gates = 0;
$sum_day_c_hatches = 0;
$sum_day_c_transoms = 0;
$sum_day_c_others = 0;
$sum_day_c_summ = 0;
}
?>
                            <tr> <!-- 2 -->
                                <td style="border-top: 2px solid #000000;"><?=d_format($applist_row['date_ready'], 'f1');?></td>
                                <td style="border-top: 2px solid #000000; text-align: center;"><u><?=appnumclear($applist_row['num']);?></u> <br> <i style="font-size: 9pt;"><?=$APP_STATUS[$applist_row['status']];?></i></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_single'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_double'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_gates'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_hatches'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_transoms'];?></td>
								<td style="border-top: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><?=$applist_row['c_others'];?></td>
								<td style="background: #f4f4f4; border-top: 2px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$applist_row['c_summ'];?></b></td>
                            </tr>
<?
// считаем сумму дверей (по типу) за один день
$sum_day_c_single = $sum_day_c_single + $applist_row['c_single'];
$sum_day_c_double = $sum_day_c_double + $applist_row['c_double'];
$sum_day_c_gates = $sum_day_c_gates + $applist_row['c_gates'];
$sum_day_c_hatches = $sum_day_c_hatches + $applist_row['c_hatches'];
$sum_day_c_transoms = $sum_day_c_transoms + $applist_row['c_transoms'];
$sum_day_c_others = $sum_day_c_others + $applist_row['c_others'];
$sum_day_c_summ = $sum_day_c_summ + $applist_row['c_summ'];
}

if ($i + 1 == $applist_count) {
// считаем сумму дверей (по типу) ИТОГО     !!! с учетом последнего дня !!!
$sum_c_single = $sum_c_single + $sum_day_c_single;
$sum_c_double = $sum_c_double + $sum_day_c_double;
$sum_c_gates = $sum_c_gates + $sum_day_c_gates;
$sum_c_hatches = $sum_c_hatches + $sum_day_c_hatches;
$sum_c_transoms = $sum_c_transoms + $sum_day_c_transoms;
$sum_c_others = $sum_c_others + $sum_day_c_others;
$sum_c_summ = $sum_c_summ + $sum_day_c_summ;
?>
                            <tr> <!-- итого за последний день -->
                                <td colspan="2" style="background: #f4f4f4; border-top: 1px solid #000000; border-bottom: 2px solid #000000; border-left: 1px solid #000000; text-align: center;"><b>ИТОГО</b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_single;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_double;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_gates;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_hatches;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_transoms;?></b></td>
								<td style="background: #f4f4f4; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_day_c_others;?></b></td>
								<td style="background: #e2e2e2; border: 1px solid #000000; border-bottom: 2px solid #000000; text-align: center; color: #000000; font-size: 16px;"><b><i><?=$sum_day_c_summ;?></i></b></td>
                            </tr>
							
                            <tr> <!-- разрыв -->
                                <td colspan="9" style="text-align: center;" onmouseover="this.style.backgroundColor='#ffffff';" onmouseout="this.style.backgroundColor='#ffffff';"><br><br></td>
							</tr>
							
                            <tr> <!-- ИТОГО -->
                                <td colspan="2" style="background: #f4f4f4; border: 2px solid #000000; text-align: center;"><b>ИТОГО К ОТГРУЗКЕ</u></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_single;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_double;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_gates;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_hatches;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_transoms;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_others;?></b></td>
								<td style="background: #e2e2e2; border: 2px solid #000000; text-align: center; color: #000000; font-size: 16px;"><b><i><?=$sum_c_summ;?></i></b></td>
                            </tr>
<?
// обнуляем сумму дверей (по типу) за последний день
$sum_day_c_single = 0;
$sum_day_c_double = 0;
$sum_day_c_gates = 0;
$sum_day_c_hatches = 0;
$sum_day_c_transoms = 0;
$sum_day_c_others = 0;
$sum_day_c_summ = 0;
// обнуляем сумму ИТОГО
$sum_c_single = 0;
$sum_c_double = 0;
$sum_c_gates = 0;
$sum_c_hatches = 0;
$sum_c_transoms = 0;
$sum_c_others = 0;
$sum_c_summ = 0;
}
$start_date_cycle = d_format($applist_row['date_start'], 'f9');
$i++;
}
?>
                            </tbody>
                        </table>
                    </div>
                </section>
<!-- ТАБЛИЦА 2 /конец/ -->







			
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