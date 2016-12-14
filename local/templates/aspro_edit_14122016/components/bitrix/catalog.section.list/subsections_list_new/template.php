<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
<? $this->setFrameMode(true); ?>
<div class="articles-list sections wrap_md">
    <? foreach($arResult["SECTIONS"] as $arItem): ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <section class="item iblock" id="<?=$this->GetEditAreaId($arItem['ID']);?>">

                <div class="item-title">
                    <a href="<?=$arItem["SECTION_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></a>
                </div>

        </section>
    <? endforeach; ?>
</div>
<?/*

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
*/?>