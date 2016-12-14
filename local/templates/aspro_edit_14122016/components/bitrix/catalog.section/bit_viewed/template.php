<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
} ?>
<script type="text/javascript">

    $(function()
    {
        function chQuant(bitemid, bitemquant)
        {
            <?if($_POST[BTOCART]=="Y"){?>
            var arPersprops = [];
            var arDelprops = [];
            <?foreach($_POST[PROPS][PERSONAL] as $key=>$val){?>
            arPersprops[<?=$key?>] = '<?=$val?>';
            <?}?>
            <?foreach($_POST[PROPS][DELIVERY] as $key=>$val){?>
            arDelprops[<?=$key?>] = '<?=$val?>';
            <?}?>
            <?}?>
            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: {
                    <?if($_POST[BTOCART]=="Y"){?>
                    PROPS: {
                        PERSONAL: arPersprops,
                        DELIVERY: arDelprops,
                    },
                    CUR_ORDER_STEP: "<?=$_POST[CUR_ORDER_STEP]?>",
                    ORDER_STEP: "<?=$_POST[ORDER_STEP]?>",
                    CUR_DELSYST: "<?=$_POST[CUR_DELSYST]?>",
                    CUR_PAYSYST: "<?=$_POST[CUR_PAYSYST]?>",
                    <?}?>
                    BITEM_ID: bitemid,
                    BITEM_QUANTITY: bitemquant,
                    ACTION: "CHQUANTINCART"
                },
                success: function(data)
                {
                    var arrR = data.split("/");
                    $(".cart_summ span").html(arrR[0] + " руб.");
                    $("#srcart span").html(arrR[1]);
                    if(arrR[1] > 99)
                    {
                        $("#srcart span").css({'width': '35px'});
                    }
                    else
                    {
                        $("#srcart span").css({'width': '20px'});
                    }
                }
            });
        }

        $(".l_minus").click(function()
        {
            if($(this).parent().find(".l_count").val() > 1)
            {
                $(this).parent().find(".l_count").val($(this).parent().find(".l_count").val() - 1);

                if($(this).parent().find(".l_count_top").val() > 99)
                {
                    $(this).parent().find(".l_count_top").css({'width': '35px'})
                }
                else
                {
                    $(this).parent().find(".l_count_top").css({'width': '20px'})
                }
                if(Number($('#srcart span').html()) > 99)
                {
                    $("#srcart span").css({'width': '35px'});
                }
                else
                {
                    $("#srcart span").css({'width': '20px'});
                }
            }
        });
        $(".l_plus").click(function()
        {
            $(this).parent().find(".l_count").val(parseInt($(this).parent().find(".l_count").val(), 10) + 1);

            console.log($(this).parent().find(".l_count").val());

            if($(this).parent().find(".l_count_top").val() > 99)
            {
                $(this).parent().find(".l_count_top").css({'width': '35px'})
            }
            else
            {
                $(this).parent().find(".l_count_top").css({'width': '20px'})
            }
            if(Number($('#srcart span').html()) > 99)
            {
                $("#srcart span").css({'width': '35px'});
            }
            else
            {
                $("#srcart span").css({'width': '20px'});
            }
        });

        $('.l_count').change(function()
        {
            if($('.l_count').val() == 0)
            {
                $('.l_count').val(1);
            }
            console.log($(this).parent().find(".l_count").val());

            chQuant($(this).parent().parent().parent().parent().attr("item_id"), $(this).parent().find(".l_count").val());

            if($(this).parent().find(".l_count_top").val() > 99)
            {
                $(this).parent().find(".l_count_top").css({'width': '35px'})
            }
            else
            {
                $(this).parent().find(".l_count_top").css({'width': '20px'})
            }

        });
        $(".saddtocart").click(function()
        {
            var productID = $(this).attr('rel');
            var productNAME = $(this).parent().find("input[name='pname']").val();
            var productDESCRIPTION = $(this).parent().find("input[name='pdescripton']").val();
            var productPRICE = $(this).parent().find("input[name='pprice']").val();
            var productDISCPRICE = $(this).parent().find("input[name='pprice_disc']").val();
            var productPRICEVATRATE = $(this).parent().find("input[name='ppricevatrate']").val();
            var productQUANTITY = $(this).parent().find(".l_count").val();
            var productDETAILurl = $(this).parent().find("input[name='pdetpage']").val();

            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: {
                    PRODUCT_ID: productID,
                    PRODUCT_NAME: productNAME,
                    PRODUCT_DESCRIPTION: productDESCRIPTION,
                    PRODUCT_PRICE: productPRICE,
                    PRODUCT_DISCOUNT_PRICE: productDISCPRICE,
                    PRODUCT_VAT_RATE_PRICE: productPRICEVATRATE,
                    PRODUCT_QUANTITY: productQUANTITY,
                    PRODUCT_DETAIL_URL: productDETAILurl,
                    ACTION: "ADDTOCART"
                },
                success: function(data)
                {
                    $("#srcart span").html(data);
                    if(data > 99)
                    {
                        $("#srcart span").css({'width': '35px'});
                    }
                    else
                    {
                        $("#srcart span").css({'width': '20px'});
                    }
                    $(".itadded").fadeIn(600);
                    $(".itadded").fadeOut(900);
                }
            });
        });
        $(".sort_varius_list li").click(function()
        {
            $(".sort_varius_active").html($(this).html());
            $(".sort_varius_active").attr("sortby", $(this).attr("sortby"));
            $(".sort_varius_active").attr("sortdirection", $(this).attr("sortdirection"));
            $(".sort_varius_active").attr("sortid", $(this).attr("sortid"));

            var fp_checked = [];
            var i = 0;
            $(".f_promo input:checked").each(function()
            {
                fp_checked[i] = $(this).attr("name");
                ++i;
            });
            var arr_brands = [];
            i = 0;
            $(".f_b_active").each(function()
            {
                arr_brands[i] = $(this).attr("brandid");
                ++i;
            });
            $(".spreload").show();
            $(".spreload").html("<div class='preload_back'></div><img class='preload_img2' src='/img/preloader.gif' width='60' height='60' alt='preload' border='0'/>");
            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: {
                    CAT_SORT: $(this).attr("sortby"),
                    CAT_SORT_NAME: $(this).html(),
                    CAT_SORT_ID: $(this).attr("sortid"),
                    CAT_SORT_DIRECTION: $(this).attr("sortdirection"),
                    CAT_SHOW_TYPE: $(".sort_lt").find(".sort_active").attr('type'),
                    IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                    IBLOCK_TYPE: "<?=$arParams[IBLOCK_TYPE]?>",
                    SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                    SUBSECTION: "<?=$GLOBALS[subsection]?>",
                    <?if($_REQUEST[SALE]){?>
                    SALE:<?=$_REQUEST[SALE]?>,
                    <?}?>
                    FILTER: {
                        CHECKPROMO: fp_checked,
                        PRICE_FROM: $("#amount_from").val(),
                        PRICE_TO: $("#amount_to").val(),
                        BRANDS: arr_brands,
                    },
                    BIT_ARRFILTER:<?=json_encode($GLOBALS['arrFilter'])?>,
                    ACTION: "CHANGESORT"
                },
                success: function(data)
                {
                    $(".catcont").html(data);
                    $(".spreload").hide();
                }
            });
        });
        $(".sort_lt_c").click(function()
        {
            var fp_checked = [];
            var i = 0;
            $(".f_promo input:checked").each(function()
            {
                fp_checked[i] = $(this).attr("name");
                ++i;
            });
            var arr_brands = [];
            i = 0;
            $(".f_b_active").each(function()
            {
                arr_brands[i] = $(this).attr("brandid");
                ++i;
            });
            $(".spreload").show();
            $(".spreload").html("<div class='preload_back'></div><img class='preload_img2' src='/img/preloader.gif' width='60' height='60' alt='preload' border='0'/>");
            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: {
                    CAT_SORT: $(".sort_varius_active").attr("sortby"),
                    CAT_SORT_NAME: $(".sort_varius_active").html(),
                    CAT_SORT_ID: $(".sort_varius_active").attr("sortid"),
                    CAT_SORT_DIRECTION: $(".sort_varius_active").attr("sortdirection"),
                    CAT_SHOW_TYPE: $(this).attr('type'),
                    IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                    IBLOCK_TYPE: "<?=$arParams[IBLOCK_TYPE]?>",
                    SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                    SUBSECTION: "<?=$GLOBALS[subsection]?>",
                    <?if($_REQUEST[SALE]){?>
                    SALE:<?=$_REQUEST[SALE]?>,
                    <?}?>
                    FILTER: {
                        CHECKPROMO: fp_checked,
                        PRICE_FROM: $("#amount_from").val(),
                        PRICE_TO: $("#amount_to").val(),
                        BRANDS: arr_brands,
                    },
                    BIT_ARRFILTER:<?=json_encode($GLOBALS['arrFilter'])?>,
                    ACTION: "CHANGESORT"
                },
                success: function(data)
                {
                    $(".catcont").html(data);
                    $(".spreload").hide();
                }
            });
        });
        $(".catshowmore").click(function()
        {
            var fp_checked = [];
            var i = 0;
            $(".f_promo input:checked").each(function()
            {
                fp_checked[i] = $(this).attr("name");
                ++i;
            });
            var arr_brands = [];
            i = 0;
            $(".f_b_active").each(function()
            {
                arr_brands[i] = $(this).attr("brandid");
                ++i;
            });
            $(".spreload").show();
            $(".spreload").html("<div class='preload_back'></div><img class='preload_img' src='/img/preloader.gif' alt='preload' width='60' height='60' border='0'/>");

            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: {
                    CAT_SORT: $(".sort_varius_active").attr("sortby"),
                    CAT_SORT_NAME: $(".sort_varius_active").html(),
                    CAT_SORT_ID: $(".sort_varius_active").attr("sortid"),
                    CAT_SORT_DIRECTION: $(".sort_varius_active").attr("sortdirection"),
                    CAT_SHOW_TYPE: $(".sort_lt").find(".sort_active").attr('type'),
                    IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                    IBLOCK_TYPE: "<?=$arParams[IBLOCK_TYPE]?>",
                    SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                    SUBSECTION: "<?=$GLOBALS[subsection]?>",
                    top100: "<?=$_REQUEST['top100']?>",
                    <?if($_REQUEST[SALE]){?>
                    SALE:<?=$_REQUEST[SALE]?>,
                    <?}?>
                    FILTER: {
                        CHECKPROMO: fp_checked,
                        PRICE_FROM: $("#amount_from").val(),
                        PRICE_TO: $("#amount_to").val(),
                        BRANDS: arr_brands,
                    },
                    BIT_ARRFILTER:<?=json_encode($GLOBALS['arrFilter'])?>,
                    ELTSNUM: "<?=($arParams[PAGE_ELEMENT_COUNT]+20);?>",
                    ACTION: "CHANGESORT"
                },
                success: function(data)
                {
                    $(".catcont").html(data);
                    $(".spreload").hide();
                }
            });
        });
        $(".catshowall").click(function()
        {
            var fp_checked = [];
            var i = 0;
            $(".f_promo input:checked").each(function()
            {
                fp_checked[i] = $(this).attr("name");
                ++i;
            });
            var arr_brands = [];
            i = 0;
            $(".f_b_active").each(function()
            {
                arr_brands[i] = $(this).attr("brandid");
                ++i;
            });
            $(".spreload").show();
            $(".spreload").html("<div class='preload_back'></div><img class='preload_img' src='/img/preloader.gif' alt='preload' width='60' height='60' border='0'/>");
            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: {
                    CAT_SORT: $(".sort_varius_active").attr("sortby"),
                    CAT_SORT_NAME: $(".sort_varius_active").html(),
                    CAT_SORT_ID: $(".sort_varius_active").attr("sortid"),
                    CAT_SORT_DIRECTION: $(".sort_varius_active").attr("sortdirection"),
                    CAT_SHOW_TYPE: $(".sort_lt").find(".sort_active").attr('type'),
                    IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                    IBLOCK_TYPE: "<?=$arParams[IBLOCK_TYPE]?>",
                    SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                    SUBSECTION: "<?=$GLOBALS[subsection]?>",
                    top100: "<?=$_REQUEST['top100']?>",
                    <?if($_REQUEST[SALE]){?>
                    SALE:<?=$_REQUEST[SALE]?>,
                    <?}?>
                    FILTER: {
                        CHECKPROMO: fp_checked,
                        PRICE_FROM: $("#amount_from").val(),
                        PRICE_TO: $("#amount_to").val(),
                        BRANDS: arr_brands,
                    },
                    BIT_ARRFILTER:<?=json_encode($GLOBALS['arrFilter'])?>,
                    ELTSNUM: "<?echo $arResult[NAV_RESULT]->NavRecordCount;?>",
                    ACTION: "CHANGESORT"
                },
                success: function(data)
                {
                    $(".catcont").html(data);
                    $(".spreload").hide();
                }
            });
        });
        $(".sort_seach").click(function()
        {
            var nsearch_word = $(".catsearchfield input").val();
            var fp_checked = [];
            var arr_brands = [];

            $(".spreload").show();
            $(".spreload").html("<div class='preload_back'></div><img class='preload_img2' src='/img/preloader.gif' width='60' height='60' alt='preload' border='0'/>");
            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: {
                    CAT_SORT: $(".sort_varius_active").attr("sortby"),
                    CAT_SORT_NAME: $(".sort_varius_active").html(),
                    CAT_SORT_ID: $(".sort_varius_active").attr("sortid"),
                    CAT_SORT_DIRECTION: $(".sort_varius_active").attr("sortdirection"),
                    CAT_SHOW_TYPE: $(".sort_lt").find(".sort_active").attr('type'),
                    IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                    IBLOCK_TYPE: "<?=$arParams[IBLOCK_TYPE]?>",
                    SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                    SUBSECTION: "<?=$GLOBALS[subsection]?>",
                    <?if($_REQUEST[SALE]){?>
                    SALE:<?=$_REQUEST[SALE]?>,
                    <?}?>
                    FILTER: {
                        CHECKPROMO: fp_checked,
                        PRICE_FROM: $("#amount_from").val(),
                        PRICE_TO: $("#amount_to").val(),
                        BRANDS: arr_brands,
                        NAME: $(".catsearchfield input").val()
                    },
                    BIT_ARRFILTER:<?=json_encode($GLOBALS['arrFilter'])?>,
                    ELTSNUM: "<?echo $arResult[NAV_RESULT]->NavRecordCount;?>",
                    ACTION: "CHANGESORT"
                },
                success: function(data)
                {
                    $(".catcont").html(data);
                    $(".catsearchfield input").val(nsearch_word);
                    $(".spreload").hide();
                }
            });
        });
        $(".catsearchfield input").keyup(function(e)
        {
            var nsearch_word = $(".catsearchfield input").val();
            var fp_checked = [];
            var i = 0;
            $(".f_promo input:checked").each(function()
            {
                fp_checked[i] = $(this).attr("name");
                ++i;
            });
            var arr_brands = [];
            i = 0;
            $(".f_b_active").each(function()
            {
                arr_brands[i] = $(this).attr("brandid");
                ++i;
            });

            if($(".catsearchfield input").val().length > 2)
            {
                //alert($("#amount_from").val());
                $.ajax({
                    type: "POST",
                    url: "/ajax/ajax.php",
                    data: {
                        IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                        SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                        NAME: $(".catsearchfield input").val(),
                        <?if($_REQUEST[SALE]){?>
                        SALE:<?=$_REQUEST[SALE]?>,
                        <?}?>
                        FILTER: {
                            CHECKPROMO: fp_checked,
                            PRICE_FROM: $("#amount_from").val(),
                            PRICE_TO: $("#amount_to").val(),
                            BRANDS: arr_brands,
                        },
                        BIT_ARRFILTER:<?=json_encode($GLOBALS['arrFilter'])?>,
                        ACTION: "CHANGESEARCH"
                    },
                    success: function(data)
                    {

                        $(".catsearch_sub").html(data);
                        $(".catsearch_sub span").not(".catsearch_showall").click(function()
                        {
                            var search_word = $(this).html();
                            $(".catsearchfield input").val($(this).html());
                            $(".catsearch_sub span").remove();
                            $(".spreload").show();
                            $(".spreload").html("<div class='preload_back'></div><img class='preload_img2' src='/img/preloader.gif' width='60' height='60' alt='preload' border='0'/>");

                            $.ajax({
                                type: "POST",
                                url: "/ajax/ajax.php",
                                data: {
                                    CAT_SORT: $(".sort_varius_active").attr("sortby"),
                                    CAT_SORT_NAME: $(".sort_varius_active").html(),
                                    CAT_SORT_ID: $(".sort_varius_active").attr("sortid"),
                                    CAT_SORT_DIRECTION: $(".sort_varius_active").attr("sortdirection"),
                                    CAT_SHOW_TYPE: $(".sort_lt").find(".sort_active").attr('type'),
                                    IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                                    IBLOCK_TYPE: "<?=$arParams[IBLOCK_TYPE]?>",
                                    SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                                    SUBSECTION: "<?=$GLOBALS[subsection]?>",
                                    <?if($_REQUEST[SALE]){?>
                                    SALE:<?=$_REQUEST[SALE]?>,
                                    <?}?>
                                    FILTER: {
                                        CHECKPROMO: fp_checked,
                                        PRICE_FROM: $("#amount_from").val(),
                                        PRICE_TO: $("#amount_to").val(),
                                        BRANDS: arr_brands,
                                        NAME: $(this).html()
                                    },
                                    BIT_ARRFILTER:<?=json_encode($GLOBALS['arrFilter'])?>,
                                    ELTSNUM: "<?echo $arResult[NAV_RESULT]->NavRecordCount;?>",
                                    ACTION: "CHANGESORT"
                                },
                                success: function(data)
                                {
                                    $(".catcont").html(data);
                                    $(".catsearchfield input").val(search_word);
                                }
                            });
                        });
                        $(".catsearch_showall").click(function()
                        {
                            $(".catsearch_sub span").remove();
                            $(".spreload").show();
                            $(".spreload").html("<div class='preload_back'></div><img class='preload_img2' src='/img/preloader.gif' width='60' height='60' alt='preload' border='0'/>");

                            $.ajax({
                                type: "POST",
                                url: "/ajax/ajax.php",
                                data: {
                                    CAT_SORT: $(".sort_varius_active").attr("sortby"),
                                    CAT_SORT_NAME: $(".sort_varius_active").html(),
                                    CAT_SORT_ID: $(".sort_varius_active").attr("sortid"),
                                    CAT_SORT_DIRECTION: $(".sort_varius_active").attr("sortdirection"),
                                    CAT_SHOW_TYPE: $(".sort_lt").find(".sort_active").attr('type'),
                                    IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                                    IBLOCK_TYPE: "<?=$arParams[IBLOCK_TYPE]?>",
                                    SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                                    SUBSECTION: "<?=$GLOBALS[subsection]?>",
                                    <?if($_REQUEST[SALE]){?>
                                    SALE:<?=$_REQUEST[SALE]?>,
                                    <?}?>
                                    FILTER: {
                                        CHECKPROMO: fp_checked,
                                        PRICE_FROM: $("#amount_from").val(),
                                        PRICE_TO: $("#amount_to").val(),
                                        BRANDS: arr_brands,
                                        NAME: $(".catsearchfield input").val()
                                    },
                                    ELTSNUM: "<?echo $arResult[NAV_RESULT]->NavRecordCount;?>",
                                    ACTION: "CHANGESORT"
                                },
                                success: function(data)
                                {
                                    $(".catcont").html(data);
                                    $(".catsearchfield input").val(nsearch_word);
                                }
                            });
                        });
                    }
                });
            }

            if(e.which == 13)
            {
                $(".spreload").show();
                $(".spreload").html("<div class='preload_back'></div><img class='preload_img2' src='/img/preloader.gif' width='60' height='60' alt='preload' border='0'/>");
                $.ajax({
                    type: "POST",
                    url: "/ajax/ajax.php",
                    data: {
                        CAT_SORT: $(".sort_varius_active").attr("sortby"),
                        CAT_SORT_NAME: $(".sort_varius_active").html(),
                        CAT_SORT_ID: $(".sort_varius_active").attr("sortid"),
                        CAT_SORT_DIRECTION: $(".sort_varius_active").attr("sortdirection"),
                        CAT_SHOW_TYPE: $(".sort_lt").find(".sort_active").attr('type'),
                        IBLOCK_ID: "<?=$arParams[IBLOCK_ID]?>",
                        IBLOCK_TYPE: "<?=$arParams[IBLOCK_TYPE]?>",
                        SECTION_CODE: "<?=$arParams[SECTION_CODE]?>",
                        SUBSECTION: "<?=$GLOBALS[subsection]?>",
                        <?if($_REQUEST[SALE]){?>
                        SALE:<?=$_REQUEST[SALE]?>,
                        <?}?>
                        FILTER: {
                            CHECKPROMO: fp_checked,
                            PRICE_FROM: $("#amount_from").val(),
                            PRICE_TO: $("#amount_to").val(),
                            BRANDS: arr_brands,
                            NAME: $(".catsearchfield input").val()
                        },
                        BIT_ARRFILTER:<?=json_encode($GLOBALS['arrFilter'])?>,
                        ELTSNUM: "<?echo $arResult[NAV_RESULT]->NavRecordCount;?>",
                        ACTION: "CHANGESORT"
                    },
                    success: function(data)
                    {
                        $(".catcont").html(data);
                        $(".catsearchfield input").val(nsearch_word);
                        $(".spreload").hide();
                    }
                });
            }
        });

    });
    function pause(n)
    {
        today = new Date()
        today2 = today
        while((today2 - today) <= n)
        {
            today2 = new Date()
        }
    }
    $(document).ready(function()
    {
        $(".l_minus_block").click(function()
        {
            if($(this).parent().find(".l_count").val() > 1)
            {
                $(this).parent().find(".l_count").val($(this).parent().find(".l_count").val() - 1);

                if($(this).parent().find(".l_count").val() > 99)
                {
                    $(this).parent().find(".l_count").css({'width': '35px'})
                }
                else
                {
                    $(this).parent().find(".l_count").css({'width': '20px'})
                }

                if(Number($('#srcart span').html()) > 99)
                {
                    $("#srcart span").css({'width': '35px'});
                }
                else
                {
                    $("#srcart span").css({'width': '20px'});
                }
            }
        });
        $(".l_plus_block").click(function()
        {
            $(this).parent().find(".l_count").val(parseInt($(this).parent().find(".l_count").val(), 10) + 1);

            if($(this).parent().find(".l_count").val() > 99)
            {
                $(this).parent().find(".l_count").css({'width': '35px'})
            }
            else
            {
                $(this).parent().find(".l_count").css({'width': '20px'})
            }

            if(Number($('#srcart span').html()) > 99)
            {
                $("#srcart span").css({'width': '35px'});
            }
            else
            {
                $("#srcart span").css({'width': '20px'});
            }

        });
        $('.l_card').hover(
            function()
            {
                pause(100);
                var $vTest = $(this).position();
                var $block_top_product = $(this).find('.block_top_product');
                var $l_info_product_zip = $(this).find('.l_info_product_zip');
                var $l_info_product_unzip = $(this).find('.l_info_product_unzip');
                if($vTest.left < 530)
                {
                    $block_top_product.css({
                        width: "410px",
                        zIndex: "5",
                        border: "3px solid #d4d4d4"
                    });
                    $l_info_product_zip.css({
                        border: "none"
                    });
                }
                else
                {

                    $block_top_product.css({
                        width: "410px",
                        zIndex: "5",
                        border: "3px solid #d4d4d4",
                        transform: "translate(-225px,0)"
                    });
                    $l_info_product_zip.css({
                        border: "none",
                        float: "right"
                    });
                    $l_info_product_unzip.css({
                        float: "left",
                        display: "block"
                    });

                }
                $l_info_product_unzip.show(150);
            },

            function()
            {
                var $vTest = $(this).position();
                var $block_top_product = $(this).find('.block_top_product');
                var $l_info_product_zip = $(this).find('.l_info_product_zip');
                var $l_info_product_unzip = $(this).find('.l_info_product_unzip');
                $l_info_product_unzip.css({display: "none"});
                if($vTest.left < 530)
                {
                    $block_top_product.css({
                        width: "185px",
                        zIndex: "1",
                        border: "1px solid #d4d4d4"
                    });
                }
                else
                {
                    $block_top_product.css({
                        width: "185px",
                        zIndex: "1",
                        border: "1px solid #d4d4d4",
                        transform: "translate(-225px,1)"
                    });
                    $l_info_product_zip.css({
                        float: "left"
                    });
                    $l_info_product_unzip.css({
                        float: "right",
                        display: "none"
                    });
                }
            }
        );

        $(".l_prev_more").fancybox();
    });
