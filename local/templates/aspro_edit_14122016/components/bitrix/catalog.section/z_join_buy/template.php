<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
} ?>
<? if(count($arResult['ITEMS']) > 0): ?>
    <div class="karousel_top" style="position: relative;">
        <div class="">
            <div class="karousel_top_wrap">
                <?
                if($arResult['ITEMS'])
                {
                    foreach($arResult['ITEMS'] as $key => $arItem):
                        if(strlen($arItem['PREVIEW_PICTURE']['SRC']) > 0)
                        {
                            $picture = $arItem['PREVIEW_PICTURE']['SRC'];
                        }
                        else
                        {
                            $picture = "/bitrix/templates/koloristika_org/img/nophotomin.png";
                        }
                        ?>
                        <div class="l_card news-on-main ">
                            <?
                            include($_SERVER['DOCUMENT_ROOT'] . "/dev_catalog/viwer_element.php");
                            ?>
                        </div>
                    <?endforeach;
                } ?>
            </div>
        </div>
    </div>
<? endif; ?>