<?php
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = "АДМИНИСТРИРОВАНИЕ СИСТЕМЫ - ".$CONFIG['title_header'];

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
#####
					</center>
                </div>
            </div>
		<!-- конец панель проверки прав администратора -->	
			
        </div>
        <!-- page heading end-->


		
        <!--body wrapper start-->
	<div class="wrapper">
		<div class="row">  
			<div class="col-lg-6">
				<section class="panel">
					<header class="panel-heading">
						Основные настройки
						<span class="tools pull-right">
							<a href="javascript:;" class="fa fa-chevron-up"></a>
						</span>
						<!-- <i id="_waiting" class="fa fa-spinner fa-pulse fa fa-fw"></i> -->
					</header>
					<div class="panel-body" style="display: none;">
						<form class="form-horizontal adminex-form">
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>Заголовок</b> <br> </label>
								<div class="col-sm-9">
									<div class="input-group m-bot15">
									<input id="title_header_text" type="text" class="form-control" placeholder="">
										<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i id="title_header" style="color: #36d900; cursor: pointer;" class="fa fa-check"></i></button>
										</span>
									</div>
									<span class="help-block" style="font-size: 7pt;">основной title всех страниц</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>Домен</b></label>
								<div class="col-sm-9">
									<div class="input-group m-bot15">
									<input id="hostname_text" type="text" class="form-control" placeholder="">
										<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i id="hostname" style="color: #36d900; cursor: pointer;" class="fa fa-check"></i></button>
										</span>
									</div>
									<span class="help-block" style="font-size: 7pt;">основной домен системы (полный адрес)</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>E-mail</b></label>
								<div class="col-sm-9">
									<div class="input-group m-bot15">
									<input id="mail_text" type="text" class="form-control" placeholder="">
										<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i id="mail" style="color: #36d900; cursor: pointer;" class="fa fa-check"></i></button>
										</span>
									</div>
									<span class="help-block" style="font-size: 7pt;">.....</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>Город</b></label>
								<div class="col-sm-9">
									<div class="input-group m-bot15">
									<input id="city_text" type="text" class="form-control" placeholder="">
										<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i id="city" style="color: #36d900; cursor: pointer;" class="fa fa-check"></i></button>
										</span>
									</div>
									<span class="help-block" style="font-size: 7pt;">отображается в подвале страницы входа в систему</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>Название системы</b></label>
								<div class="col-sm-9">
									<div class="input-group m-bot15">
									<input id="system_name_text" type="text" class="form-control" placeholder="">
										<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i id="system_name" style="color: #36d900; cursor: pointer;" class="fa fa-check"></i></button>
										</span>
									</div>
									<span class="help-block" style="font-size: 7pt;">отображается в подвале страницы входа в систему</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>Год</b></label>
								<div class="col-sm-9">
									<div class="input-group m-bot15">
									<input id="year_text" type="text" class="form-control" placeholder="">
										<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i id="year" style="color: #36d900; cursor: pointer;" class="fa fa-check"></i></button>
										</span>
									</div>
									<span class="help-block" style="font-size: 7pt;">отображается в подвале страницы входа в систему</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>Домен Битрикс24</b></label>
								<div class="col-sm-9">
									<div class="input-group m-bot15">
									<input id="url_bitrix24_text" type="text" class="form-control" placeholder="">
										<span class="input-group-btn">
										<button class="btn btn-default" type="button"><i id="url_bitrix24" style="color: #36d900; cursor: pointer;" class="fa fa-check"></i></button>
										</span>
									</div>
									<span class="help-block" style="font-size: 7pt;">основной домен корп. портала (без http и слешей)</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>Резервный вход</b></label>
								<div class="col-sm-9">
									<div class="radio">
										<label>
											<input type="radio" name="optionsRadios" id="yandex_oauth_1" value="enabled">
											Включить запасной шлюз авторизации
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="optionsRadios" id="yandex_oauth_0" value="disabled" checked>
											Выключить запасной шлюз авторизации
										</label>
									</div>
									<span class="help-block" style="font-size: 7pt;">через сервер Yandex.API</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 col-sm-3 control-label"><b>Закрыть на работы</b></label>
								<div class="col-sm-9">
									<div class="input-group m-bot15">
										<span class="input-group-addon">
											<input type="checkbox" id="its_work">
										</span>
										<input id="its_work_date" type="text" class="form-control" placeholder="дата окончания работ '2000-01-31 00:00:00'">
									</div>
									<span class="help-block" style="font-size: 7pt;">закрыть доступ к системе /offline/offline.php</span>
								</div>
							</div>
						</form>
					</div>
				</section>
			</div>
		</div>	
		
		
		<div class="row">        
			<div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Правила доступа к элементам системы
                            <span class="tools pull-right">
                                <a href="javascript:;" class="fa fa-chevron-up"></a>
                             </span>
                    </header>
                    <div class="panel-body" style="display: none;">
<?
$stmt_crm_access = $dbConnection->query('SELECT * FROM crm_access ORDER BY id ASC');
$crm_access = $stmt_crm_access->fetchAll();
?>
						<table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th style="font-size: 10px;">Метка</th>
                                <th style="font-size: 10px;">Комментарий</th>
                                <th style="font-size: 8px;">Андмин</th>
								<th style="font-size: 8px;">N\A</th>
								<th style="font-size: 8px;">Директор</th>
								<th style="font-size: 8px;">Дир. по развитию</th>
								<th style="font-size: 8px;">Бухгалтер</th>
								<th style="font-size: 8px;">Нач. произ-ва</th>
								<th style="font-size: 8px;">Инж-констр</th>
								<th style="font-size: 8px;">Нач. ОП</th>
								<th style="font-size: 8px;">Мен. ОП</th>
								<th style="font-size: 8px;">Мен. ОП по прозвону</th>
								<th style="font-size: 8px;">Мен. ОП с расширенным доступом</th>
                            </tr>
                            </thead>
