<?php 
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = "Заказ №".appnumclear($_GET['num'])." - ".$CONFIG['title_header'];

//подключаем верстку
include("head.inc.php"); //шапка сайта
include("navbarleft.inc.php"); //боковое меню навигации
include("navbartop.inc.php"); //верхнее меню навигации
			
?>	
<!-- начало тела станицы -->

        <!--body wrapper start-->
	<div class="wrapper">

<?
//вывод информации по номеру заявки
if (isset($_GET['num']) && is_numeric($_GET['num'])) {


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



// ДЛЯ АДМИНИСТРАТОРА\ПРОИЗВОДТВА\РУКОВОДСТВА
if (check_access('btn_app_status', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{

switch(getOrderInfo($_GET['num'], 'status')) {
	
//$status_btn_ready
//$status_btn_gotobuilding
//$status_btn_production
//$status_btn_suspended
//$status_btn_stopped
//$status_btn_shipped
// = 'disabled';
// = '';

	case 'app-ready':
		$status_btn_ready = 'disabled style="font-weight:bold; background-color: #fff; color: #000;"';
		$status_btn_gotobuilding = '';
		$status_btn_production = '';
		$status_btn_suspended = '';
		$status_btn_stopped = '';
		$status_btn_shipped = '';
	break;
	
	case 'app-production':
		$status_btn_ready = '';
		$status_btn_gotobuilding = '';
		$status_btn_production = 'disabled style="font-weight:bold; background-color: #fff; color: #000;"';
		$status_btn_suspended = '';
		$status_btn_stopped = '';
		$status_btn_shipped = '';
	break;
	
	case 'app-gotobuilding':
		$status_btn_ready = '';
		$status_btn_gotobuilding = 'disabled style="font-weight:bold; background-color: #fff; color: #000;"';
		$status_btn_production = '';
		$status_btn_suspended = '';
		$status_btn_stopped = '';
		$status_btn_shipped = '';
	break;
	
	case 'app-suspended':
		$status_btn_ready = '';
		$status_btn_gotobuilding = '';
		$status_btn_production = '';
		$status_btn_suspended = 'disabled style="font-weight:bold; background-color: #fff; color: #000;"';
		$status_btn_stopped = '';
		$status_btn_shipped = '';
	break;
	
	case 'app-stopped':
		$status_btn_ready = '';
		$status_btn_gotobuilding = '';
		$status_btn_production = '';
		$status_btn_suspended = '';
		$status_btn_stopped = 'disabled style="font-weight:bold; background-color: #fff; color: #000;"';
		$status_btn_shipped = '';
	break;
	
	case 'app-shipped':
		$status_btn_ready = '';
		$status_btn_gotobuilding = '';
		$status_btn_production = '';
		$status_btn_suspended = '';
		$status_btn_stopped = '';
		$status_btn_shipped = 'disabled style="font-weight:bold; background-color: #fff; color: #000;"';
	break;

	default:
		$status_btn_ready = 'disabled';
		$status_btn_gotobuilding = 'disabled';
		$status_btn_production = 'disabled';
		$status_btn_suspended = 'disabled';
		$status_btn_stopped = 'disabled';
		$status_btn_shipped = 'disabled';
	;
}	

	} else {

switch(getOrderInfo($_GET['num'], 'status')) {
	
//$status_btn_ready
//$status_btn_gotobuilding
//$status_btn_production
//$status_btn_suspended
//$status_btn_stopped
//$status_btn_shipped
// = 'disabled';
// = '';

	case 'app-ready':
		$status_btn_ready = 'disabled';
		$status_btn_gotobuilding = 'disabled';
		$status_btn_production = 'disabled';
		$status_btn_suspended = 'disabled';
		$status_btn_stopped = 'disabled';
		$status_btn_shipped = '';
	break;
	
	case 'app-production':
		$status_btn_ready = '';
		$status_btn_gotobuilding = '';
		$status_btn_production = 'disabled';
		$status_btn_suspended = '';
		$status_btn_stopped = '';
		$status_btn_shipped = 'disabled';
	break;
	
	case 'app-gotobuilding':
		$status_btn_ready = '';
		$status_btn_gotobuilding = 'disabled';
		$status_btn_production = 'disabled';
		$status_btn_suspended = '';
		$status_btn_stopped = '';
		$status_btn_shipped = 'disabled';
	break;
	
	case 'app-suspended':
		$status_btn_ready = 'disabled';
		$status_btn_gotobuilding = '';
		$status_btn_production = '';
		$status_btn_suspended = 'disabled';
		$status_btn_stopped = '';
		$status_btn_shipped = 'disabled';
	break;
	
	case 'app-stopped':
		$status_btn_ready = 'disabled';
		$status_btn_gotobuilding = 'disabled';
		$status_btn_production = '';
		$status_btn_suspended = 'disabled';
		$status_btn_stopped = 'disabled';
		$status_btn_shipped = 'disabled';
	break;
	
	case 'app-shipped':
		$status_btn_ready = 'disabled';
		$status_btn_gotobuilding = 'disabled';
		$status_btn_production = 'disabled';
		$status_btn_suspended = 'disabled';
		$status_btn_stopped = 'disabled';
		$status_btn_shipped = 'disabled';
	break;

	default:
		$status_btn_ready = 'disabled';
		$status_btn_gotobuilding = 'disabled';
		$status_btn_production = 'disabled';
		$status_btn_suspended = 'disabled';
		$status_btn_stopped = 'disabled';
		$status_btn_shipped = 'disabled';
	;
}	
	
	}

// строим запрос на вывод информации об изменении статуса заказа
$stmt = $dbConnection->query('SELECT * FROM crm_apps_status WHERE app_num = "'.$_GET['num'].'" ORDER BY date DESC');
$app_status = $stmt->fetchAll();
							
?>

		<div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <!--pagination start-->
                            <section class="panel">
                                <header class="panel-heading">
                                    Информация о заказе 
<?
if (check_access('elements_administration', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{							
?>
									&nbsp;&nbsp; <i id="edit-btn-info-order" value="edit" class="fa fa-pencil-square-o" style="color: #f40000; cursor: pointer;" aria-hidden="true"></i>
<?
	}
?>
									<span class="tools pull-right">
										<a class="fa fa-chevron-down" href="javascript:;"></a>
									</span>
                                </header>
                                <div class="panel-body">
									<table class="table holder-edit" style="font-size: 14px;">
										<thead>
										<tr>
											<th style="width: 16.5%; text-align: center; border: 2px solid #c4c4c4; font-size: 17px; background: #dddddd; color: #000000">1</th>
											<th style="width: 16.5%; text-align: center; border: 2px solid #c4c4c4; font-size: 17px; background: #dddddd; color: #000000">2</th>
											<th style="width: 16.5%; text-align: center; border: 2px solid #c4c4c4; font-size: 17px; background: #dddddd; color: #000000">В</th>
											<th style="width: 16.5%; text-align: center; border: 2px solid #c4c4c4; font-size: 17px; background: #dddddd; color: #000000">Л</th>
											<th style="width: 16.5%; text-align: center; border: 2px solid #c4c4c4; font-size: 17px; background: #dddddd; color: #000000">Ф</th>
											<th style="width: 17.5%; text-align: center; border: 2px solid #c4c4c4; font-size: 17px; background: #dddddd; color: #000000">Ъ</th>
										</tr>
										</thead>
										<tbody>
										<tr>
											<td style="text-align: center; border: 2px dotted #c4c4c4"><b id="edit_c_single"><?=getOrderInfo($_GET['num'], 'c_single');?></b></td>
											<td style="text-align: center; border: 2px dotted #c4c4c4"><b id="edit_c_double"><?=getOrderInfo($_GET['num'], 'c_double');?></b></td>
											<td style="text-align: center; border: 2px dotted #c4c4c4"><b id="edit_c_gates"><?=getOrderInfo($_GET['num'], 'c_gates');?></b></td>
											<td style="text-align: center; border: 2px dotted #c4c4c4"><b id="edit_c_hatches"><?=getOrderInfo($_GET['num'], 'c_hatches');?></b></td>
											<td style="text-align: center; border: 2px dotted #c4c4c4"><b id="edit_c_transoms"><?=getOrderInfo($_GET['num'], 'c_transoms');?></b></td>
											<td style="text-align: center; border: 2px dotted #c4c4c4"><b id="edit_c_others"><?=getOrderInfo($_GET['num'], 'c_others');?></b></td>
										</tr>
										</tbody>
									</table>
									<div align="right"><b>Всего в заказе: <u id="edit_c_summ"><?=getOrderInfo($_GET['num'], 'c_summ');?></u> шт.</b></div>
									<br>
									<div class="well well-large">
									<div class="form-group">
										<label class="control-label col-md-4">
										<b>Номер заказа:</b>
										</label>
										<div class="controls col-md-8" style="font-size: 16px;">
										<u><?=appnumclear($_GET['num']);?></u>
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="control-label col-md-4">
										<b>Статус:</b>
										</label>
										<div id="labe_status" class="controls col-md-8">
										<span class="label label-<?=getOrderInfo($_GET['num'], 'status');?>"><?=$APP_STATUS[getOrderInfo($_GET['num'], 'status')];?></span>
										</div>
									</div>
									<br>
<?
if (check_access('hidden_date_factory', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{							
?>
									<div class="form-group">
										<label class="control-label col-md-4">
										<b>Контрагент:</b>
										</label>
										<div class="controls col-md-8">
										<?	
												// отображаем информацию в зависимости от иерархии и доступности
												if (in_array('"'.getOrderInfo($_GET['num'], 'bitrix24_client_id').'"', $app_view)) {
										?>
										
										
										<a id="edit_kontragent_a" style="display: block;" target="_blank" value="<?=getOrderInfo($_GET['num'], 'bitrix24_kontragent_id');?>" href="https://dverim.bitrix24.ru/crm/company/show/<?=getOrderInfo($_GET['num'], 'bitrix24_kontragent_id');?>/"><?=getOrderInfo($_GET['num'], 'bitrix24_kontragent_name');?></a>
										<select id="edit_kontragent_select" style="display: none; border: 1px solid #ff0000;" class="form-control input-sm m-bot15" name="bitrix24_kontragent">
										<?
										//получаем список организаций с учетом структуры компании в Битрикс24 (за 1 раз call может выдать массив с 50 компаниями) 
										$start_bx24_id = 0;
										do {
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
												echo '<option value="'.$value["ID"].'">'.$value["TITLE"].'</option>';
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

										
										<?
												} else {
										?>
										<i style="color: #bbbbbb;" class="fa fa-lock fa-1x" aria-hidden="true"> нет доступа</i></td>
										<?
												}
										?>
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="control-label col-md-4">
										<b>Счет №:</b>
										</label>
										<div id="labe_status" class="controls col-md-8">
										<?	
												// отображаем информацию в зависимости от иерархии и доступности
												if (in_array('"'.getOrderInfo($_GET['num'], 'bitrix24_client_id').'"', $app_view)) {
										?>
										<a role="button" data-toggle="modal" href="#billINFO" class="btn btn-default btn-xs"><?=getBillInfo($_GET['num'], 'bill_number');?></a>
										
						<!-- форма запуска нового заказа -->
						<div class="modal fade" id="billINFO" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="width: 400px;">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Данные по счету <!-- &nbsp;&nbsp; <i id="edit-btn-info-bill" value="edit" class="fa fa-pencil-square-o" style="color: #f40000; cursor: pointer;" aria-hidden="true"></i> --></h4> 
                                        </div>
                                        <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="control-label col-md-5"><b>Вид оплаты</b></label>
                                                    <div class="col-md-7">
													<?
													if (getBillInfo($_GET['num'], 'pay_metod') == 'bill') {
														$pay_metod = 'Безналичный расчет';
													} else {
														$pay_metod = 'Наличный расчет';
													}
													echo $pay_metod;
													?>
                                                    </div>
                                                </div>
												<br>
												<div class="form-group">
                                                    <label class="control-label col-md-5"><b>Номер счета</b></label>
                                                    <div class="col-md-7">
													<link id="bill_number"><?=getBillInfo($_GET['num'], 'bill_number');?></link>
                                                    </div>
                                                </div>
												<br>
												<div class="form-group">
                                                    <label class="control-label col-md-5"><b>Сумма счета</b></label>
                                                    <div class="col-md-7">
													<link id="bill_total"><?=getBillInfo($_GET['num'], 'bill_total');?></link> руб.
                                                    </div>
                                                </div>
												<br>
												<div class="form-group">
                                                    <label class="control-label col-md-5"><b>Предоплата</b></label>
                                                    <div class="col-md-7">
													<?
													if (getBillInfo($_GET['num'], 'bill_total') == 0) {
													$bill_prepay_percent == 0;
													} else {
													$bill_prepay_percent = (getBillInfo($_GET['num'], 'bill_prepay')/getBillInfo($_GET['num'], 'bill_total'))*100;
													$bill_prepay_percent = round($bill_prepay_percent, 0);
													}
													?>
													<?=getBillInfo($_GET['num'], 'bill_prepay');?> руб. <b>(<?=$bill_prepay_percent;?>%)</b>
                                                    </div>
                                                </div>
												<br>
												<div class="form-group">
                                                    <label class="control-label col-md-5"><b>Доставка</b></label>
                                                    <div class="col-md-7">
													<link id="bill_transfer"><?=getBillInfo($_GET['num'], 'bill_transfer');?></link> руб.
                                                    </div>
                                                </div>
												<br>
												<div class="form-group">
                                                    <label class="control-label col-md-5"><b>Монтаж</b></label>
                                                    <div class="col-md-7">
													<link id="bill_bulding"><?=getBillInfo($_GET['num'], 'bill_bulding');?></link> руб.
                                                    </div>
                                                </div>
												<br>
												<div class="form-group">
                                                    <label class="control-label col-md-5"><b>Бонус менеджера</b></label>
                                                    <div class="col-md-7">
													<link id="bill_bonus"><?=getBillInfo($_GET['num'], 'bill_bonus');?></link> руб.
                                                    </div>
                                                </div>
												
                                        </div>

                                        <div class="modal-footer">
											<?
											if($bill_prepay_percent == 100){ 
												if(getBillInfo($_GET['num'], 'bill_postpay') == 0){
											?>
												<div class="well well-large"><b style="font-size: 17px;"><i>Оплачен на 100% при запуске заказа!</i></b><br></div>
												<button disabled class="btn btn-success btn-lg btn-block" name="app_add_btn" type="submit">Отметить полную оплату счета</button>
											<?
												} else {
											?>
												<div class="well well-large"><b><i><?=getBillInfo($_GET['num'], 'postpay_user_name');?></i></b> отметил(а) полную оплату счета <?=d_format(getBillInfo($_GET['num'], 'postpay_date'), 'f3');?></div>
												<button disabled class="btn btn-success btn-lg btn-block" name="app_add_btn" type="submit">Отметить полную оплату счета</button>
											<?
												}
											} else {
											?>
											
											<button class="btn btn-success btn-lg btn-block" name="postpay_bill" value="<?=getOrderInfo($_GET['num'], 'num');?>" type="submit">Отметить полную оплату счета</button>
											
											<?
											}
											?>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<!-- форма запуска нового заказа /конец/ -->

										<?
												} else {
										?>
										<i style="color: #bbbbbb;" class="fa fa-lock fa-1x" aria-hidden="true"> нет доступа</i></td>
										<?
												}
										?>
										</div>
									</div>
									<br>
<?
	}
?>
									<div class="form-group">
										<label class="control-label col-md-4">
										<b>Менеджер:</b>
										</label>
										<div class="controls col-md-8">
										<a target="_blank" href="https://dverim.bitrix24.ru/company/personal/user/<?=getOrderInfo($_GET['num'], 'bitrix24_client_id');?>/"><?=name_format_l_n(getOrderInfo($_GET['num'], 'bitrix24_client_name'));?></a>
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="control-label col-md-4">
										<b>В очередь:</b>
										</label>
										<div class="controls col-md-8">
										<?=d_format(getOrderInfo($_GET['num'], 'date_start'), 'f3');?>
										</div>
									</div>
									<br>
									<div class="form-group">
										<label class="control-label col-md-4">
										<b>Готовность:</b>
										</label>
										<div class="controls col-md-8">
										<?=d_format(getOrderInfo($_GET['num'], 'date_ready'), 'f1');?>
<?
if (getOrderInfo($_GET['num'], 'work_day_workload') == 0 || getOrderInfo($_GET['num'], 'percentage_workload') == 0) {
	
} else {
	
	if (getOrderInfo($_GET['num'], 'date_ready') < getOrderInfo($_GET['num'], 'date_ready_workload') && getOrderInfo($_GET['num'], 'work_day_workload') != 0) {
?>
<b style="color: #d90000;"> *</b>
<?
	} else {
		
	}
}
?>
										</div>
									</div>
<?
if (getOrderInfo($_GET['num'], 'work_day_workload') == 0 || getOrderInfo($_GET['num'], 'percentage_workload') == 0) {
	
} else {
	
	if (getOrderInfo($_GET['num'], 'date_ready') < getOrderInfo($_GET['num'], 'date_ready_workload') && getOrderInfo($_GET['num'], 'work_day_workload') != 0) {
?>
									<br>
									<div class="form-group">
										<label class="control-label col-md-4">
										</label>
										<div class="controls col-md-8" style="color: #d90000; font-size: 10px;">
										<b>* </b>был проигнорирован рекомендованный минимальный срок в <b><?=getOrderInfo($_GET['num'], 'work_day_workload');?></b> рабочих дней с учетом <b><?=getOrderInfo($_GET['num'], 'percentage_workload');?>%</b> загруженности производства
										</div>
									</div>
									<br><br>
<?
	} else {
		
	}
}
?>
									</div>
                                </div>
                            </section>

                            <!--pagination end-->
                        </div>
                    </div>				
				
				
				
                    <div class="row">
                        <div class="col-md-12">
                            <!--pagination start-->
                            <section class="panel">
                                <header class="panel-heading">
                                    Статус заказа
									<span class="tools pull-right">
										<a class="fa fa-chevron-down" href="javascript:;"></a>
									</span>
                                </header>
                                <div class="panel-body">
<?
// -- проверяем, включен ли вход по запасному шлюзу авторизации
if ($CONFIG['yandex_oauth'] == 'disabled') {
?>
								<center>
                                    <button class="btn btn-success" <?=$status_btn_ready;?> value="<?=$_GET['num'];?>" name="app-ready" type="button">Готов</button>
									<button class="btn btn-primary" <?=$status_btn_production;?> value="<?=$_GET['num'];?>" name="app-production" type="button">В очереди</button>
									<button class="btn btn-warning" <?=$status_btn_suspended;?> value="<?=$_GET['num'];?>" name="app-suspended" type="button">Остановить</button>
									<button class="btn btn-danger" <?=$status_btn_stopped;?> value="<?=$_GET['num'];?>" name="app-stopped" type="button">Отменить</button>
									<button class="btn btn-info" <?=$status_btn_shipped;?> value="<?=$_GET['num'];?>" name="app-shipped" type="button">Отгрузить</button>
									<?
									if (getUserInfo($_SESSION["auth_id"], 'email') == 'ec.factory@1dvm.ru' || getUserInfo($_SESSION["auth_id"], 'email') == 'head.factory@1dvm.ru' || getUserInfo($_SESSION["auth_id"], 'email') == 'kt@1dvm.ru') {
									?>
									<br><br> 
									<button <?=$status_btn_gotobuilding;?> class="btn btn-default btn-xs btn-block" value="<?=$_GET['num'];?>" name="app-gotobuilding" type="button">Запустить в производство</button>
									<?
									} else {
									?>
									<?
									}
									?>
								</center>
								<br>
<?
}
?>								
									
<div>

				<table class="table table-hover" style="font-size: 11px;">
				<thead>
				<tr>
					<th style="width: 30%">Сотрудник</th>
					<th style="width: 25%">Статус</th>
					<th>Комментарий</th>
				</tr>
				</thead>
				<tbody>
<?
foreach($app_status as $app_status_row) {
if ($app_status_row['comments'] == '') {
	$status_comments = 'без комментария...';
} else {
	$status_comments = $app_status_row['comments'];
}
?>
				<tr>
					<td><a class="name" href="https://dverim.bitrix24.ru/company/personal/user/<?=$app_status_row['bitrix24_client_id'];?>/"><?=$app_status_row['bitrix24_client_name'];?></a></td>
					<td><span class="label label-<?=$app_status_row['status'];?>"><?=$APP_STATUS[$app_status_row['status']];?></span> <br> <b><?=d_format($app_status_row['date'], 'f7');?></b></td>
					<td><?=$status_comments;?></td>
				</tr>
<?
}
?>
				</tbody>
			</table>

</div>

                                </div>
                            </section>
                            <!--pagination end-->
                        </div>
                    </div>

					
					
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <!--label and badge start-->
                            <section class="panel">
                                <header class="panel-heading">
                                    Актуальный бланк заявки
									<span class="tools pull-right">
										<a class="fa fa-chevron-down" href="javascript:;"></a>
									</span>
                                </header>
                                <div class="panel-body">
									<b>Заявка поставлена в очередь с комментарием:</b>
									<br>
									<div class="well well-large">
									<?
									if (getOrderInfo($_GET['num'], 'comments') == '') {
										echo 'без комментария...';
									} else {
										echo getOrderInfo($_GET['num'], 'comments');
									}
									?>
									</div>

									<a role="button" target="_blank" href="https://docs.google.com/viewer?url=<?=$CONFIG['hostname'].'uploads/apps_files/'.date('Y').'/'.appnumclear($_GET['num']).'/'.getOrderInfo($_GET['num'], 'app_file_excel');?>" class="btn btn-success btn-lg btn-block">Открыть актуальный бланк от <?=d_format(getOrderInfo($_GET['num'], 'app_file_excel_date'), 'f3');?></a>
									<br>
									<a role="button"  href="<?=$CONFIG['hostname'].'uploads/apps_files/'.date('Y').'/'.appnumclear($_GET['num']).'/'.getOrderInfo($_GET['num'], 'app_file_excel');?>" class="btn btn-primary btn-xs col-md-12">Скачать актуальный бланк от <?=d_format(getOrderInfo($_GET['num'], 'app_file_excel_date'), 'f3');?></a>
									
									<br><br><br>
									<center><b>Предыдущие версии бланка заказа</b></center>
									
<?
// строим запрос на вывод информации об обновлении бланка
$app_upd_count = 0;
$stmt_app_upd = $dbConnection->query('SELECT * FROM crm_apps_blank_upd WHERE app_num = "'.$_GET['num'].'" ORDER BY upd_date DESC');
$app_upd = $stmt_app_upd->fetchAll();
$app_upd_count = $stmt_app_upd->rowCount();
?>									
									<table class="table table-hover" style="font-size: 11px;">
									<thead>
									<tr>
										<th style="width: 15%"></th>
										<th style="width: 32%">Кто обновил</th>
										<th>1</th>
										<th>2</th>
										<th>В</th>
										<th>Л</th>
										<th>Ф</th>
										<th>Ъ</th>
										<th>Итого</th>
									</tr>
									</thead>
									<tbody>
<?
if ($app_upd_count != 0) {
$i = $app_upd_count;
foreach($app_upd as $app_upd_row) {
?>

									<tr>
										<td><a class="name" target="_blank" href="<?=$CONFIG['hostname'].'uploads/apps_files/'.date('Y').'/'.appnumclear($app_upd_row['app_num']).'/'.$app_upd_row['old_blank_link'];?>">скачать версию <?=$i--;?></a></td>
										<td><a class="name" target="_blank" href="https://dverim.bitrix24.ru/company/personal/user/<?=$app_upd_row['bitrix24_client_id'];?>/"><?=$app_upd_row['bitrix24_client_name'];?></a><br>от <b><?=d_format($app_upd_row['upd_date'], 'f3');?><b></td>
										<td><?=$app_upd_row['c_single'];?></td>
										<td><?=$app_upd_row['c_double'];?></td>
										<td><?=$app_upd_row['c_gates'];?></td>
										<td><?=$app_upd_row['c_hatches'];?></td>
										<td><?=$app_upd_row['c_transoms'];?></td>
										<td><?=$app_upd_row['c_others'];?></td>
										<td><?=$app_upd_row['c_summ'];?></td>
									</tr>
<?
}
} else {
?>
									<tr>
										<td colspan="9" align="center">обновлений бланка не было</td>
									</tr>
<?	
}
?>
									</tbody>
								</table>
									
								</div>
                            </section>
                            <!--label and badge end-->
							<!--label and badge start-->
<?
// -- проверяем, включен ли вход по запасному шлюзу авторизации
if ($CONFIG['yandex_oauth'] == 'disabled') {
?>
							<section class="panel">
                                <header class="panel-heading">
                                    Обновление бланка заказа
									<span class="tools pull-right">
										<a class="fa fa-chevron-down" href="javascript:;"></a>
									</span>
                                </header>
								
								
								
								
								
								<div class="panel-body">
								<b style="font-size: 11px; color: #EF0000;">
								<u>Инструкция:</u><br>
								1. Скачиваем актуальный бланк (синяя кнопка выше)<br>
								2. В скаченный бланк вносим необходимые изменения и сохраняем его<br>
								3. Вводим комментарий и прикрепляем обновленный бланк<br>
								4. Нажимаем кнопку "Обновить"<br><br>
								</b>
									<form action="action.inc.php" method="post" enctype="multipart/form-data" class="form-horizontal bucket-form">
									<input name="app_num_update" type="hidden" value="<?=$_GET['num'];?>">
									<input name="app_date_ready_update" type="hidden" value="<?=getOrderInfo($_GET['num'], 'date_ready');?>">
									<input name="app_old_blank" type="hidden" value="<?=getOrderInfo($_GET['num'], 'app_file_excel');?>">
										<div class="form-group">
											<label class="col-sm-3 control-label">Комментарий <br> <label style="font-size: 7pt;">обязательно напишите причину обновления бланка</label></label>
											<div class="col-sm-9">
												<textarea rows="6" name="manager_upd_comment" class="form-control"></textarea>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Бланк заказа</label>
											<div class="col-sm-9">
												<div class="fileupload fileupload-new" data-provides="fileupload"><input type="hidden">
														<span class="btn btn-default btn-file">
														<span class="fileupload-new"><i class="fa fa-cloud-upload"></i> загрузить бланк в формате MS Excel </span>
														<span class="fileupload-exists"><i class="fa fa-undo"></i> выбрать другой бланк заказа </span>
														<input type="file" name="app_file_upd" style="width: 300px;" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="default">
														</span>
													<span class="fileupload-preview" style="margin-left:0px; margin-top:3px; font-size: 8pt;"></span>
													<a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none; margin-left:5px;"></a>
												</div>
											</div>
										</div>
										<div class="form-group">
										<label class="col-sm-3 control-label"></label>
										<div class="col-sm-9">
											<button class="btn btn-success col-sm-12" name="app_update_btn" type="submit">Обновить</button>
										</div>
										</div>
									</form>
								</div>  
                            </section> <!-- disabled -->
<?
}
?>
                            <!--label and badge end-->
                        </div>
                    </div>

                </div>
            </div>
<?
} else {
//заявка не определена по номеру
?>
<div class="row">
			<div class="col-sm-12">
				<section class="panel">
					<header class="panel-heading">
						Информация о заказе
					</header>
					<div class="panel-body">	
	<br>
	<center>
	<h4>
	Упс... Что-то пошло не так :(
	<br><br>
	</h4>
	</center>
					</div>
				</section>
			</div>
		</div>	
	
<?
}
?>

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

//обработчики кнопок изменения статуса
// #F8F8F8 (для чата Bx24 - #000000) - app-gotobuilding - светло-серый (в производстве)	
// #ffffff (для чата Bx24 - #000000) - app-production - белый (передан на исполнение)	
// #e7f9e1 (для чата Bx24 - #008000) - app-ready - зелёный (готов)
// #f8d9ac (для чата Bx24 - #FFA500) - app-suspended - ораньжевый (приостановлен)
// #fce2dc (для чата Bx24 - #FF69B4) - app-stopped - розовый (отменён)
// #e8cff8 (для чата Bx24 - #800080) - app-shipped - фиолетовый (отгружен)

<?
// ДЛЯ АДМИНИСТРАТОРА\ПРОИЗВОДТВА\РУКОВОДСТВА
if (check_access('btn_app_status', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{
?>
//по правилу css({'color': '#36d900', 'cursor': 'pointer'});style="font-weight: bold"
//{"disabled": false, "style": false} {'color': '#bbbbbb', 'cursor': 'pointer'}
//кнопка готовности
$('button[name="app-ready"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_ready', 'num': btn_app_num, 'reason': reason},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr({"disabled": true, "style": "font-weight: bold; background-color: #fff; color: #000;"});
				$('button[name="app-gotobuilding"]').attr({"disabled": false, "style": false});
				$('button[name="app-production"]').attr({"disabled": false, "style": false});
				$('button[name="app-suspended"]').attr({"disabled": false, "style": false});
				$('button[name="app-stopped"]').attr({"disabled": false, "style": false});
				$('button[name="app-shipped"]').attr({"disabled": false, "style": false});
					
			}
		});	
	});

//кнопка запуска ПРОИЗВОДСТВОМ
$('button[name="app-gotobuilding"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','без комментария...');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_gotobuilding', 'num': btn_app_num, 'reason': reason, 'userid_app_start': <?=getOrderInfo($_GET['num'], 'bitrix24_client_id');?>},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr({"disabled": false, "style": false});
				$('button[name="app-gotobuilding"]').attr({"disabled": true, "style": "font-weight: bold; background-color: #fff; color: #000;"});
				$('button[name="app-production"]').attr({"disabled": false, "style": false});
				$('button[name="app-suspended"]').attr({"disabled": false, "style": false});
				$('button[name="app-stopped"]').attr({"disabled": false, "style": false});
				$('button[name="app-shipped"]').attr({"disabled": false, "style": false});			
			}
		});	
	});
	
//кнопка исполнения
$('button[name="app-production"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','');
	//if (reason == null || reason == '') {
	//} else {
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_production', 'num': btn_app_num, 'reason': reason},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr({"disabled": false, "style": false});
				$('button[name="app-gotobuilding"]').attr({"disabled": false, "style": false});
				$('button[name="app-production"]').attr({"disabled": true, "style": "font-weight: bold; background-color: #fff; color: #000;"});
				$('button[name="app-suspended"]').attr({"disabled": false, "style": false});
				$('button[name="app-stopped"]').attr({"disabled": false, "style": false});
				$('button[name="app-shipped"]').attr({"disabled": false, "style": false});			
			}
		});
	//}
	});
	
