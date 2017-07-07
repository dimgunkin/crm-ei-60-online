<?php
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = "Продажи - ".$CONFIG['title_header'];

//подключаем верстку
include("head.inc.php"); //шапка сайта
include("navbarleft.inc.php"); //боковое меню навигации
include("navbartop.inc.php"); //верхнее меню навигации

//Название месяца (сущ.)
$_monthsNameList = array(
"1"=>"ЯНВАРЬ","2"=>"ФЕВРАЛЬ","3"=>"МАРТ",
"4"=>"АПРЕЛЬ","5"=>"МАЙ", "6"=>"ИЮНЬ",
"7"=>"ИЮЛЬ","8"=>"АВГУСТ","9"=>"СЕНТЯБРЬ",
"10"=>"ОКТЯБРЬ","11"=>"НОЯБРЬ","12"=>"ДЕКАБРЬ"
);
$_month = $_monthsNameList[date("n")].' '.date("Y"); /* имя тесущего месяца и год */
		
?>	
<!-- начало тела станицы -->

<?

		


?>	

        <!-- page heading end-->

		

        <!--body wrapper start-->
	<div class="wrapper">
		<div class="row">

<!-- СТАТИСТИКА ПО ПРОДАЖАМ -->			
			<div class="col-md-8">
				<section class="panel">
					<header class="panel-heading">
						Статистика по продажам <?=$_month;?>
					</header>
					<div class="panel-body">
					<br>
					<table class="table table-hover">
                            <thead>
                            <tr>
								<th style="border-bottom: 1px solid #000000; text-align: center;"></th>
								<th style="border-bottom: 1px solid #000000; text-align: center; width: 140px;">Руководитель</th>
								<th style="border: 1px solid #000000; text-align: center; width: 45px;">1</th>
								<th style="border: 1px solid #000000; text-align: center; width: 45px;">2</th>
								<th style="border: 1px solid #000000; text-align: center; width: 45px;">В</th>
								<th style="border: 1px solid #000000; text-align: center; width: 45px;">Л</th>
								<th style="border: 1px solid #000000; text-align: center; width: 45px;">Ф</th>
								<th style="border: 1px solid #000000; text-align: center; width: 45px;">Ъ</th>
								<th style="background: #f4f4f4; border: 1px solid #000000; text-align: center; width: 70px;">ИТОГО</th>
                            </tr>
                            </thead>
<?
// сумма дверей у руководителя
$sum_head_c_single = 0;
$sum_head_c_double = 0;
$sum_head_c_gates = 0;
$sum_head_c_hatches = 0;
$sum_head_c_transoms = 0;
$sum_head_c_others = 0;
$sum_head_c_summ = 0;

// сумма дверей у менеджера
$sum_manager_c_single = 0;
$sum_manager_c_double = 0;
$sum_manager_c_gates = 0;
$sum_manager_c_hatches = 0;
$sum_manager_c_transoms = 0;
$sum_manager_c_others = 0;
$sum_manager_c_summ = 0;

// сумма общего кол-ва дверей по всем отделам
$sum_c_single = 0;
$sum_c_double = 0;
$sum_c_gates = 0;
$sum_c_hatches = 0;
$sum_c_transoms = 0;
$sum_c_others = 0;
$sum_c_summ = 0;

// Заготовка запроса с внешними переменными
$stmt_departmentlist = $dbConnection->query('SELECT * from crm_department_bx24 WHERE sales = "1" ORDER BY id ASC');
$departmentlist = $stmt_departmentlist->fetchAll();

