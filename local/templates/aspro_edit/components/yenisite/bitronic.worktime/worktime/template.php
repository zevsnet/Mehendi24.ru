<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
<? if(method_exists($this, 'setFrameMode'))
    $this->setFrameMode(true); ?>

<div class='ys-timeline'>
    <div class="z_line_worck">
        <span class="icon-new icon-new-work-time"></span>
        <span class="z_worck" datetime="Mo,Su 9:00-21:00">Пн-Вс с <?=str_replace('-', ' до ', $arParams["TIME_WORK"])?></span>
    </div>
    <div class="z_line_worck">

        <span class="z_phones"><? $APPLICATION->IncludeFile(SITE_DIR . "include/phone.php", Array(), Array(
                "MODE" => "html",
                "NAME" => GetMessage("PHONE")
            )); ?></span>
    </div>
    <div class="z_line_worck">
        <span class="order_wrap_btn">
            <span class="z_callback_btn callback_btn"><?=GetMessage("CALLBACK")?></span>
        </span>
    </div>
    <div class='ys-lunch'><?=$arParams["LUNCH"]?></div>

</div>