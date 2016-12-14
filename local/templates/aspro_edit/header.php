<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
if($GET["debug"] == "y")
{
    error_reporting(E_ERROR | E_PARSE);
}
IncludeTemplateLangFile(__FILE__);
global $APPLICATION, $TEMPLATE_OPTIONS, $arSite;
$arSite = CSite::GetByID(SITE_ID)->Fetch();
$htmlClass = ($_REQUEST && isset($_REQUEST['print']) ? 'print' : false);
?>
    <!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?=($htmlClass ? 'class="' . $htmlClass . '"' : '')?>>
    <head>
        <html lang="ru">
        <title><? $APPLICATION->ShowTitle() ?></title>
        <? $APPLICATION->ShowMeta("viewport"); ?>
        <? $APPLICATION->ShowMeta("HandheldFriendly"); ?>
        <? $APPLICATION->ShowMeta("apple-mobile-web-app-capable", "yes"); ?>
        <? $APPLICATION->ShowMeta("apple-mobile-web-app-status-bar-style"); ?>
        <? $APPLICATION->ShowMeta("SKYPE_TOOLBAR"); ?>

        <? $APPLICATION->ShowHead(); ?>

        <? $APPLICATION->AddHeadString('<script>BX.message(' . CUtil::PhpToJSObject($MESS, false) . ')</script>', true); ?>
        <? if(CModule::IncludeModule("aspro.mshop"))
        {
            CMShop::Start(SITE_ID);
        } ?>
        <!--[if gte IE 9]>
        <style type="text/css">.basket_button, .button30, .icon { filter: none; }</style><![endif]-->
        <link href='<?=CMain::IsHTTPS() ? 'https' : 'http'?>://fonts.googleapis.com/css?family=Ubuntu:400,500,700,400italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <meta name="yandex-verification" content="0bcfbfa6fa7e9e39"/>
        <meta name="google-site-verification" content="sUwgOMAJWVzMsfLtTIWydavrrueTNnenvVNrY9Yni-g"/>
        <meta name="cypr-verification" content="c2b81d9e4f45cf20aa8c386de767289b"/>
        <meta name="wot-verification" content="9f100f5360bc8d128293"/>
        <meta name="author" content="Mehendi24">
        <meta name="copyright" content="Mehendi24.ru">
        <meta name="robots" content="index, follow">
        <meta http-equiv="content-type" content="text/html;UTF-8">
        <meta http-equiv="cache-control" content="cache">
        <meta http-equiv="content-language" content="ru">
        <meta http-equiv="revisit-after" content="1 days">
        <link rel="stylesheet" href="/local/newyear/style.css">

        <script type="text/javascript">
            var SbGlobal = {};
        </script>

        <?
        use Bitrix\Main\Page\Asset;

        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/external/slick/slick.min.js");
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/external/slick/slick.css");
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/external/slick/slick-theme.css");
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/external/select2-4.0.3/js/select2.min.js");
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/external/jquery.scrollbar.min.js");
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/external/jquery.scrollbar.css");
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/external/select2-4.0.3/css/select2.min.css");
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/sb_main.js");
        ?>

    </head>
<body id="main">
    <div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<? if(!CModule::IncludeModule("aspro.mshop"))
{ ?>
    <center><? $APPLICATION->IncludeFile(SITE_DIR . "include/error_include_module.php"); ?></center></body></html><? die(); ?><? } ?>
<? $APPLICATION->IncludeComponent("aspro:theme.mshop", ".default", array("COMPONENT_TEMPLATE" => ".default"), false); ?>
<? CMShop::SetJSOptions(); ?>
<? $isFrontPage = CSite::InDir(SITE_DIR . 'index.php'); ?>
<? $isContactsPage = CSite::InDir(SITE_DIR . 'contacts/'); ?>
<? $isBasketPage = CSite::InDir(SITE_DIR . 'basket/'); ?>

