<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?

switch($arParams['WEB_FORM_ID']){
    case 'CALLBACK':
        $arParams['WEB_FORM_ID']='SIMPLE_FORM_12';
        break;
}
?>
<?$APPLICATION->IncludeComponent("bitrix:form.result.new", "popup_new", $arParams, $component);?>