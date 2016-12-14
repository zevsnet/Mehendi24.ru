<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
CModule::IncludeModule("iblock");

?>
<link rel="stylesheet" type="text/css" href="jquery.datetimepicker.css"/>
<script src="jquery.datetimepicker.full.js"></script>
<input type="text" id="datetimepicker"/>
<?
date_default_timezone_set('Asia/Krasnoyarsk');
?>
<script>
$.datetimepicker.setLocale('ru');
	$('#datetimepicker').datetimepicker({
		inline:true,
		value:'<?=date('d.m.Y H:i')?>',
		minDate:'<?=date('d.m.Y H:i')?>',
		dayOfWeekStart:1,
		allowTimes:[
		'09:00','11:00','12:00','21:00'
		],
		step:120,
		format:'d.m.Y H:i',
	});
</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>