<div class="wrapper <?=($TEMPLATE_OPTIONS["HEAD"]["CURRENT_MENU_COLOR"] != "none" ? "has_menu" : "");?> h_color_<?=$TEMPLATE_OPTIONS["HEAD"]["CURRENT_HEAD_COLOR"];?> m_color_<?=$TEMPLATE_OPTIONS["HEAD"]["CURRENT_MENU_COLOR"];?> <?=($isFrontPage ? "front_page" : "");?> basket_<?=strToLower($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"]);?> head_<?=strToLower($TEMPLATE_OPTIONS["HEAD"]["CURRENT_VALUE"]);?> banner_<?=strToLower($TEMPLATE_OPTIONS["BANNER_WIDTH"]["CURRENT_VALUE"]);?>">
    <div class="header_wrap <?=strtolower($TEMPLATE_OPTIONS["HEAD_COLOR"]["CURRENT_VALUE"])?>">
        <div class="top-h-row">
            <div class="wrapper_inner">
                <div class="content_menu">
                    <? $APPLICATION->IncludeComponent("bitrix:menu", "top_content_row", array(
                        "ROOT_MENU_TYPE"        => $TEMPLATE_OPTIONS["HEAD"]["CURRENT_MENU"],
                        "MENU_CACHE_TYPE"       => "Y",
                        "MENU_CACHE_TIME"       => "86400",
                        "MENU_CACHE_USE_GROUPS" => "N",
                        "MENU_CACHE_GET_VARS"   => array(),
                        "MAX_LEVEL"             => "1",
                        "CHILD_MENU_TYPE"       => "left",
                        "USE_EXT"               => "N",
                        "DELAY"                 => "N",
                        "ALLOW_MULTI_SELECT"    => "N",
                    ), false); ?>
                </div>
                <div class="phones">
							<span class="phone_wrap">
								<span class="icons"></span>
								<span class="phone_text">
									<? $APPLICATION->IncludeFile(SITE_DIR . "include/phone.php", Array(), Array(
                                        "MODE" => "html",
                                        "NAME" => GetMessage("PHONE")
                                    )); ?>
								</span>
							</span>
							<span class="order_wrap_btn">
								<span class="callback_btn"><?=GetMessage("CALLBACK")?></span>
							</span>
                </div>
                <div class="h-user-block" id="personal_block">
                    <div class="form_mobile_block">
                        <div class="search_middle_block"><? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/search.title.catalog3.php'); ?></div>
                    </div>
                    <? $APPLICATION->IncludeComponent("bitrix:system.auth.form", "top", array(
                        "REGISTER_URL"        => SITE_DIR . "auth/registration/",
                        "FORGOT_PASSWORD_URL" => SITE_DIR . "auth/forgot-password/",
                        "PROFILE_URL"         => SITE_DIR . "personal/",
                        "SHOW_ERRORS"         => "Y"
                    )); ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <header id="header">
            <div class="wrapper_inner">
                <table class="middle-h-row" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td class="logo_wrapp">
                            <div class="logo">
                                <? CMShop::ShowLogo(); ?>
                            </div>
                        </td>
                        <td class="center_block">
                            <div class="main-nav">
                                <? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/menu.top_general_multilevel.php'); ?>
                            </div>
                            <!--div class="middle_phone">
                                <div class="phones">
										<span class="phone_wrap">
											<span class="icons"></span>
											<span class="phone_text">
												<? $APPLICATION->IncludeFile(SITE_DIR . "include/phone.php", Array(), Array(
                                "MODE" => "html",
                                "NAME" => GetMessage("PHONE")
                            )); ?>
											</span>
										</span>
										<span class="order_wrap_btn">
											<span class="callback_btn"><?=GetMessage("CALLBACK")?></span>
										</span>
                                </div>
                            </div-->
                            <div class="search z_worck">
                                <? $APPLICATION->IncludeComponent("yenisite:bitronic.worktime", "worktime", array(
                                    "CACHE_TIME"         => "360000",
                                    "CACHE_TYPE"         => "A",
                                    "FRIDAY"             => "Y",
                                    "LUNCH"              => "",
                                    "MONDAY"             => "Y",
                                    "SATURDAY"           => "Y",
                                    "SUNDAY"             => "Y",
                                    "THURSDAY"           => "Y",
                                    "TIME_WEEKEND"       => "09:00-18:00",
                                    "TIME_WORK"          => "09:00 - 21:00",
                                    "TUESDAY"            => "Y",
                                    "WEDNESDAY"          => "Y",
                                    "COMPONENT_TEMPLATE" => "worktime"
                                ), false); ?>

                            </div>
                            <div class="search">
                                <div style="text-align: center"><span>Интернет магазин мастеров красоты</span></div>
                                <? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/search.title.catalog.php'); ?>
                            </div>

                        </td>
                        <td class="basket_wrapp">
                            <div class="wrapp_all_icons">
                                <? /*<div class="header-compare-block icon_block iblock" id="compare_line">
                                    <? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/catalog.compare.list.compare_top.php'); ?>
                                </div>*/ ?>
                                <div class="header-cart" id="basket_line">
                                    <? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("header-cart"); ?>
                                    <? //CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);?>
                                    <? if($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"] == "FLY" && !$isBasketPage && !CSite::InDir(SITE_DIR . 'order/')): ?>
                                        <script type="text/javascript">
                                            $(document).ready(function()
                                            {
                                                $.ajax({
                                                    url: arMShopOptions['SITE_DIR'] + 'ajax/basket_fly.php',
                                                    type: 'post',
                                                    success: function(html)
                                                    {
                                                        $('#basket_line').append(html);
                                                    }
                                                });
                                            });
                                        </script>
                                    <? endif; ?>
                                    <? $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "top", array(
                                        "PATH_TO_BASKET" => SITE_DIR . "basket/",
                                        "PATH_TO_ORDER"  => SITE_DIR . "order/"
                                    )); ?>
                                    <? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("header-cart", ""); ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="catalog_menu">
                <div class="wrapper_inner">
                    <div class="wrapper_middle_menu">
                        <? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/menu.top_catalog_multilevel.php'); ?>
                    </div>
                </div>
            </div>
        </header>
    </div>