</script>

<? if($_POST[CAT_SHOW_TYPE])
{
    $catshowtype = $_POST[CAT_SHOW_TYPE];
}
else
{
    $catshowtype = "CARD";
}
if($_POST[CAT_SORT])
{
    $catsortname = $_POST[CAT_SORT_NAME];
    $casortid = $_POST[CAT_SORT_ID];
    $catsortby = $_POST[CAT_SORT];
    $catsortdirection = $_POST[CAT_SORT_DIRECTION];
}
else
{
    $catsortname = "Наименованию а-я";
    $casortid = "sort1";
    $catsortby = "NAME";
    $catsortdirection = "ASC";
}
?>


<?
/*
 * Получаем категории раздела на глубину 1
 * */
$bit_group = true;
$rsParentSection = CIBlockSection::GetByID($GLOBALS['SECTION_POS']['ID']);
if($arParentSection = $rsParentSection->GetNext())
{
    $arFilter = array(
        'IBLOCK_ID'     => $arParentSection['IBLOCK_ID'],
        '>LEFT_MARGIN'  => $arParentSection['LEFT_MARGIN'],
        '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],
        '>DEPTH_LEVEL'  => $arParentSection['DEPTH_LEVEL']
    ); // выберет потомков без учета активности
    $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);
    $section_Result = array();
    while($arSect = $rsSect->GetNext())
    {
        $arSect['NOBASKET'] = 'Y';
        $arSect['DETAIL_PAGE_URL'] = $arSect['SECTION_PAGE_URL'];

        $arSelect = Array('PREVIEW_PICTURE', 'DETAIL_PICTURE', 'PROPERTY_CML2_MANUFACTURER');
        //$arSelect = Array();
        $arFilter = array_merge(Array(
            "IBLOCK_ID"       => $arSect['IBLOCK_ID'],
            "SECTION_ID"      => $arSect['ID'],
            "ACTIVE_DATE"     => "Y",
            "ACTIVE"          => "Y",
            "!DETAIL_PICTURE" => false
        ), $GLOBALS['arrFilter']);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 1), $arSelect);
        $add_section = false;
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            if($arFields['DETAIL_PICTURE'])
            {
                $rsFile = CFile::GetByID($arFields['DETAIL_PICTURE']);
            }
            else
            {
                $rsFile = CFile::GetByID($arFields['PREVIEW_PICTURE']);
            }

            $arFile = $rsFile->Fetch();

            $arFileTmp = CFile::ResizeImageGet($arFile, array(
                "width"  => 145,
                "height" => 145
            ), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arFilter);

            $arFile['PREVIEW_WIDTH'] = $arFileTmp["width"];
            $arFile['PREVIEW_HEIGHT'] = $arFileTmp["height"];

            $arFile['SRC'] = $arFileTmp['src'];
            $arSect['PREVIEW_PICTURE'] = $arSect['DETAIL_PICTURE'] = $arFile;
            $add_section = true;
        }

        /*Заносим в рабочий массив*/
        if($add_section)
            $section_Result[] = $arSect;
    }

    $iCnt = CIBlockElement::GetList(array(), array(
        "IBLOCK_ID"  => $arParentSection['IBLOCK_ID'],
        "ACTIVE"     => "Y",
        "SECTION_ID" => $GLOBALS['SECTION_POS']['ID']
    ), array());
    if($GLOBALS['arrFilter'])
    {
    }
    else
    {
        if(($section_Result) && ($iCnt <= 0))
        {
            //$arResult['ITEMS'] = array_merge($section_Result, $arResult['ITEMS']);
            $arResult['ITEMS'] = $section_Result;
            $bit_group = false;
        }
        else if(($iCnt > 0))
        {
            $arResult['ITEMS'] = array_merge($section_Result, $arResult['ITEMS']);
        }
    }
}
/*инициализируем настройки для отображения*/
$BIT_arParams = array(
    'bit_count_element_in_line' => 4,
    'bit_margin_right_element'  => 15,
);

