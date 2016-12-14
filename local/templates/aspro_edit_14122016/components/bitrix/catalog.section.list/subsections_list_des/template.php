<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
<? $this->setFrameMode(true); ?>

<? if($arResult["SECTION"]["DESCRIPTION"]): ?>
    <hr class="long"/>
    <div class="main_description"><?=$arResult["SECTION"]["DESCRIPTION"]?></div>
<? else: ?>
    <? $arSection = CIBlockSection::GetList(array(), array(
        "IBLOCK_ID" => $arResult["SECTION"]["IBLOCK_ID"],
        "ID"        => $arResult["ID"]
    ), false, array("ID", "UF_SECTION_DESCR"))->GetNext(); ?>
    <? if($arSection["UF_SECTION_DESCR"]): ?>
        <hr class="long"/>
        <div class="main_description"><?=$arSection["UF_SECTION_DESCR"]?></div>
    <? endif; ?>
<? endif; ?>