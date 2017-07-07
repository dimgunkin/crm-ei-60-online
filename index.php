<?
//подключаем файл конфигурации системы
include_once("functions.inc.php");

//подключаем настройки авторизации через портал bitrix24
require("oauthbx24/include/config.php");

// --- РЕЖИМ ТЕХНИЧЕСКИХ РАБОТ ---
if ($CONFIG['its_work'] == 0) {

$error = "";
$GLOBALS["b"] = "8";

// clear auth session
if(isset($_REQUEST["clear"]) || $_SERVER["REQUEST_METHOD"] == "POST")
{
	unset($_SESSION["query_data"]);
	unset($_SESSION["auth_email"]);
	unset($_SESSION["auth_status"]);
	unset($_SESSION["auth_id"]);
	unset($_SESSION);
	unset($query_data);
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{
/******************* get code *************************************/
	if(!empty($_POST["portal"]))
	{
		$domain = $_POST["portal"];
		$params = array(
			"response_type" => "code",
			"client_id" => CLIENT_ID,
			"redirect_uri" => REDIRECT_URI,
		);
		$path = "/oauth/authorize/";

		redirect(PROTOCOL."://".$domain.$path."?".http_build_query($params));
	}
/******************** /get code ***********************************/
}

if(isset($_REQUEST["code"]))
{
/****************** get access_token ******************************/
	$code = $_REQUEST["code"];
	$domain = $_REQUEST["domain"];
	$member_id = $_REQUEST["member_id"];

	$params = array(
		"grant_type" => "authorization_code",
		"client_id" => CLIENT_ID,
		"client_secret" => CLIENT_SECRET,
		"redirect_uri" => REDIRECT_URI,
		"scope" => SCOPE,
		"code" => $code,
	);
	$path = "/oauth/token/";

	$query_data = query("GET", PROTOCOL."://".$domain.$path, $params);
	
	//если токен получен верно, проверяем пользователя
	//если почта присутствует в списке, обновляем данные пользователя, которые мы получили из Битрикс24
	//если почта отсутствует, сбрасываем данные авторизации и выводим пользователю сообщение о том, что ему запрещён доступ
	if(isset($query_data["access_token"]))
	{
		$_SESSION["query_data"] = $query_data; //заносим данные авторизации в сессию для их дальнейшего использования
		$_SESSION["query_data"]["ts"] = time(); //заносим время авторизации в сессию (время жизни токена 3600 секунд)
		
		//получаем массив с информацией об авторизованном пользователе
		$user_info = call($_SESSION["query_data"]["domain"], "user.current", array(
				"auth" => $_SESSION["query_data"]["access_token"])
			);
		$_SESSION["auth_email"] = $user_info["result"]["EMAIL"]; //заносим авторизованный EMAIL в сессию	
		
		//проверяем есть ли в базе пользователь с аналогичным email как в Битрикс24 + разрешён ли ему вход
		$stmt = $dbConnection->prepare('SELECT * FROM crm_employees WHERE email=:auth_email');
		$stmt->execute(array(':auth_email' => substr($_SESSION["auth_email"], 0)));
		$stmt_fetch = $stmt -> fetch(PDO::FETCH_ASSOC);
		$stmt_count = $stmt -> rowCount();
		//если условия входа соблюдены, обновляем информацию о пользователе (информация о пользователе выгружается из Битрикс24)
 		if($stmt_count == 1 && $stmt_fetch['status'] == 1) {
			$stmt = $dbConnection->prepare('UPDATE crm_employees SET								
									bitrix24_id=:ID,
									bitrix24_department_id=:UF_DEPARTMENT,
									name=:NAME,
									last_name=:LAST_NAME,
									second_name=:SECOND_NAME,
									personal_gender=:PERSONAL_GENDER,
									work_position=:WORK_POSITION,
									personal_birthday=:PERSONAL_BIRTHDAY,
									personal_city=:PERSONAL_CITY,
									personal_mobile=:PERSONAL_MOBILE,
									work_phone=:WORK_PHONE,
									uf_skype=:UF_SKYPE,
									personal_photo=:PERSONAL_PHOTO
			WHERE email=:auth_email');
			$stmt -> execute(array(':ID' => $user_info["result"]["ID"],
									':UF_DEPARTMENT' => $user_info["result"]["UF_DEPARTMENT"][0],
									':NAME' => $user_info["result"]["NAME"],
									':LAST_NAME' => $user_info["result"]["LAST_NAME"],
									':SECOND_NAME' => $user_info["result"]["SECOND_NAME"],
									':PERSONAL_GENDER' => $user_info["result"]["PERSONAL_GENDER"],
									':WORK_POSITION' => $user_info["result"]["WORK_POSITION"],
									':PERSONAL_BIRTHDAY' => $user_info["result"]["PERSONAL_BIRTHDAY"],
									':PERSONAL_CITY' => $user_info["result"]["PERSONAL_CITY"],
									':PERSONAL_MOBILE' => $user_info["result"]["PERSONAL_MOBILE"],
									':WORK_PHONE' => $user_info["result"]["WORK_PHONE"],
									':UF_SKYPE' => $user_info["result"]["UF_SKYPE"],
									':PERSONAL_PHOTO' => $user_info["result"]["PERSONAL_PHOTO"],
									':auth_email' => $user_info["result"]["EMAIL"]
			));
			
			//после обновления информации заносим в сессию некоротые переменные, которыми будем пользоваться в дальнейшем
			$_SESSION["auth_status"] = 1; //успешный статус авторизации и обновления данных
			$_SESSION["auth_id"] = $stmt_fetch['id']; //id пользователя в системе
		} 
		
		if($stmt_count == 0 || $stmt_fetch['status'] == 0) {
			//если пользователь незарегистрирован\отключен(status=0) в базе системы, то обнуляем все данные и не пускаем его
			unset($_SESSION["query_data"]);
			unset($_SESSION["auth_email"]);
			unset($_SESSION["auth_status"]);
			unset($_SESSION["auth_id"]);
			unset($query_data);
			//die();
			header('Location: index.php');
		}		

	}
	else
	{
		$error = "Произошла ошибка авторизации! ".print_r($query_data, 1);
	}
/********************** /get access_token *************************/
}
elseif(isset($_REQUEST["refresh"]))
{
/******************** refresh auth ********************************/
	$params = array(
		"grant_type" => "refresh_token",
		"client_id" => CLIENT_ID,
		"client_secret" => CLIENT_SECRET,
		"redirect_uri" => REDIRECT_URI,
		"scope" => SCOPE,
		"refresh_token" => $_SESSION["query_data"]["refresh_token"],
	);

	$path = "/oauth/token/";

	$query_data = query("GET", PROTOCOL."://".$_SESSION["query_data"]["domain"].$path, $params);

	if(isset($query_data["access_token"]))
	{
		$_SESSION["query_data"] = $query_data;
		$_SESSION["query_data"]["ts"] = time();

		//redirect($_GET['redirect']);
		//echo 'token_refresh_ok';
		die();
	}
	else
	{
		$error = "Произошла ошибка авторизации! ".print_r($query_data);
	}
/********************* /refresh auth ******************************/
}

//require_once(dirname(__FILE__)."/oauthbx24/include/header.php");

if(!isset($_SESSION["query_data"]))
{
/******************************************************************/
	if($error)
	{
		echo '<b>'.$error.'</b>';

	}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="favicondmmcrm.png" type="image/png">

    <title><?=$CONFIG['title_header'];?></title>

    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet">

</head>

<body class="login-body">

<div class="container">

    <form class="form-signin" action="" method="post">
        <div class="form-signin-heading text-center">
            <br><img src="images/login-logo.png" alt=""/>
        </div>
        <div class="login-wrap">
			<input type="hidden" name="portal" value="<?=$CONFIG['url_bitrix24'];?>">
<?
// -- проверяем, включен ли вход по запасному шлюзу авторизации
if ($CONFIG['yandex_oauth'] == 'enabled') {
	$btn_auth_bx42 = 'disabled';
} else {
	$btn_auth_bx42 = '';
}
?>
            <button <?=$btn_auth_bx42;?> class="btn btn-lg btn-login btn-block" type="submit">
                <!--  <i class="fa fa-paper-plane fa-1x fa-fw" aria-hidden="true"></i> -->
				<img src="images/auth_button.png" />
            </button>

            <div class="registration">
                <a class="" data-toggle="modal" href="#myModal">
                Возникли трудности при входе в систему
                </a>
				<a class="" id="#myModal3" data-toggle="modal" href="#myModal3"></a>
            </div>

            <label class="copy_bottom">
                    ©<?=$CONFIG['year'];?>  <?=$CONFIG['city'];?>
            </label>
			
        </div>

        <!-- Modal -->
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Инструкция по входу в систему</h4>
                    </div>
                    <div class="modal-body">
                        <p>Инструкция</p>
                        Если возникают проблемы с авторизацией через Битрикс 24, воспользуйтесь <a href="index_yaoauth.php">аварийной авторизацией</a> через сервер Yandex Почты.
						<br>
						ВНИМАНИЕ, при данном способе авторизации некоторые функции портала органичены!

                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Закрыть подсказку</button>
                        <!-- <button class="btn btn-primary" type="button">Запросить данные</button> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->
		
    </form>
</div>



<!-- Placed js at the end of the document so the pages load faster -->
<!-- Placed js at the end of the document so the pages load faster -->
<script src="js/jquery-1.10.2.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/modernizr.min.js"></script>

</body>
</html>

<?

/******************************************************************/

}
else
{
/******************************************************************/
//Контент главной страницы








//подключаем файл со всеми функциями системы
include_once("functions.inc.php");

//проверяем авторизацию на портале (с авторизацией Битрикс24 не связано)
authsessionCheck();

//подключаем файл конфиг авторизации Битрикс 24
include_once("oauthbx24/include/config.php");

// задаем html-title страницы
$CONFIG['title_header'] = "Новости и объявления - ".$CONFIG['title_header'];

//подключаем верстку
include("head.inc.php"); //шапка сайта
include("navbarleft.inc.php"); //боковое меню навигации
include("navbartop.inc.php"); //верхнее меню навигации
	
// Заготовка запроса с внешними переменными
$stmt = $dbConnection->query('SELECT * from crm_applist ORDER BY num DESC');
$applist = $stmt->fetchAll();	

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


<!-- начало тела станицы -->

        <!--body wrapper start-->
        <div class="wrapper">
		<div class="row">
			<div class="col-md-8">
				<section class="panel">
					<header class="panel-heading">
						Новости и важные объявления
					</header>
					<div class="panel-body">

					 Раздел находится в стадии разработки... 
<!--	
				 
<ul class="activity-list">
<?
//запрос на вывод всего списка новостей\объявлений
$stmt_news_ad = $dbConnection->query('SELECT * FROM crm_news_ad ORDER BY date DESC');
$news_ad = $stmt_news_ad->fetchAll();

//вывод данных
foreach($news_ad as $news_ad_row) {
	//запрос для выяснения прочитана новость\объявление или нет
	$stmt_view_news_ad = $dbConnection->query('SELECT * FROM crm_views WHERE type_content = "news_and_ad" AND id_content = "'.$news_ad_row['id'].'" AND bitrix24_client_id = "'.getUserInfo($_SESSION["auth_id"], 'bitrix24_id').'"');
	$view_news_ad = $stmt_view_news_ad->fetchAll();
	$view_news_ad_count = $stmt_view_news_ad->rowCount();
	if ($news_ad_row['type'] == 'news') {
		$type = '[новость]';
	} if ($news_ad_row['type'] == 'ad') {
		$type = '[объявление]';
	}
	
	if ($view_news_ad_count == '0') {
		$view_border = 'style="border: 2px solid #38b1de;"';
		$view_b_open = '<b>';
		$view_b_close = '</b>';
	} else {
		$view_border = '';
		$view_b_open = '';
		$view_b_close = '';
	}
?>
	<li>
		<div class="avatar">
			<img <?=$view_border;?> src="images/news/<?=$news_ad_row['type'];?>.png" alt="<?=$type;?>"/>
		</div>
		<div class="activity-desk">
			<h5><a target="_blank" href="https://dverim.bitrix24.ru/company/personal/user/<?=$news_ad_row['bitrix24_client_id'];?>/"><?=$news_ad_row['bitrix24_client_name'];?></a></h5>
			<p class="text-muted" style = "font-size: 12px;"><?=$view_b_open;?><?=d_format($news_ad_row['date'], 'f1');?><?=$view_b_close;?></p>
			<?=$view_b_open;?><?=$news_ad_row['preview'];?><?=$view_b_close;?>
			<br><br>
			<button class="btn btn-success btn-xs" type="button"><i class="fa fa-eye"></i>&nbsp;&nbsp;читать&nbsp;</button>
		</div>
	</li>
<?
}
?>
</ul>

-->

										</div>
				</section>
			</div>
			
			<div class="col-md-4">
				<section class="panel">
					<header class="panel-heading">
						АКТУАЛЬНЫЙ БЛАНК-ЗАКАЗА
					</header>
					<div class="panel-body">
					
						<a href="<?=$CONFIG['hostname'];?>uploads/blank_version/blank_version_23_06_17.xlsx" class="btn btn-info btn-xs btn-lg btn-block">
                            <i class="fa fa-file-excel-o"></i>&nbsp;&nbsp; Скачать бланк от 23 июня 2017
                        </a>
					<br>
							<div style="height: 300px; overflow-y: scroll;">
							
<b style="font-size: 11px;"><i>23 июня 2017</i></b>
<br>
	<p style="font-size: 10px;">
		&nbsp;&nbsp;&nbsp; - добавлено наименование "Добор 14х60х100"
		<br>
		&nbsp;&nbsp;&nbsp; - добавлено наименование "Портал 60х100"
	</p>
<hr>
							</div>
					</div>
				</section>
			</div>
			
		</div>
        </div>
        <!--body wrapper end-->




<?
		include("footer.inc.php"); // подвал страницы
}

?>

<?
// --- РЕЖИМ ТЕХНИЧЕСКИХ РАБОТ /конец/ ---
} else {
	include("offline/offline.php"); /* страница технических работ */
}
?>





