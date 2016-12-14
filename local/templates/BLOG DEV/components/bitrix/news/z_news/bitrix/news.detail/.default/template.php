<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
$this->setFrameMode(true);
?>
<div class="news-detail">
    <h3><?=$arResult["NAME"]?></h3>

    <div class="row">
        <? if($arResult["DETAIL_PICTURE"]): ?>
            <div class="col-md-12 z_detail_pic" style="background-image: url('<?=$arResult["DETAIL_PICTURE"]["SRC"]?>');"></div>
            <br/>
        <? endif; ?>
        <div class="col-md-12">
            <? if(strlen($arResult["DETAIL_TEXT"]) > 0): ?>
                <? echo $arResult["DETAIL_TEXT"]; ?>
            <? else: ?>
                <? echo $arResult["PREVIEW_TEXT"]; ?>
            <? endif ?>
        </div>
    </div>
    <br/>
</div>

