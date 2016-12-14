<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
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
<section id="slider">
    <div class="slider_comment">
        <? foreach($arResult["ITEMS"] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            $renderImage = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], Array("width" => 200, "height" => 200));
            ?>
            <div class="z_slider_comment effect3">
                <div class="row" style="text-align: center">
                    <div class="col-md-12">
                        <div class="z_avatar" style="background-image: url('<?=$renderImage['src']?>')"></div>
                    </div>
                    <div class="col-md-12 z_row_comment"><span class="z_title"><?=$arItem['NAME']?></span></div>
                    <div class="col-md-12"><span class="z_description"><?=$arItem['PREVIEW_TEXT']?></span></div>
                </div>
            </div>
        <? endforeach; ?>
    </div>
</section>
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/slick/slick.min.js"></script>
<script>
    $(document).ready(function()
    {
        $('.slider_comment').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            centerMode: true,
            variableWidth: true
        });
    });
</script>