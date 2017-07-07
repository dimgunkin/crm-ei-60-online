<?php
//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = "Крнтакты сотрудников - ".$CONFIG['title_header'];

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
            <div class="directory-info-row">
                <div class="row">
				
				
<?
// Делаем запрос подставляя значения переменных $quantity и $list
$stmt_emp_company = $dbConnection->query('SELECT * from crm_employees WHERE status = "1" ORDER BY id ASC');
// Считаем количество полученных записей
$emp_company = $stmt_emp_company->fetchAll();

foreach($emp_company as $emp_company_row) {

if($emp_company_row['work_position'] == '') { $work_position = ''; } else { $work_position = ' - '.$emp_company_row['work_position']; }
if($emp_company_row['personal_city'] == '') { $personal_city = 'нет данных...'; } else { $personal_city = $emp_company_row['personal_city']; }
if($emp_company_row['personal_birthday'] == '0000-00-00') { $personal_birthday = 'нет данных...'; } else { $personal_birthday = d_format($emp_company_row['personal_birthday'], 'f1'); }

if($emp_company_row['work_phone'] == '') {
	$work_phone = '';
} else {
	$work_phone = '<i class="fa fa-phone-square"></i> <abbr title="Рабочий">'.$emp_company_row["work_phone"].'</abbr>';
}

if($emp_company_row['personal_mobile'] == '') {
	$personal_mobile = '';
} else {
	$personal_mobile = '<i class="fa fa-phone-square"></i> <abbr title="Личный">'.$emp_company_row["personal_mobile"].'</abbr>';
}

?>

                    <div class="col-md-6 col-sm-6">
                        <div class="panel">
                            <div class="panel-body">
                                <h4><?=$emp_company_row['last_name'].' '.$emp_company_row['name'];?> <span class="text-muted small"><?=$work_position;?></span></h4>
                                <div class="media">
                                    <a class="pull-left" target="_blank" href="https://dverim.bitrix24.ru/company/personal/user/<?=$emp_company_row['bitrix24_id'];?>/">
                                        <img class="thumb media-object" src="images/no_photo.png" width="103" height="103" alt="">
                                    </a>
                                    <div class="media-body">
                                        <address>
                                            <strong>ООО "Двери металл-М"</strong><br>
											<i class="fa fa-globe"></i> <?=$personal_city;?><br>
											<i class="fa fa-birthday-cake"></i> <?=$personal_birthday;?><br><br>
											
                                            <i class="fa fa-envelope"></i> <?=$emp_company_row['email'];?><br>
											<?=$work_phone;?>  <?//=$personal_mobile;?>
                                            
                                        </address>
                                        <ul class="social-links">
                                            <li><a title="" target="_blank" data-placement="top" data-toggle="tooltip" class="tooltips" href="https://dverim.bitrix24.ru/company/personal/user/<?=$emp_company_row['bitrix24_id'];?>/" data-original-title="Битрикс 24"><i class="fa fa-user-o"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

<?
}
?>
					
					
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