foreach($departmentlist as $departmentlist_row) {

//имя руководителя (ф и.)
$name_head_short = getUserInfo_BX24ID($departmentlist_row["uf_head_dep"], 'last_name').' '.substr(getUserInfo_BX24ID($departmentlist_row["uf_head_dep"], 'name'), 0, 2).'.';
$bx24_id_head = $departmentlist_row["uf_head_dep"];


// получаем все заявки руководителя (за месяц) -------------------------------------------------------------------------
$stmt_sum_head = $dbConnection->query('SELECT * from crm_applist WHERE MONTH(`date_start`) = MONTH(NOW()) AND bitrix24_client_id = "'.$departmentlist_row["uf_head_dep"].'" AND status != "app-stopped"');
$sum_head = $stmt_sum_head->fetchAll();

// считаем сумму дверей по всем заявкам руководителя (с разделением на тип двери)
foreach($sum_head as $sum_head_row) {
$sum_head_c_single = $sum_head_c_single + $sum_head_row["c_single"];
$sum_head_c_double = $sum_head_c_double + $sum_head_row["c_double"];
$sum_head_c_gates = $sum_head_c_gates + $sum_head_row["c_gates"];
$sum_head_c_hatches = $sum_head_c_hatches + $sum_head_row["c_hatches"];
$sum_head_c_transoms = $sum_head_c_transoms + $sum_head_row["c_transoms"];
$sum_head_c_others = $sum_head_c_others + $sum_head_row["c_others"];
$sum_head_c_summ = $sum_head_c_summ + $sum_head_row["c_summ"];
}
// получаем все заявки руководителя (за месяц) /конец/ ------------------------------------------------------------------

// руководители, которые не учавствуют в подсчете менеджерских сумм дверей
//Гехтман Алексей, Прошина Виктория, Анциферова Наталья
$head_dep_ids = '8, 20, 24';

//echo '<br>'.var_export($head_dep_ids).'<br>';

 


// -- получаем массив с bx24_id всех менеджеров отдела и просчитываем сумму дверей всех менеджеров отдела (за месяц) -- 
if ($departmentlist_row["uf_head_dep"] == 8) {
	$id_dep = 22;
} else {
	$id_dep = $departmentlist_row["id_dep"];
}
	
$stmt_bx24id_dep = $dbConnection->query('SELECT * from crm_employees WHERE bitrix24_department_id = "'.$id_dep.'" AND bitrix24_id NOT IN ('.$head_dep_ids.')');
$bx24id_dep = $stmt_bx24id_dep->fetchAll();
$bx24id_dep_count = $stmt_bx24id_dep->rowCount();

if ($bx24id_dep_count == 0) {
	
} else {
	foreach($bx24id_dep as $bx24id_dep_row) {

		// получаем все заявки менеджера отдела (за месяц) -------------------------------------------------------------------------
		$stmt_sum_manager = $dbConnection->query('SELECT * from crm_applist WHERE MONTH(`date_start`) = MONTH(NOW()) AND bitrix24_client_id = "'.$bx24id_dep_row["bitrix24_id"].'" AND status != "app-stopped"');
		$sum_manager = $stmt_sum_manager->fetchAll();

		// считаем сумму дверей по всем заявкам менеджера отдела (с разделением на тип двери)
		foreach($sum_manager as $sum_manager_row) {
		$sum_manager_c_single = $sum_manager_c_single + $sum_manager_row["c_single"];
		$sum_manager_c_double = $sum_manager_c_double + $sum_manager_row["c_double"];
		$sum_manager_c_gates = $sum_manager_c_gates + $sum_manager_row["c_gates"];
		$sum_manager_c_hatches = $sum_manager_c_hatches + $sum_manager_row["c_hatches"];
		$sum_manager_c_transoms = $sum_manager_c_transoms + $sum_manager_row["c_transoms"];
		$sum_manager_c_others = $sum_manager_c_others + $sum_manager_row["c_others"];
		$sum_manager_c_summ = $sum_manager_c_summ + $sum_manager_row["c_summ"];
		}
		// получаем все заявки менеджеров отдела (за месяц) /конец/ -----------------------------------------------------------------

	}
}
// --  --



?>
                            <tr> <!-- по каждому отделу -->
                                <td><?=$departmentlist_row["name_dep"];?></td>
								<td style="text-align: center;"><a target="_blank" href="https://dverim.bitrix24.ru/company/personal/user/<?=$bx24_id_head;?>/"><?=$name_head_short;?></a></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$sum_head_c_single + $sum_manager_c_single;?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$sum_head_c_double + $sum_manager_c_double;?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$sum_head_c_gates + $sum_manager_c_gates;?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$sum_head_c_hatches + $sum_manager_c_hatches;?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$sum_head_c_transoms + $sum_manager_c_transoms;?></td>
								<td style="border-left: 1px solid #000000; text-align: center;"><?=$sum_head_c_others + $sum_manager_c_others;?></td>
								<td style="background: #f4f4f4; border-left: 1px solid #000000; border-right: 1px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_head_c_summ + $sum_manager_c_summ;?></b></td>
                            </tr>

<?
// суммируем общее кол-во дверей по всем отделам
$sum_c_single = $sum_c_single + $sum_head_c_single + $sum_manager_c_single;
$sum_c_double = $sum_c_double + $sum_head_c_double + $sum_manager_c_double;
$sum_c_gates = $sum_c_gates + $sum_head_c_gates + $sum_manager_c_gates;
$sum_c_hatches = $sum_c_hatches + $sum_head_c_hatches + $sum_manager_c_hatches;
$sum_c_transoms = $sum_c_transoms + $sum_head_c_transoms + $sum_manager_c_transoms;
$sum_c_others = $sum_c_others + $sum_head_c_others + $sum_manager_c_others;
$sum_c_summ = $sum_c_summ + $sum_head_c_summ + $sum_manager_c_summ;
// обнуляем сумму дверей у руководителя
$sum_head_c_single = 0;
$sum_head_c_double = 0;
$sum_head_c_gates = 0;
$sum_head_c_hatches = 0;
$sum_head_c_transoms = 0;
$sum_head_c_others = 0;
$sum_head_c_summ = 0;
// обнуляем сумму дверей у менеджера
$sum_manager_c_single = 0;
$sum_manager_c_double = 0;
$sum_manager_c_gates = 0;
$sum_manager_c_hatches = 0;
$sum_manager_c_transoms = 0;
$sum_manager_c_others = 0;
$sum_manager_c_summ = 0;
}
?>
                            <tr> <!-- разрыв -->
                                <td colspan="9" style="border-top: 1px solid #000000; text-align: center;" onmouseover="this.style.backgroundColor='#ffffff';" onmouseout="this.style.backgroundColor='#ffffff';"><br></td>
							</tr>
							
                            <tr> <!-- сумма итого -->
                                <td colspan="2" style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b>ИТОГО</b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_single;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_double;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_gates;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_hatches;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_transoms;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_others;?></b></td>
								<td style="background: #f4f4f4; border: 2px solid #000000; text-align: center; font-size: 16px;"><b><?=$sum_c_summ;?></b></td>
                            </tr>
                            </tbody>
                        </table>
					<div class="text-center" style="font-size: 9px;">последнее обновление данных <?=d_format(date("Y-m-d H:i:s"), 'f3');?></div>
					</div>
				</section>			
			</div>
