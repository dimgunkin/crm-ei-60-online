 <!--footer section start-->
        <footer>
            ООО "Двери металл-М"<br>Воронеж 2016 - 2017
        </footer>
        <!--footer section end-->


    </div>
    <!-- main content end-->
</section>

<!-- Placed js at the end of the document so the pages load faster -->
<script src="js/jquery-1.10.2.min.js"></script>

<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="js/jquery-migrate-1.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/modernizr.min.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<!--JQ\JS обработчики системы CRM.tld-->
<script src="js/crm.tld.inc.js"></script>

<!--easy pie chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<script src="js/easypiechart/easypiechart-init.js"></script>

<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<script src="js/sparkline/sparkline-init.js"></script>

<!--icheck -->
<script src="js/iCheck/jquery.icheck.js"></script>
<script src="js/icheck-init.js"></script>

<!--spinner-->
<script type="text/javascript" src="js/fuelux/js/spinner.min.js"></script>
<script src="js/spinner-init.js"></script>

<!-- jQuery Flot Chart
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>
<script src="js/flot-chart/jquery.flot.selection.js"></script>
<script src="js/flot-chart/jquery.flot.stack.js"></script>
<script src="js/flot-chart/jquery.flot.time.js"></script>
<script src="js/main-chart.js"></script>
-->

<!--pickers plugins-->
<script type="text/javascript" src="js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="js/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>

<!--gritter script-->
<script type="text/javascript" src="js/gritter/js/jquery.gritter.js"></script>
<script src="js/gritter/js/gritter-init.js" type="text/javascript"></script>

<!--file upload-->
<script type="text/javascript" src="js/bootstrap-fileupload.min.js"></script>

<!--pickers initialization-->
<script src="js/pickers-init.js"></script>

<!--bootstrap input mask-->
<script type="text/javascript" src="js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>

<!--common scripts for all pages-->
<script src="js/scripts.js"></script>

<script type="text/javascript">
$(document).ready(function () {
//обновление истекшего токена
//кнопка обновления
$('a[id="refresh_auth_token"]').click(function() {
		$.ajax({
			url: 'jq_processor.php',
			type: 'GET',
			data: {'refresh': '1', 'page': '<?=$_SERVER['PHP_SELF'];?>'},
			cache: false,			
		success: function(data){
				if (data == 'token_ok') {
					location.reload();
				} else {
					$('a[id="refresh_auth_token"]').attr('disabled', true);
					document.location.replace("http://crm.ei-60.online/index.php?clear=1");
				}			
			}
		});	
	});

<?
// -- проверяем, включен ли вход по запасному шлюзу авторизации
if ($CONFIG['yandex_oauth'] == 'disabled') {
?>
//////////// обновляем основной токен bx24 каждый час ////////////
function refresh_token() {
//location.replace("<?=$CONFIG['hostname'];?>index.php?refresh=1&redirect=<?=$_SERVER['REQUEST_URI'];?>")
	$.ajax({
		url: 'index.php',
		type: 'GET',
		data: {'refresh': '1'},
		cache: false,			
	success: function(response){
		//if (data == 'token_refresh_ok') {
			$('#refresh_token').html('<br><br><i style="color: #238c00;" class="fa fa-refresh fa-spin fa-3x fa-fw"></i>');
		//	$('#refresh_token').effect('pulsate', {times: 5}, 4000);
			setTimeout(function(){
			$('#refresh_token').html('  ');
			}, 5000);
		//} else {
		//	$('#refresh_token').html('<i style="padding-top: 10px;" class="fa fa-ban fa-2x" style="color: #d90000;" aria-hidden="true"></i>');
		//	$('#refresh_token').effect('pulsate', {times: 4}, 4000);
		//}		
		}
	});
}

function check_token_live() {
	$.ajax({
		url: 'jq_processor.php',
		type: 'GET',
		data: {'datarequest': 'tokenlive'},
		cache: false,			
	success: function(data){
			if (data == 0) {
				refresh_token();
			} 
		}
	});
}

setInterval(check_token_live, 10000);

});
//////////// обновляем основной токен bx24 каждый час ////////////
<?
}
?>
</script>


</body>
</html>