if($_REQUEST['top100'] == 'Y')
{
    $BIT_arParams['bit_count_element_in_line'] = 5;
    $BIT_arParams['bit_margin_right_element'] = 35;
}

?>
<div class="listproduct" <?=($_REQUEST['top100'] == 'Y') ? 'style="margin-left: 0px !important;"' : ''?>><?
    ?>
    <div style="display: table;width:100%;"><?
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

                if($catshowtype == "CARD")
                {
                    $style_tmp = '';
                    if(($key + 1) / $BIT_arParams['bit_count_element_in_line'] - floor(($key + 1) / $BIT_arParams['bit_count_element_in_line']) != 0)
                    {
                        $style_tmp = 'margin-right:' . $BIT_arParams['bit_margin_right_element'] . 'px';
                    }
                    $class_tmp = 'l_card';
                    if($arItem[NOBASKET] == "Y")
                    {
                        $class_tmp = 'l_card_no_product';
                    }
                    ?>
                    <div class="<?=$class_tmp;?>" style="<?=$style_tmp;?>"><?
                        include($_SERVER['DOCUMENT_ROOT'] . "/include_arear/viwer_element/viwer_element.php");
                        ?></div>
                    <?
                }
                elseif($catshowtype = "LIST")
                {
                    ?>
                    <div class="l_list_wrap">
                    <div class="l_list">
                        <div class="l_list_r">
                            <div class="l_list_price">
                                <div style="text-align:center;margin: 7px 0;"><?
                                    if($arItem['MIN_PRICE']['VALUE'] != $arItem['MIN_PRICE']['DISCOUNT_VALUE'])
                                    {
                                        ?>
                                        <span style="text-decoration: line-through;"><?=$arItem['MIN_PRICE']['PRINT_VALUE']?></span><?
                                    }
                                    ?><span><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></span>
                                </div><?
                                if($arItem[NOBASKET] != "Y")
                                {
                                    ?>
                                    <div class="">Есть в наличии</div><?
                                }
                                ?></div>
                            <div class="l_list_basc">
                                <? if($arItem[NOBASKET] != "Y")
                                {
                                    ?>
                                    <div style="display: table;margin: 0 auto;margin-top: 15px;margin-bottom: 6px;">
                                        <div class="l_minus">–</div>
                                        <input class="l_count"/>

                                        <div class="l_plus">+</div>
                                    </div>
                                    <input type="hidden" value="<?=$arItem[NAME]?>" name="pname"/>
                                    <input type="hidden" value="<?=$arItem['MIN_PRICE']['VALUE'] - $arItem['MIN_PRICE']['DISCOUNT_VALUE']?>" name="pprice_disc"/>
                                    <input type="hidden" value="<?=$arItem['MIN_PRICE']['DISCOUNT_VALUE']?>" name="pprice"/><?
                                    foreach($arItem['PRICES'] as $vat)
                                    {
                                        ?>
                                        <input type="hidden" value="<?=$vat['PRINT_VATRATE_VALUE']?>" name="ppricevatrate"/><?
                                    }
                                    ?>
                                    <input type="hidden" value="<? echo $arItem['DETAIL_PAGE_URL']; ?>" name="pdetpage"/>
                                    <input type="hidden" name='pdescripton' value="<?=$arItem[PREVIEW_TEXT]?>"/>
                                <div style="text-align: center;font-weight: bold;cursor: pointer;text-decoration: underline;" class="saddtocart" rel="<?=$arItem[ID]?>">
                                        В КОРЗИНУ</div><?
                                }
                                else
                                {
                                    ?>
                                    <div style="text-align: center; margin-top:15px;">ГРУППА ТОВАРОВ</div><?
                                }
                                ?></div>
                        </div>
                        <div class="l_list_l">
                            <a href="<? echo $arItem['DETAIL_PAGE_URL']; ?>">
                                <div class="l_list_img" style="background-image: url(<?=$picture?>);">
                                    <? if($arItem['MIN_PRICE']['VALUE'] != $arItem['MIN_PRICE']['DISCOUNT_VALUE'])
                                    {
                                        ?><i class="l_discount">
                                        <input type="hidden" value="<?=$arItem['discount']['ID']?>"/></i><?
                                    }
                                    ?></div>
                            </a>

                            <div class="l_list_desc">
                                <a href="<? echo $arItem['DETAIL_PAGE_URL']; ?>">
                                    <div style="font-weight: bold;color: #878787;"><? echo $arItem[PROPERTIES][CML2_MANUFACTURER][VALUE]; ?></div>
                                    <div style="font-weight: bold;"><?=$arItem[NAME]?></div>
                                    <p><?
                                        if(strlen($arItem[DETAIL_TEXT]) > 0)
                                        {
                                            echo substr($arItem["DETAIL_TEXT"], 0, 200);
                                            if(strlen($arItem["DETAIL_TEXT"]) > 200)
                                            {
                                                echo "...";
                                            }
                                        }
                                        else
                                        {
                                            echo substr($arItem["PREVIEW_TEXT"], 0, 200);
                                            if(strlen($arItem["PREVIEW_TEXT"]) > 200)
                                            {
                                                echo "...";
                                            }
                                        }
                                        ?></p>
                                </a>
                            </div>
                        </div>
                    </div>
                    </div><?
                }
            endforeach;
        }
        else
        {
            ?><p>К сожалению товаров удовлетворяющих условиям фильтрации не
            найдено. Попробуйте изменить параметры фильтрации!</p><?
        }
        ?></div><?
    if($arResult[NAV_RESULT]->NavRecordCount != $arResult[NAV_RESULT]->NavPageSize && $arResult[NAV_RESULT]->NavRecordCount > $arResult[NAV_RESULT]->NavPageSize && $bit_group)
    {
        ?>
        <div class="cat_nav">
            <div class="catshowmore">ЗАГРУЗИТЬ ЕЩЕ</div>
            <div class="catshowall">ВЫВЕСТИ ВЕСЬ СПИСОК ТОВАРОВ</div>
        </div><?
    }
    ?>
    <div class="itadded" style="display:none;position:fixed; width: 100%;  top:50%; left: 0; margin-top: -100px;background: rgba(52, 52, 52, 0.83);z-index: 20;color: #fff;">
        <h2 style="color: #fff;">Товар добавлен в корзину</h2></div>
    <div class="spreload" style="display:none;"></div>
</div>
<div style="clear:both;"></div>
<script>
    $(document).ready(function()
    {

        $('.l_discount').click(function()
        {


            var sale_id = $(this).find('input').val()


            $('.sale_backg').fadeIn(200);
            $('.bodycontent').css('overflow', 'hidden');

            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: {
                    ID: sale_id,
                    ACTION: "SHOWSALE"
                },
                success: function(data)
                {
                    $(".sale_backg").html(data);
                }
            });


        })

    })
</script>