<? if(!$isFrontPage): ?>
    <div class="wrapper_inner">
        <section class="middle">
            <div class="container">
                <? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "mshop_new", array(
                    "START_FROM"       => "0",
                    "PATH"             => "",
                    "SITE_ID"          => "-",
                    "SHOW_SUBSECTIONS" => "N"
                ), false); ?>
                <h1><?=$APPLICATION->ShowTitle(false);?></h1>
                <? if($isContactsPage): ?>
            </div>
        </section>
    </div>
    <? else: ?>
    <div id="content">
    <? if(CSite::InDir(SITE_DIR . 'help/') || CSite::InDir(SITE_DIR . 'company/') || CSite::InDir(SITE_DIR . 'info/')): ?>
    <div class="left_block">
        <? $APPLICATION->IncludeComponent("bitrix:menu", "left_menu", array(
            "ROOT_MENU_TYPE"        => "left",
            "MENU_CACHE_TYPE"       => "A",
            "MENU_CACHE_TIME"       => "3600000",
            "MENU_CACHE_USE_GROUPS" => "N",
            "MENU_CACHE_GET_VARS"   => "",
            "MAX_LEVEL"             => "1",
            "CHILD_MENU_TYPE"       => "left",
            "USE_EXT"               => "Y",
            "DELAY"                 => "N",
            "ALLOW_MULTI_SELECT"    => "N"
        ), false, array("ACTIVE_COMPONENT" => "Y")); ?>
    </div>
    <div class="right_block">
<? endif; ?>
<? endif; ?>
<? endif; ?>
<? if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest")
    $APPLICATION->RestartBuffer(); ?>