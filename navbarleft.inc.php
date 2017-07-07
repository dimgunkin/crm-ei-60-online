    <!-- left side start-->
    <div class="left-side sticky-left-side">

        <!--logo and iconic logo start-->
        <div class="logo">
            <a href="/"><img src="images/logo.png" alt=""></a>
        </div>

        <div class="logo-icon text-center">
            <a href="/"><img src="images/logo_icon.png" alt=""></a>
        </div>
        <!--logo and iconic logo end-->

        <div class="left-side-inner">

            <!--sidebar nav start-->
            <ul class="nav nav-pills nav-stacked custom-nav">

			<!--
			<a class="info-number-left-menu"> <span class="badge">новое</span>
			-->
				
				
				<li <?=echoActiveClassSubMenu("index.php");?> <?=echoActiveClassSubMenu("");?>>
					<a href="index.php"><i class="fa fa-newspaper-o"></i>
						<span>Новости и объявления</span>
					</a>
				</li>
				
				
				<li <?=echoActiveClassSubMenu("order_list.php");?>>
					<a href="order_list.php"><i class="fa fa-th-list"></i>
						<span>Заказы в производстве</span>
					</a>
				</li>
				
				<!-- СТАТИСТИКА И РЕЙТИНГ ПРОДАЖ -->
				<?
					if (check_access('menuleft_stats_sales', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
					{
				?>
					<li <?=echoActiveClassSubMenu("stats_rate_sales.php");?>>
						<a href="stats_rate_sales.php"><i class="fa fa-bar-chart"></i>
							<span>Статистика по продажам</span>
						</a>
					</li>
				<?	
					} 
				?>					
				<!-- СТАТИСТИКА И РЕЙТИНГ ПРОДАЖ конец -->
				
				<!-- КОНТАКТЫ СОТРУДНИКОВ -->
				<?
					if (check_access('menuleft_contsats_company', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
					{
				?>
					<li <?=echoActiveClassSubMenu("contacts_company.php");?>>
						<a href="contacts_company.php"><i class="fa fa-address-card-o"></i>
							<span>Контакты сотрудников</span>
						</a>
					</li>
				<?	
					} 
				?>					
				<!-- КОНТАКТЫ СОТРУДНИКОВ конец -->

<br>				
				
<!-- РАЗДЕЛ ЗАГРУЖЕННОСТИ -->
<?
	if (check_access('menuleft_info_on_days', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{
?>
<center><b style="color: #cdcdcd;"> ПРОИЗВОДСТВО </b></center> <br>
				<li <?=echoActiveClassSubMenu("info_on_days.php");?> style="background-color: #3D7030;">
					<a href="info_on_days.php"><i class="fa fa-tasks"></i>
						<span>Таблица загруженности</span>
					</a>
				</li>
<?	
	} 
?>					
<!-- РАЗДЕЛ РАЗГРУЖЕННОСТИ конец -->
				
				
<!-- РАЗДЕЛЫ АДМИНИСТРАТОРА -->	
<?
	if (check_access('menuleft_adm', getUserInfo($_SESSION["auth_id"], 'work_position_access')))
	{
?>
<br> <center><b style="color: #cdcdcd;"> РАЗДЕЛ АДМИНИСТРАТОРА </b></center> <br>
	<li <?=echoActiveClassSubMenu("debug.php");?> style="background-color: #707030;">
		<a href="debug.php"><i class="fa fa-bug"></i>
			<span>СТРАНИЦА ОТЛАДКИ</span>
		</a>
	</li>
	
	<li <?=echoActiveClassSubMenu("crm_adminka.php");?> style="background-color: #920e0e;">
		<a href="crm_adminka.php"><i class="fa fa-cogs"></i>
			<span>АДМИНИСТРИРОВАНИЕ</span>
		</a>
	</li>
	
<?	
	} 
?>			
<!-- РАЗДЕЛЫ АДМИНИСТРАТОРА конец -->				
				
				<!--
				<li class="menu-list <?=echoActiveClassMenu("1234");?>"><a href=""><i class="fa fa-laptop"></i> <span>Управление</span></a>
                    <ul class="sub-menu-list">
                        <li <?=echoActiveClassSubMenu("1234");?>><a href="1234">Менеджер задач</a></li>
                    </ul>
                </li>
				-->

            </ul>
            <!--sidebar nav end-->

        </div>
    </div>
    <!-- left side end-->
	
	
	<!-- main content start-->
    <div class="main-content" >