//кнопка приостановки заказа
$('button[name="app-suspended"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','');
	//if (reason == null || reason == '') {
	//} else {
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_suspended', 'num': btn_app_num, 'reason': reason, 'userid_app_start': <?=getOrderInfo($_GET['num'], 'bitrix24_client_id');?>},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr({"disabled": false, "style": false});
				$('button[name="app-gotobuilding"]').attr({"disabled": false, "style": false});
				$('button[name="app-production"]').attr({"disabled": false, "style": false});
				$('button[name="app-suspended"]').attr({"disabled": true, "style": "font-weight: bold; background-color: #fff; color: #000;"});
				$('button[name="app-stopped"]').attr({"disabled": false, "style": false});
				$('button[name="app-shipped"]').attr({"disabled": false, "style": false});			
			}
		});
	//}
	});
	
//кнопка отмены заказа
$('button[name="app-stopped"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','');
	//if (reason == null || reason == '') {
	//} else {
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_stopped', 'num': btn_app_num, 'reason': reason},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr({"disabled": false, "style": false});
				$('button[name="app-gotobuilding"]').attr({"disabled": false, "style": false});
				$('button[name="app-production"]').attr({"disabled": false, "style": false});
				$('button[name="app-suspended"]').attr({"disabled": false, "style": false});
				$('button[name="app-stopped"]').attr({"disabled": true, "style": "font-weight: bold; background-color: #fff; color: #000;"});
				$('button[name="app-shipped"]').attr({"disabled": false, "style": false});			
			}
		});
	//}
	});

