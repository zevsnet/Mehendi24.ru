<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<table class="table table-condensed">
	<thead>
	<tr>
		<th>Название</th>
		<th>Цена</th>
	</tr>
	</thead>
	<tbody>
		<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
			<tr style="width: 100%" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<td class="z-table-col-1"><?=$arItem["NAME"]?></td>
				<td class="z-table-col-2"><?$price = $arItem['PROPERTIES']['Z_PRICE']['VALUE'];?>				<?=($price!=0)? $price.' руб.':''?>
				</td>
			</tr>
		<?endforeach;?>
	</tbody>
</table>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<a href="/service"><div class="z_bt_service">Записаться</div></a>
	</div>
</div>