<?
// функция проверки прав доступа
function access_check($row, $id) {
	if ($row == 1) {
		$access = $id.'" value="'.$id.'" name="toggle-on" style="color: #36d900; cursor: pointer;" class="fa fa-toggle-on"></i>';
	} else {
		$access = $id.'" value="'.$id.'" name="toggle-off" style="color: #bbbbbb; cursor: pointer;" class="fa fa-toggle-off"></i>';
	}
return $access;
}

foreach($crm_access as $crm_access_row) {
?>

                            <tr>
                                <td><b><?=$crm_access_row['id'];?></b></td>
								<td style="font-size: 11px;"><i style="border-bottom: 1px dashed #000000;"><?=$crm_access_row['item_name'];?></i></td>
								<td style="font-size: 11px;" id="comment_<?=$crm_access_row['id'];?>" value="<?=$crm_access_row['id'];?>"><?=$crm_access_row['comment'];?></td>
								<td><i id="fullacc_<?=access_check($crm_access_row['fullacc'], $crm_access_row['id']);?></td>
								<td><i id="unacc_<?=access_check($crm_access_row['unacc'], $crm_access_row['id']);?></td>
								<td><i id="director_<?=access_check($crm_access_row['director'], $crm_access_row['id']);?></td>
								<td><i id="director_dev_<?=access_check($crm_access_row['director_dev'], $crm_access_row['id']);?></td>
								<td><i id="accountant_<?=access_check($crm_access_row['accountant'], $crm_access_row['id']);?></td>
								<td><i id="factory_head_<?=access_check($crm_access_row['factory_head'], $crm_access_row['id']);?></td>
								<td><i id="factory_ec_<?=access_check($crm_access_row['factory_ec'], $crm_access_row['id']);?></td>
								<td><i id="salesteam_head_<?=access_check($crm_access_row['salesteam_head'], $crm_access_row['id']);?></td>
								<td><i id="salesteam_manager_<?=access_check($crm_access_row['salesteam_manager'], $crm_access_row['id']);?></td>
								<td><i id="salesteam_manager_the_call_<?=access_check($crm_access_row['salesteam_manager_the_call'], $crm_access_row['id']);?></td>
								<td><i id="salesteam_manager_extended_<?=access_check($crm_access_row['salesteam_manager_extended'], $crm_access_row['id']);?></td>
                            </tr>
<?
}
?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
  
			
			<div class="col-sm-12">
				<section class="panel">
					<header class="panel-heading">
						----------
					</header>
					<div class="panel-body">
					
	-------
					
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

//обработчики прав доступа
$('i[id^="fullacc_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'fullacc', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="fullacc_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="fullacc_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="fullacc_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="fullacc_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="fullacc_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="fullacc_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});
	
$('i[id^="unacc_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'unacc', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="unacc_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="unacc_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="unacc_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="unacc_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="unacc_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="unacc_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});

$('i[id^="director_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'director', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="director_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="director_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="director_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="director_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="director_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="director_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});
	
$('i[id^="director_dev_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'director_dev', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="director_dev_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="director_dev_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="director_dev_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="director_dev_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="director_dev_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="director_dev_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});
	
$('i[id^="accountant_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'accountant', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="accountant_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="accountant_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="accountant_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="accountant_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="accountant_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="accountant_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});

$('i[id^="factory_head_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'factory_head', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="factory_head_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="factory_head_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="factory_head_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="factory_head_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="factory_head_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="factory_head_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});

$('i[id^="factory_ec_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'factory_ec', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="factory_ec_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="factory_ec_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="factory_ec_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="factory_ec_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="factory_ec_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="factory_ec_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});
	
$('i[id^="salesteam_manager_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'salesteam_manager', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="salesteam_manager_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="salesteam_manager_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="salesteam_manager_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="salesteam_manager_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="salesteam_manager_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="salesteam_manager_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});

$('i[id^="salesteam_manager_the_call_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'salesteam_manager_the_call', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="salesteam_manager_the_call_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="salesteam_manager_the_call_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="salesteam_manager_the_call_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="salesteam_manager_the_call_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="salesteam_manager_the_call_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="salesteam_manager_the_call_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});	

$('i[id^="salesteam_manager_extended_"]').click(function() {
	var id = $('#'+this.id).attr('value');
	var name = $('#'+this.id).attr('name');
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'access': 'salesteam_manager_extended', 'id': id, 'crm_access': name},
			cache: false,			
		success: function(response){
				if (name == 'toggle-off') {
					$('i[id="salesteam_manager_extended_'+id+'"]').attr('class', 'fa fa-toggle-on');
					$('i[id="salesteam_manager_extended_'+id+'"]').css({'color': '#36d900', 'cursor': 'pointer'});
					$('i[id="salesteam_manager_extended_'+id+'"]').attr('name', 'toggle-on');
					
				}
				if (name == 'toggle-on') {
					$('i[id="salesteam_manager_extended_'+id+'"]').attr('class', 'fa fa-toggle-off');
					$('i[id="salesteam_manager_extended_'+id+'"]').css({'color': '#bbbbbb', 'cursor': 'pointer'});
					$('i[id="salesteam_manager_extended_'+id+'"]').attr('name', 'toggle-off');
				}		
			}
		});	
	});	

});

</script>