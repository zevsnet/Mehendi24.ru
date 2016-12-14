<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . SITE_TEMPLATE_ID . "/header.php");
$wizTemplateId = COption::GetOptionString("main", "wizard_template_id", "eshop_adapt_horizontal", SITE_ID);
CUtil::InitJSCore();
CJSCore::Init(array("fx"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <? $APPLICATION->ShowHead();
    /**/
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/style.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/style.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/theme-color/default-theme.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/bootstrap-progressbar-3.3.4.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/animate.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/jquery.fancybox.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/slick.css');
    /*Botstrap 3*/
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/bootstrap.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/font-awesome.css');
    ?>
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Fonts -->
    <!-- Open Sans for body font -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <!-- Lato for Title -->
    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>


    <title><? $APPLICATION->ShowTitle() ?></title>

    <? $APPLICATION->ShowMeta("HandheldFriendly"); ?>
    <? $APPLICATION->ShowMeta("apple-mobile-web-app-capable", "yes"); ?>
    <? $APPLICATION->ShowMeta("apple-mobile-web-app-status-bar-style"); ?>
    <? $APPLICATION->ShowMeta("SKYPE_TOOLBAR"); ?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
<body>
<?$APPLICATION->IncludeFile(SITE_DIR."local/include/tag.php", Array(), Array("MODE" => "text", "NAME" => "TAG")); ?>
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>

<!-- BEGAIN PRELOADER -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>
<!-- END PRELOADER -->

<!-- SCROLL TOP BUTTON -->
<a class="scrollToTop" href="#"><i class="fa fa-angle-up"></i></a>
<!-- END SCROLL TOP BUTTON -->
<!-- Start header -->
<header id="header">
    <!-- header top search -->
    <div class="header-top">
        <div class="container">
            <form action="">
                <div id="search">
                    <input type="text" placeholder="Type your search keyword here and hit Enter..." name="s" id="m_search" style="display: inline-block;">
                    <button type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- header bottom -->
    <div class="header-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <div class="header-contact">
                        <?/*<ul>
                            <li>
                                <div class="phone">
                                    <i class="fa fa-phone"></i>
                                    <? $APPLICATION->IncludeComponent("bitrix:main.include", "", Array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH"           => "/include_arear/header/phone_number.php",
                                        "EDIT_TEMPLATE"  => ""
                                    ), false); ?>
                                </div>
                            </li>
                            <li>
                                <div class="mail">
                                    <i class="fa fa-envelope"></i>
                                    <? $APPLICATION->IncludeComponent("bitrix:main.include", "", Array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH"           => "/include_arear/header/email.php",
                                        "EDIT_TEMPLATE"  => ""
                                    ), false); ?>
                                </div>
                            </li>
                        </ul>*-/?>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <? /*<div class="header-login">
                        <a class="login modal-form" data-target="#login-form" data-toggle="modal" href="#">Корзина <span class="text-content">
                                <span class="basket-total-price  hidden-xs marked"><span class="value">0</span> <span class="b-rub">Р</span></span>
                            </span>
                        </a>
                    </div>*/ ?>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- End header -->

