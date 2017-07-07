        <!-- header section start-->
        <div class="header-section">

		<a class="toggle-btn"><i class="fa fa-bars"></i></a>
		
            <!--notification menu start -->
            <div class="menu-right">
                <ul class="notification-menu">	
					<li style="padding-right: 20px;">
						<i style="padding-top: 10px;" class="fa fa-calendar-o fa-2x" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<i style="padding-top: 10px; font-size: 23px;"><?=d_format(date("Y-m-d H:i:s"), 'f1');?></i>
					</li>
				
<!-- УВЕДОМЛЕНИЯ --
					<li class="">
						<a href="#" class="btn btn-default dropdown-toggle info-number" data-toggle="dropdown">
							<i class="fa fa-bell-o"></i>
							-- <span class="badge">0</span> --
						</a>
						<div class="dropdown-menu dropdown-menu-head pull-right">
							<h5 class="title">Пропущенные уведомления</h5>
							<ul class="dropdown-list normal-list">
								<li style="padding: 15px; background-color: #f4fbfd;">
										<i class="fa fa-circle" style="color: #38b1de;"></i>
										<span class="name">текст текст текст текст текст уведомление 1 ...  </span>
										<em class="small">2 часа</em>
								</li>
								<li style="padding: 15px;">
										<span class="name">текст текст текст текст текст текст текст текст текст уведомление 2 ... </span>
										<em class="small"> вчера</em>
								</li>
								<li style="padding: 15px;">
										<span class="name">уведомление 3 ... </span>
										<em class="small"> вчера</em>
								</li>
								-- <li class="new"><a href="">Очистить</a></li> --
							</ul>
						</div>
					</li>
					
-- УВЕДОМЛЕНИЯ \конец\ -->

                    <li>
                        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<?
// -- проверяем, включен ли вход по запасному шлюзу авторизации
if ($CONFIG['yandex_oauth'] == 'disabled') {
	
							if (getUserInfo($_SESSION["auth_id"], 'personal_photo') == NULL)
							{
								$avatar_url = "images/no_photo.png";
							} else {
								$avatar_url = getUserInfo($_SESSION["auth_id"], 'personal_photo'); 
							}
							?>
                            <img src="<?=$avatar_url;?>" alt="" />
<?
}
?>
                            <?=getUserInfo($_SESSION["auth_id"], 'last_name').' '.getUserInfo($_SESSION["auth_id"], 'name');?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                            <li><a href="https://dverim.bitrix24.ru/company/personal/user/<?=getUserInfo($_SESSION["auth_id"], 'bitrix24_id');?>/" target="_blank"><i class="fa fa-user"></i> Профиль Битрикс 24</a></li>
                            <!-- <li><a href="#"><i class="fa fa-cog"></i> Настройки</a></li> --> 
                            <li><a href="/index.php?clear=1"><i class="fa fa-sign-out"></i> Выйти из системы...</a></li>
                        </ul>
                    </li>

                </ul>
            </div>
            <!--notification menu end -->

        </div>
        <!-- header section end-->
		
		<center id="refresh_token">
		</center>