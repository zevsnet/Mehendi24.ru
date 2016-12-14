<?if($arResult['ITEMS']):?>
	<h1><?=$arResult[ITEMS][0][PROPERTIES][CML2_MANUFACTURER][VALUE]?></h1>

	<?$APPLICATION->SetTitle('Акция на продукцию '.$arResult[ITEMS][0][PROPERTIES][CML2_MANUFACTURER][VALUE]);?>
<?endif;?>