//кнопка отгрузки заказа
$('button[name="app-shipped"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_shipped', 'num': btn_app_num, 'reason': reason, 'userid_app_start': <?=getOrderInfo($_GET['num'], 'bitrix24_client_id');?>},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr({"disabled": false, "style": false});
				$('button[name="app-gotobuilding"]').attr({"disabled": false, "style": false});
				$('button[name="app-production"]').attr({"disabled": false, "style": false});
				$('button[name="app-suspended"]').attr({"disabled": false, "style": false});
				$('button[name="app-stopped"]').attr({"disabled": false, "style": false});
				$('button[name="app-shipped"]').attr({"disabled": true, "style": "font-weight: bold; background-color: #fff; color: #000;"});			
			}
		});
	});
<?
	} else {	
?>
//для остальных
//кнопка готовности
$('button[name="app-ready"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = '';
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_ready', 'num': btn_app_num, 'reason': reason},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr('disabled', true);
				$('button[name="app-production"]').attr('disabled', true);
				$('button[name="app-suspended"]').attr('disabled', true);
				$('button[name="app-stopped"]').attr('disabled', true);
				$('button[name="app-shipped"]').attr('disabled', false);
				
				//$('tr[id="'+btn_app_num+'"]').removeClass();
				//$('tr[id="'+btn_app_num+'"]').addClass('app-ready');
				//$('span[id="'+btn_app_num+'"]').html('<?=$APP_STATUS['app-ready'];?>');			
			}
		});	
	});
	
