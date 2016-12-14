<div class="catalog_item_wrapp ">
    <?$arProperties = array();?>
    <?foreach( $arItem["DISPLAY_PROPERTIES"] as $arProp ):?>
        <?if( !empty( $arProp["VALUE"] ) ):?>
            <?$value = $arProp["DISPLAY_VALUE"] ? $arProp["DISPLAY_VALUE"] : $arProp["VALUE"];
            $arProperties[] = $arProp['NAME'] . ': ' . $value;?>
        <?endif;?>
    <?endforeach;?>
    <?$props = $arProperties;//implode(', ', $arProperties);?>
    <?if(isset($arParams['arParams']))
        $arParams = $arParams['arParams'];
    if($arParams["DISPLAY_COMPARE"] == 'true')
        $arParams["DISPLAY_COMPARE"] = true;
    //_::dd($arItem);?>
    <div class="basket_props_block" id="bx_basket_div_<?=$arItem["ID"];?>" style="display: none;">
        <?
        if(!empty($arItem['PRODUCT_PROPERTIES_FILL']))
        {
            foreach($arItem['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo)
            {
                ?>
                <input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
                <?
                if(isset($arItem['PRODUCT_PROPERTIES'][$propID]))
                    unset($arItem['PRODUCT_PROPERTIES'][$propID]);
            }
        }
        $arItem["EMPTY_PROPS_JS"] = "Y";
        $emptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
        if(!$emptyProductProperties)
        {
            $arItem["EMPTY_PROPS_JS"] = "N";
            ?>
            <div class="wrapper">
                <table>
                    <?
                    foreach($arItem['PRODUCT_PROPERTIES'] as $propID => $propInfo)
                    {
                        ?>
                        <tr>
                            <td><? echo $arItem['PROPERTIES'][$propID]['NAME']; ?></td>
                            <td>
                                <?
                                if('L' == $arItem['PROPERTIES'][$propID]['PROPERTY_TYPE'] && 'C' == $arItem['PROPERTIES'][$propID]['LIST_TYPE'])
                                {
                                    foreach($propInfo['VALUES'] as $valueID => $value)
                                    {
                                        ?><label>
                                        <input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
                                        </label><?
                                    }
                                }
                                else
                                {
                                    ?>
                                    <select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"><?
                                    foreach($propInfo['VALUES'] as $valueID => $value)
                                    {
                                        ?>
                                        <option value="<? echo $valueID; ?>" <? echo($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option><?
                                    }
                                    ?></select><?
                                }
                                ?>
                            </td>
                        </tr>
                        <?
                    }
                    ?>
                </table>
            </div>
            <?
        }
        ?>
    </div>
    <? //$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
    //$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

    //$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID']);
    $arItemIDs = CMShop::GetItemsIDs($arItem);

    $totalCount = CMShop::GetTotalCount($arItem);
    $arQuantityData = CMShop::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"]);
    //_::dd($arItem);

    $item_id = $arItem["ID"];
    $strMeasure = '';
    if(!$arItem["OFFERS"] || $arParams['TYPE_SKU'] !== 'TYPE_1')
    {
        if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"])
        {
            $arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
            $strMeasure = $arMeasure["SYMBOL_RUS"];
        }
        $arAddToBasketData = CMShop::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
    }
    elseif($arItem["OFFERS"])
    {
        $strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
    }
    ?>
    <div class="catalog_item item_wrap sb_full_container <?=(($_GET['q'])) ? 's' : ''?>" id="<?=$arItemIDs["strMainID"];?>">
        <div>
            <div class="image_wrapper_block">
                <? /*$frame = $this->createFrame()->begin('');
							$frame->setBrowserStorage(true);*/ ?>
                <? if((!$arItem["OFFERS"] && $arParams["DISPLAY_WISH_BUTTONS"] != "N") || ($arParams["DISPLAY_COMPARE"] == "Y")): ?>
                    <div class="like_icons">
                        <? if($arParams["DISPLAY_WISH_BUTTONS"] != "N" && $totalCount > 0): ?>
                            <? if(!$arItem["OFFERS"]): ?>
                                <div class="wish_item_button">
                                    <span title="Отложить" class="wish_item to" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
                                    <span title="В отложенных" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
                                </div>
                            <? elseif($arItem["OFFERS"] && !empty($arItem['OFFERS_PROP'])): ?>
                                <div class="wish_item_button" style="display: none;">
                                    <span title="Отложить" class="wish_item to <?=$arParams["TYPE_SKU"];?>" data-item="" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
                                    <span title="В отложенных" class="wish_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="" data-iblock="<?=$arOffer["IBLOCK_ID"]?>"><i></i></span>
                                </div>
                            <? endif; ?>
                        <? endif; ?>
                        <? if($arParams["DISPLAY_COMPARE"] == "Y"): ?>
                            <? if(!$arItem["OFFERS"] || $arParams["TYPE_SKU"] !== 'TYPE_1'): ?>
                                <div class="compare_item_button">
                                    <span title="Сравнить" class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
                                    <span title="В сравнении" class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
                                </div>
                            <? elseif($arItem["OFFERS"]): ?>
                                <div class="compare_item_button">
                                    <span title="Сравнить" class="compare_item to <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
                                    <span title="В сравнении" class="compare_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
                                </div>
                            <? endif; ?>
                        <? endif; ?>
                    </div>
                <? endif; ?>
                <? //$frame->end();?>
                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>">
                    <div class="stickers">
                        <? if(is_array($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])): ?>
                            <? foreach($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"] as $key => $class)
                            { ?>
                                <div class="sticker_<?=strtolower($class);?>" title="<?=$arItem["PROPERTIES"]["HIT"]["VALUE"][$key]?>"></div>
                            <? } ?>
                        <? endif; ?>
                    </div>
                    <?
                    $a_alt = ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"]);
                    $a_title = ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"]);
                    ?>
                    <? if(!empty($arItem["PREVIEW_PICTURE"]) && !$arItem['SKU']): ?>
                        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>"/>
                    <? elseif(!empty($arItem["DETAIL_PICTURE"])): ?>
                        <? $img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array(
                            "width" => 170,
                            "height" => 170
                        ), BX_RESIZE_IMAGE_PROPORTIONAL, true); ?>
                        <img src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>"/>
                    <? elseif(!empty($arItem["PREVIEW_PICTURE"]) && $arItem['SKU']): ?>
                        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>"/>
                    <? else: ?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>"/>
                    <? endif; ?>
                </a>
            </div>
            <div class="item_info <?=$arParams["TYPE_SKU"]?>">
                <div class="item-title">
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></span></a>
                </div>
                <?if(!$arItem['SKU_TEMPLATE']):?>
                    <?=$arQuantityData["HTML"];?>
                <?endif;?>
                <div class="sb_props">
                    <?foreach($props as $prop):?>
                        <div><?=$prop;?></div>
                    <?endforeach;?>
                    <?//=$props?></div>
                <div class="cost prices clearfix">
                    <?
                    /*$frame = $this->createFrame()->begin('');
                    $frame->setBrowserStorage(true);*/
                    ?>
                    <? if($arItem["OFFERS"])
                    { ?>
                        <? $minPrice = false;
                        if(isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE']))
                        {
                            // $minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
                            $minPrice = $arItem['MIN_PRICE'];
                        }
                        $offer_id = 0;
                        if($arParams["TYPE_SKU"] == "N")
                        {
                            $offer_id = $minPrice["MIN_ITEM_ID"];
                        }
                        $min_price_id = $minPrice["MIN_PRICE_ID"];
                        if(!$min_price_id)
                            $min_price_id = $minPrice["PRICE_ID"];
                        if($minPrice["MIN_ITEM_ID"])
                            $item_id = $minPrice["MIN_ITEM_ID"];
                        $prefix = '';
                        if('N' == $arParams['TYPE_SKU'] || $arParams['DISPLAY_TYPE'] !== 'block' || empty($arItem['OFFERS_PROP']))
                        {
                            $prefix = 'от';
                        }
                        if($arParams["SHOW_OLD_PRICE"] == "Y")
                        {
                            ?>
                            <div class="price" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE']; ?>">
                                <? if(strlen($minPrice["PRINT_DISCOUNT_VALUE"])):?>
                                    <?=$prefix;?> <?=$minPrice["PRINT_DISCOUNT_VALUE"];?>
                                    <? if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure)
                                    {
                                        ?>
                                        /<?=$strMeasure?>
                                    <? } ?>
                                <? endif; ?>
                            </div>
                            <div class="price discount" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE_OLD']; ?>">
                                <span <?=(!$minPrice["DISCOUNT_DIFF"] ? 'style="display:none;"' : '')?>><?=$minPrice["PRINT_VALUE"];?></span>
                            </div>
                            <? if($arParams["SHOW_DISCOUNT_PERCENT"] == "Y")
                        {
                            ?>
                            <div class="sale_block" <?=(!$minPrice["DISCOUNT_DIFF"] ? 'style="display:none;"' : '')?>>
                                <? $percent = round(($minPrice["DISCOUNT_DIFF"] / $minPrice["VALUE"]) * 100, 2); ?>
                                <div class="value">-<?=$percent;?>%</div>
                                <div class="text">Экономия
                                    <span><?=$minPrice["PRINT_DISCOUNT_DIFF"];?></span></div>
                                <div class="clearfix"></div>
                            </div>
                        <? } ?>
                        <? }
                        else
                        {
                            ?>
                            <div class="price" id="<?=$arItemIDs["ALL_ITEM_IDS"]['PRICE']?>">
                                <? if(strlen($minPrice["PRINT_DISCOUNT_VALUE"])):?>
                                    <?=$prefix;?> <?=$minPrice['PRINT_DISCOUNT_VALUE'];?>
                                    <? if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure)
                                    {
                                        ?>
                                        /<?=$strMeasure?>
                                    <? } ?>
                                <? endif; ?>
                            </div>
                        <? } ?>
                    <? }
                    elseif($arItem['SKU'])
                    {
                        $minPrice = false;
                        if(isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE']))
                        {
                            // $minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
                            $minPrice = $arItem['MIN_PRICE'];
                        }
                        $offer_id = 0;
                        if($arParams["TYPE_SKU"] == "N")
                        {
                            $offer_id = $minPrice["MIN_ITEM_ID"];
                        }
                        $min_price_id = $minPrice["MIN_PRICE_ID"];
                        if(!$min_price_id)
                            $min_price_id = $minPrice["PRICE_ID"];
                        if($minPrice["MIN_ITEM_ID"])
                            $item_id = $minPrice["MIN_ITEM_ID"];
                        $prefix = '';
//                        if('N' == $arParams['TYPE_SKU'] || $arParams['DISPLAY_TYPE'] !== 'block' || empty($arItem['OFFERS_PROP']))
//                        {
//                            $prefix = 'от';
//                        }
                        if($arParams["SHOW_OLD_PRICE"] == "Y")
                        {
                            ?>
                            <div class="price sb_price" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE']; ?>">
                                <? if(strlen($minPrice["PRINT_DISCOUNT_VALUE"])):?>
                                    <?=$prefix;?> <span><?=$minPrice["PRINT_DISCOUNT_VALUE"];?></span>
                                    <? if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure)
                                    {
                                        ?>
                                        /<?=$strMeasure?>
                                    <? } ?>
                                <? endif; ?>
                            </div>
                            <div class="price discount" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE_OLD']; ?>">
                                <span <?=(!$minPrice["DISCOUNT_DIFF"] ? 'style="display:none;"' : '')?>><?=$minPrice["PRINT_VALUE"];?></span>
                            </div>
                            <? if($arParams["SHOW_DISCOUNT_PERCENT"] == "Y")
                        {
                            ?>
                            <div class="sale_block" <?=(!$minPrice["DISCOUNT_DIFF"] ? 'style="display:none;"' : '')?>>
                                <? $percent = round(($minPrice["DISCOUNT_DIFF"] / $minPrice["VALUE"]) * 100, 2); ?>
                                <div class="value">-<?=$percent;?>%</div>
                                <div class="text">Экономия
                                    <span><?=$minPrice["PRINT_DISCOUNT_DIFF"];?></span></div>
                                <div class="clearfix"></div>
                            </div>
                        <? } ?>
                        <? }
                        else
                        {
                            ?>
                            <div class="price" id="<?=$arItemIDs["ALL_ITEM_IDS"]['PRICE']?>">
                                <? if(strlen($minPrice["PRINT_DISCOUNT_VALUE"])):?>
                                    <?=$prefix;?> <?=$minPrice['PRINT_DISCOUNT_VALUE'];?>
                                    <? if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure)
                                    {
                                        ?>
                                        /<?=$strMeasure?>
                                    <? } ?>
                                <? endif; ?>
                            </div>
                        <? } ?>
                    <? }
                    elseif($arItem["PRICES"])
                    { ?>
                        <? $arCountPricesCanAccess = 0;
                        $min_price_id = 0;
                        foreach($arItem["PRICES"] as $key => $arPrice)
                        {
                            if($arPrice["CAN_ACCESS"])
                            {
                                $arCountPricesCanAccess++;
                            }
                        } ?>
                        <? foreach($arItem["PRICES"] as $key => $arPrice)
                    { ?>
                        <? if($arPrice["CAN_ACCESS"])
                    {
                        $percent = 0;
                        if($arPrice["MIN_PRICE"] == "Y")
                        {
                            $min_price_id = $arPrice["PRICE_ID"];
                        } ?>
                        <? $price = CPrice::GetByID($arPrice["ID"]); ?>
                        <? if($arCountPricesCanAccess > 1): ?>
                        <div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div>
                    <? endif; ?>
                        <? if($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] && $arParams["SHOW_OLD_PRICE"] == "Y")
                    { ?>
                        <div class="price">
                            <? if(strlen($arPrice["PRINT_VALUE"])): ?>
                                <?=$arPrice["PRINT_DISCOUNT_VALUE"];?>
                                <? if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure)
                                { ?>
                                    /<?=$strMeasure?>
                                <? } ?>
                            <? endif; ?>
                        </div>
                        <div class="price discount">
                            <span><?=$arPrice["PRINT_VALUE"];?></span>
                        </div>
                        <? if($arParams["SHOW_DISCOUNT_PERCENT"] == "Y")
                    { ?>
                        <div class="sale_block">
                            <? $percent = round(($arPrice["DISCOUNT_DIFF"] / $arPrice["VALUE"]) * 100, 2); ?>
                            <? if($percent && $percent < 100)
                            { ?>
                                <div class="value">-<?=$percent;?>%</div>
                            <? } ?>
                            <div class="text">Экономия
                                <span><?=$arPrice["PRINT_DISCOUNT_DIFF"];?></span></div>
                            <div class="clearfix"></div>
                        </div>
                    <? } ?>
                    <? }
                    else
                    { ?>
                        <div class="price">
                            <? if(strlen($arPrice["PRINT_VALUE"])): ?>
                                <?=$arPrice["PRINT_VALUE"];?>
                                <? if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure)
                                { ?>
                                    /<?=$strMeasure?>
                                <? } ?>
                            <? endif; ?>
                        </div>
                    <? } ?>
                    <? } ?>
                    <? } ?>
                    <? } ?>
                    <? //$frame->end();?>
                </div>
                <? $arDiscounts = CCatalogDiscount::GetDiscountByProduct($arItem["ID"], $USER->GetUserGroupArray(), "N", $min_price_id, SITE_ID);
                $arDiscount = array();
                if($arDiscounts)
                    $arDiscount = current($arDiscounts);
                if($arDiscount["ACTIVE_TO"])
                {
                    ?>
                    <div class="view_sale_block">
                        <div class="count_d_block">
                            <span class="active_to_<?=$arItem["ID"]?> hidden"><?=$arDiscount["ACTIVE_TO"];?></span>
                            <div class="title">До конца акции</div>
                            <span class="countdown countdown_<?=$arItem["ID"]?> values"></span>
                            <script>
                                $(document).ready(function()
                                {
                                    if($('.countdown').size())
                                    {
                                        var active_to = $('.active_to_<?=$arItem["ID"]?>').text(),
                                            date_to = new Date(active_to.replace(/(\d+)\.(\d+)\.(\d+)/, '$3/$2/$1'));
                                        $('.countdown_<?=$arItem["ID"]?>').countdown({
                                            until: date_to,
                                            format: 'dHMS',
                                            padZeroes: true,
                                            layout: '{d<}<span class="days item">{dnn}<div class="text">{dl}</div></span>{d>} <span class="hours item">{hnn}<div class="text">{hl}</div></span> <span class="minutes item">{mnn}<div class="text">{ml}</div></span> <span class="sec item">{snn}<div class="text">{sl}</div></span>'
                                        }, $.countdown.regionalOptions['ru']);
                                    }
                                })
                            </script>
                        </div>
                        <div class="quantity_block">
                            <div class="title">Остаток</div>
                            <div class="values">
											<span class="item">
												<span class="value" <?=(count($arItem["OFFERS"]) > 0 ? 'style="opacity:0;"' : '')?>><?=$totalCount;?></span>
												<span class="text">штук</span>
											</span>
                            </div>
                        </div>
                    </div>
                <? } ?>
                <div class="hover_block">
                    <? if($arItem["OFFERS"])
                    { ?>
                        <? if(!empty($arItem['OFFERS_PROP']))
                    { ?>
                        <div class="sku_props">
                            <div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>">
                                <? $arSkuTemplate = array(); ?>
                                <? $arSkuTemplate = CMShop::GetSKUPropsArray($arItem['OFFERS_PROPS_JS'], $arResult["SKU_IBLOCK_ID"], $arParams["DISPLAY_TYPE"], $arParams["OFFER_HIDE_NAME_PROPS"]); ?>
                                <? foreach($arSkuTemplate as $code => $strTemplate)
                                {
                                    if(!isset($arItem['OFFERS_PROP'][$code]))
                                        continue;
                                    echo '<div>', str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate), '</div>';
                                } ?>
                            </div>
                            <? $arItemJSParams = CMShop::GetSKUJSParams($arResult, $arParams, $arItem);

                            ?>
                            <script type="text/javascript">
                                var <? echo $arItemIDs["strObName"]; ?> =
                                new JCCatalogSection(<? echo CUtil::PhpToJSObject($arItemJSParams, false, true); ?>);
                            </script>
                        </div>
                    <? } ?>
                    <? } ?>
                    <? if($arItem['SKU']): ?>
                        <div class="sku_props">
                            <div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>">
                                <?=$arItem['SKU_TEMPLATE']?>
                                <?unset($arItem['SKU_TEMPLATE']);?>
                            </div>
                            <script type="text/javascript">
                                SbGlobal.arParams = <? echo CUtil::PhpToJSObject($arParams, false, true); ?>;
                            </script>
                        </div>
                    <? endif; ?>
                    <? if(!$arItem["OFFERS"] || $arParams['TYPE_SKU'] !== 'TYPE_1'): ?>
                        <div class="counter_wrapp <?=($arItem["OFFERS"] && $arParams["TYPE_SKU"] == "TYPE_1" ? 'woffers' : '')?>">
                            <? if(($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arItem["CAN_BUY"]): ?>
                                <div class="counter_block" data-offers="<?=($arItem["OFFERS"] ? "Y" : "N");?>" data-item="<?=$arItem["ID"];?>">
                                    <span class="minus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN']; ?>">-</span>
                                    <input type="text" class="text" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<?=$arAddToBasketData["MIN_QUANTITY_BUY"]?>"/>
                                    <span class="plus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP']; ?>" <?=($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='" . $arAddToBasketData["MAX_QUANTITY_BUY"] . "'" : "")?>>+</span>
                                </div>
                            <? endif; ?>
                            <div id="<?=$arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS'];?>" class="button_block <?=(($arAddToBasketData["ACTION"] == "ORDER"/*&& !$arItem["CAN_BUY"]*/) || !$arItem["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] || $arAddToBasketData["ACTION"] == "SUBSCRIBE" ? "wide" : "");?>">
                                <!--noindex-->
                                <?=$arAddToBasketData["HTML"]?>
                                <!--/noindex-->
                            </div>
                        </div>
                    <? elseif($arItem["OFFERS"]): ?>
                        <? if(empty($arItem['OFFERS_PROP']))
                        { ?>
                            <div class="offer_buy_block buys_wrapp woffers">
                                <?
                                $arItem["OFFERS_MORE"] = "Y";
                                $arAddToBasketData = CMShop::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small read_more1', $arParams); ?>
                                <!--noindex-->
                                <?=$arAddToBasketData["HTML"]?>
                                <!--/noindex-->
                            </div>
                        <? }
                        else
                        { ?>
                            <div class="offer_buy_block buys_wrapp woffers" style="display:none;">
                                <div class="counter_wrapp"></div>
                            </div>
                        <? } ?>
                    <? endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>