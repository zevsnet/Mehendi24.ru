<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>

<!-- Start footer -->
<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-sm-9">
                <div class="footer-left">
                    <div class="row">
					
                        <div class="col-md-4">
                            
							<span style="color:#fff; font-size:12px;">mehendi24 © 2016</span>
                            <? /*$APPLICATION->IncludeComponent("bitrix:menu", "z_menu_bottom", Array(
                                "ROOT_MENU_TYPE"        => "b_menu_1",
                                "MAX_LEVEL"             => "1",
                                "CHILD_MENU_TYPE"       => "",
                                "USE_EXT"               => "N",
                                "MENU_CACHE_TYPE"       => "A",
                                "MENU_CACHE_TIME"       => "604800",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "MENU_CACHE_GET_VARS"   => "",
                                "DELAY"                 => "N",
                                "ALLOW_MULTI_SELECT"    => "N",
                                "COMPONENT_TEMPLATE"    => ""
                            ), false);*/?>
                        </div>
                        <div class="col-md-4">
                           
                            <? /*$APPLICATION->IncludeComponent("bitrix:menu", "z_menu_bottom", Array(
                                "ROOT_MENU_TYPE"        => "b_menu_2",
                                "MAX_LEVEL"             => "1",
                                "CHILD_MENU_TYPE"       => "",
                                "USE_EXT"               => "N",
                                "MENU_CACHE_TYPE"       => "A",
                                "MENU_CACHE_TIME"       => "604800",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "MENU_CACHE_GET_VARS"   => "",
                                "DELAY"                 => "N",
                                "ALLOW_MULTI_SELECT"    => "N",
                                "COMPONENT_TEMPLATE"    => ""
                            ), false); */?>
                        </div>
                        <div class="col-md-4">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3">
                <div class="footer-right">
                    <a href="https://vk.com/loveshellac16"><i class="fa fa-vk"></i></a>
                    <? /*<a href="#"><i class="fa fa-twitter"></i></a>
					<a href="#"><i class="fa fa-google-plus"></i></a>
					<a href="#"><i class="fa fa-linkedin"></i></a>
					<a href="#"><i class="fa fa-pinterest"></i></a>*/ ?>
                </div>
            </div>
        </div>
    </div>
    <? $APPLICATION->IncludeComponent("bitrix:main.include", "", Array(
        "AREA_FILE_SHOW" => "file",
        "PATH"           => "/include_arear/footer/metrika.php",
        "EDIT_TEMPLATE"  => ""
    ), false); ?>
</footer>
<!-- End footer -->

<!-- jQuery library -->

<script src="<?=SITE_TEMPLATE_PATH?>/assets/js/bootstrap.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/assets/js/slick.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/assets/js/jquery.mixitup.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/assets/js/jquery.fancybox.pack.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/assets/js/waypoints.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/assets/js/jquery.counterup.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/assets/js/wow.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/assets/js/bootstrap-progressbar.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/assets/js/custom.js"></script>
</body>
</html>