//кнопка исполнения
$('button[name="app-production"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','');
	if (reason == null || reason == '') {
	} else {
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_production', 'num': btn_app_num, 'reason': reason},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr('disabled', false);
				$('button[name="app-production"]').attr('disabled', true);
				$('button[name="app-suspended"]').attr('disabled', false);
				$('button[name="app-stopped"]').attr('disabled', false);
				$('button[name="app-shipped"]').attr('disabled', true);			
			}
		});
	}
	});
	
//кнопка приостановки заказа
$('button[name="app-suspended"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','');
	//if (reason == null || reason == '') {
	//} else {
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_suspended', 'num': btn_app_num, 'reason': reason, 'userid_app_start': <?=getOrderInfo($_GET['num'], 'bitrix24_client_id');?>},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr('disabled', true);
				$('button[name="app-production"]').attr('disabled', false);
				$('button[name="app-suspended"]').attr('disabled', true);
				$('button[name="app-stopped"]').attr('disabled', false);
				$('button[name="app-shipped"]').attr('disabled', true);			
			}
		});
	//}
	});
	
//кнопка отмены заказа
$('button[name="app-stopped"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = prompt('Причина смены статуса заказа?','');
	//if (reason == null || reason == '') {
	//} else {
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_stopped', 'num': btn_app_num, 'reason': reason},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr('disabled', true);
				$('button[name="app-production"]').attr('disabled', false);
				$('button[name="app-suspended"]').attr('disabled', true);
				$('button[name="app-stopped"]').attr('disabled', true);
				$('button[name="app-shipped"]').attr('disabled', true);			
			}
		});
	//}
	});

