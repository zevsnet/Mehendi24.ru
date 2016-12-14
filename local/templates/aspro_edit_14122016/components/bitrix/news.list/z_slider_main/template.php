<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
    <div class="main-slider">
        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="single-slide">
                <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="img">
                <div class="slide-content">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 col-sm-6" style="position: relative;bottom: -25rem;">
                                <div class="slide-article">
                                    <h1 class="wow fadeInUp" data-wow-duration="0.5s"
                                        data-wow-delay="0.5s"><?= $arItem['PROPERTIES']['Z_NAME']['VALUE'] ?></h1>
                                    <p class="wow fadeInUp" data-wow-duration="0.5s"
                                       data-wow-delay="0.75s"><?= $arItem["PREVIEW_TEXT"] ?></p>
                                    <a class="read-more-btn wow fadeInUp" data-wow-duration="1s" data-wow-delay="1s"
                                       href="<?= $arItem['PROPERTIES']['Z_URL']['VALUE'] ?>"><?= (!empty($arItem['PROPERTIES']['Z_URL_NAME']['VALUE'])) ? $arItem['PROPERTIES']['Z_URL_NAME']['VALUE'] : 'Подробнее' ?></a>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="slider-img wow fadeInUp">
                                    <? $file = CFile::ResizeImageGet($arItem['PROPERTIES']['Z_IMAGE_PERSONAL']['VALUE'], array('width' => 550, 'height' => 765), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                                    if (!empty($file['src'])):
                                        ?>
                                        <img src="<?= $file['src'] ?>" alt="<?= $arItem['PROPERTIES']['Z_NAME']['VALUE'] ?>">
                                    <? endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? endforeach; ?>
    </div>
</section>