<!-- СТАТИСТИКА ПО ПРОДАЖАМ /конец/ -->	
		
		
<!-- РЕЙТИНГ МЕНЕДЖЕРОВ -->
			<div class="col-md-4">
				<div class="panel">
					<header class="panel-heading">
						Статистика по менеджерам<br><?=$_month;?>
					</header>
					<div class="panel-body">
						<ul class="goal-progress">
<?
// кого учитывать в рейтинге
$employees_sale_ids = '"director_dev", "salesteam_head", "salesteam_manager", "salesteam_manager_extended"';

// сумма дверей каждого сотрудника (за все заявки текущего месяца)
$sum_c_summ_rate = 0;
		
// получаем список участников рейтинга
$stmt_employees_sale = $dbConnection->query('SELECT * from crm_employees WHERE work_position_access IN ('.$employees_sale_ids.')');
$employees_sale = $stmt_employees_sale->fetchAll();
$employees_sale_count = $stmt_employees_sale->rowCount();


foreach($employees_sale as $employees_sale_row) {

	// считаем сумму дверей (заявки за текущий месяц)
	$stmt_sum_employees_sale = $dbConnection->query('SELECT * from crm_applist WHERE MONTH(`date_start`) = MONTH(NOW()) AND bitrix24_client_id = "'.$employees_sale_row["bitrix24_id"].'" AND status != "app-stopped"');
	$sum_employees_sale = $stmt_sum_employees_sale->fetchAll();
	$sum_employees_sale_count = $stmt_sum_employees_sale->rowCount();
	
	foreach($sum_employees_sale as $sum_employees_sale_row) {
		$sum_c_summ_rate = $sum_c_summ_rate + $sum_employees_sale_row["c_summ"];
	}
	
	// если у сотрудника нет запущенных заказов
	if ($sum_employees_sale_count == 0) {
		$summ_persent = 15;
	} else {
		$summ_persent = (($sum_c_summ_rate/$sum_c_summ)*100)+20;
	}
	
/////// --------- СОРТИРОВКА !!!!!!!! ---------------
/////// --------- СОРТИРОВКА !!!!!!!! ---------------
/////// --------- СОРТИРОВКА !!!!!!!! ---------------
/////// --------- СОРТИРОВКА !!!!!!!! ---------------

?>
							<li>
								<div class="prog-avatar">
									<img src="images/no_photo.png" alt="" />
								</div>
								<div class="details">
									<div class="title">
										<a target="_blank" href="https://dverim.bitrix24.ru/company/personal/user/<?=$employees_sale_row["bitrix24_id"];?>/"><?=$employees_sale_row["last_name"].' '.$employees_sale_row["name"];?></a> <sup><u>заказы <?=$sum_employees_sale_count;?> шт</u></sup>
									</div>
									<div class="progress progress-xs">
										<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: <?=$summ_persent;?>%">
											<span class=""><?=$sum_c_summ_rate;?> шт</span>
										</div>
									</div>
								</div>
							</li>
<?
// обнуляем сумму дверей каждого сотрудника (за все заявки текущего месяца)
$sum_c_summ_rate = 0;
}
?>
						</ul>
						<div class="text-center" style="font-size: 9px;">последнее обновление данных <?=d_format(date("Y-m-d H:i:s"), 'f3');?></div>
					</div>
				</div>
			</div>
<!-- РЕЙТИНГ МЕНЕДЖЕРОВ /конец/ -->

		
		
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