//кнопка отгрузки заказа
$('button[name="app-shipped"]').click(function() {
	var btn_app_num = $(this).attr("value");
	var reason = '';
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'status_btn_shipped', 'num': btn_app_num, 'reason': reason, 'userid_app_start': <?=getOrderInfo($_GET['num'], 'bitrix24_client_id');?>},
			cache: false,			
		success: function(response){
				$('button[name="app-ready"]').attr('disabled', true);
				$('button[name="app-production"]').attr('disabled', true);
				$('button[name="app-suspended"]').attr('disabled', true);
				$('button[name="app-stopped"]').attr('disabled', true);
				$('button[name="app-shipped"]').attr('disabled', true);			
			}
		});
	});
<?
	}
?>

//Информация о заказе &nbsp;&nbsp; <i id="edit-btn-info-order" value="edit" class="fa fa-pencil-square-o" style="color: #f40000; cursor: pointer;" aria-hidden="true"></i> &nbsp; <i id="edit-btn-info-order" value="save" class="fa fa-floppy-o" style="color: #22c400; cursor: pointer;" aria-hidden="true"></i>


//РЕДАКТИРОВАНИЕ ДАННЫХ
$('i[id="edit-btn-info-order"]').click(function() {
	var btn_value = $(this).attr("value");
	var edit_kontragent_a_id = $('a[id="edit_kontragent_a"]').attr("value");
	var edit_kontragent_a_text = $('a[id="edit_kontragent_a"]').html();
	var edit_c_single = $('b[id="edit_c_single"]').html();
	var edit_c_double = $('b[id="edit_c_double"]').html();
	var edit_c_gates = $('b[id="edit_c_gates"]').html();
	var edit_c_hatches = $('b[id="edit_c_hatches"]').html();
	var edit_c_transoms = $('b[id="edit_c_transoms"]').html();
	var edit_c_others = $('b[id="edit_c_others"]').html();
	
	if (btn_value == 'edit') {
		$('i[id="edit-btn-info-order"]').attr({"value": "save", "class": "fa fa-floppy-o", "style": "color: #22c400; cursor: pointer;"});
		$('b[id="edit_c_single"]').html('<input id="edit_c_single_now" type="text" value="' + edit_c_single + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('b[id="edit_c_double"]').html('<input id="edit_c_double_now" type="text" value="' + edit_c_double + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('b[id="edit_c_gates"]').html('<input id="edit_c_gates_now" type="text" value="' + edit_c_gates + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('b[id="edit_c_hatches"]').html('<input id="edit_c_hatches_now" type="text" value="' + edit_c_hatches + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('b[id="edit_c_transoms"]').html('<input id="edit_c_transoms_now" type="text" value="' + edit_c_transoms + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('b[id="edit_c_others"]').html('<input id="edit_c_others_now" type="text" value="' + edit_c_others + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('a[id="edit_kontragent_a"]').attr({"style": "display: none;"});
		$('select[id="edit_kontragent_select"]').attr({"style": "display: block; border: 1px solid #ff0000;"});
		$('select option[value=' + edit_kontragent_a_id + ']').attr('selected', 'true').text(edit_kontragent_a_text);
		$('a[id="edit_bill_a"]').attr({"style": "display: none;"});
		$('div[id="edit_bill_div"]').attr({"style": "display: block;"});
	}
	
	var c_summ = parseInt($('input[id="edit_c_single_now"]').attr("value"))+parseInt($('input[id="edit_c_double_now"]').attr("value"))+parseInt($('input[id="edit_c_gates_now"]').attr("value"))+parseInt($('input[id="edit_c_hatches_now"]').attr("value"))+parseInt($('input[id="edit_c_transoms_now"]').attr("value"))+parseInt($('input[id="edit_c_others_now"]').attr("value"));

	if (btn_value == 'save') {
		$('b[id="edit_c_single"]').html($('input[id="edit_c_single_now"]').attr("value"));
		$('b[id="edit_c_double"]').html($('input[id="edit_c_double_now"]').attr("value"));
		$('b[id="edit_c_gates"]').html($('input[id="edit_c_gates_now"]').attr("value"));
		$('b[id="edit_c_hatches"]').html($('input[id="edit_c_hatches_now"]').attr("value"));
		$('b[id="edit_c_transoms"]').html($('input[id="edit_c_transoms_now"]').attr("value"));
		$('b[id="edit_c_others"]').html($('input[id="edit_c_others_now"]').attr("value"));
		$('u[id="edit_c_summ"]').html(c_summ);
		$('a[id="edit_kontragent_a"]').attr({"style": "display: block;"});
		$('a[id="edit_kontragent_a"]').attr({"style": "display: block;", "href": "https://dverim.bitrix24.ru/crm/company/show/" + $('select[id="edit_kontragent_select"]').find('option:selected').val() + "/"});
		$('a[id="edit_kontragent_a"]').html($('select[id="edit_kontragent_select"]').find('option:selected').text());
		$('select[id="edit_kontragent_select"]').attr({"style": "display: none; border: 1px solid #ff0000;"});
		$('a[id="edit_bill_a"]').attr({"style": "display: block;"});
		$('div[id="edit_bill_div"]').attr({"style": "display: none;"});
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'edit-btn-info-order', 'num': <?=$_GET['num'];?>, 'c_single': $('b[id="edit_c_single"]').html(), 'c_double': $('b[id="edit_c_double"]').html(), 'c_gates': $('b[id="edit_c_gates"]').html(), 'c_hatches': $('b[id="edit_c_hatches"]').html(), 'c_transoms': $('b[id="edit_c_transoms"]').html(), 'c_others': $('b[id="edit_c_others"]').html(), 'c_summ': c_summ, 'kontragent_id': $('select[id="edit_kontragent_select"]').find('option:selected').val(), 'kontragent_text': $('select[id="edit_kontragent_select"]').find('option:selected').text()},
			cache: false,			
		success: function(response){
					$('i[id="edit-btn-info-order"]').attr({"value": "load", "class": "fa fa-refresh fa-spin fa-fw", "style": "color: #d9d900;"});
				setTimeout(function(){
					$('i[id="edit-btn-info-order"]').attr({"value": "edit", "class": "fa fa-pencil-square-o", "style": "color: #f40000; cursor: pointer;"});
				}, 3000);
			}
		});
	}
	
	
	
});

/* 
	$('i[id="edit-btn-info-bill"]').click(function() {
	var btn_value = $(this).attr("value");
	var bill_number = $('link[id="bill_number"]').html();
	var bill_total = $('link[id="bill_total"]').html();
	var bill_transfer = $('link[id="bill_transfer"]').html();
	var bill_bulding = $('link[id="bill_bulding"]').html();
	var bill_bonus = $('link[id="bill_bonus"]').html();
	
	if (btn_value == 'edit') {
		$('i[id="edit-btn-info-bill"]').attr({"value": "save", "class": "fa fa-floppy-o", "style": "color: #22c400; cursor: pointer;"});
		$('link[id="bill_number"]').html('<input id="bill_number_now" type="text" value="' + bill_number + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('link[id="bill_total"]').html('<input id="bill_total_now" type="text" value="' + bill_total + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('link[id="bill_transfer"]').html('<input id="bill_transfer_now" type="text" value="' + bill_transfer + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('link[id="bill_bulding"]').html('<input id="bill_bulding_now" type="text" value="' + bill_bulding + '" class="form-control" style="border: 1px solid #ff0000;">');
		$('link[id="bill_bonus"]').html('<input id="bill_bonus_now" type="text" value="' + bill_bonus + '" class="form-control" style="border: 1px solid #ff0000;">');
	}
	
	if (btn_value == 'save') {
		$('i[id="edit-btn-info-bill"]').attr({"value": "edit", "class": "fa fa-pencil-square-o", "style": "color: #f40000; cursor: pointer;"});
		$('link[id="bill_number"]').html($('input[id="bill_number_now"]').attr("value"));
		$('link[id="bill_total"]').html($('input[id="bill_total_now"]').attr("value"));
		$('link[id="bill_transfer"]').html($('input[id="bill_transfer_now"]').attr("value"));
		$('link[id="bill_bulding"]').html($('input[id="bill_bulding_now"]').attr("value"));
		$('link[id="bill_bonus"]').html($('input[id="bill_bonus_now"]').attr("value"));
	}
	
	
	
	});
*/

	
//кнопка отметки оплаты по счету
$('button[name="postpay_bill"]').click(function() {
	var btn_app_num = $(this).attr("value");
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'btn': 'postpay_bill', 'num': btn_app_num, 'bill_id': <?=getOrderInfo($_GET['num'], 'bill_id');?>, 'bill_total': <?=getBillInfo($_GET['num'], 'bill_total');?>, 'bill_prepay': <?=getBillInfo($_GET['num'], 'bill_prepay');?>},
			cache: false,			
		success: function(response){
				$('button[name="postpay_bill"]').attr('disabled', true);					
			}
		});
	});

});
</script>