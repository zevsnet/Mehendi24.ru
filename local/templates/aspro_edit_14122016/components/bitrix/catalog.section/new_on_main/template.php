<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<? if (count($arResult['ITEMS']) > 0): ?>
    <script>
        $(function()
        {
            $(".l_minus").click(function()
            {
                if($(this).parent().find(".l_count").val() > 1)
                {
                    $(this).parent().find(".l_count").val($(this).parent().find(".l_count").val() - 1);
                }
            });
            $(".l_plus").click(function()
            {
                $(this).parent().find(".l_count").val(parseInt($(this).parent().find(".l_count").val(), 10) + 1);
            });
            $(".saddtocart").click(function()
            {
                var productID = $(this).attr('rel');
                var productNAME = $(this).parent().find("input[name='pname']").val();
                var productDESCRIPTION = $(this).parent().find("input[name='pdescripton']").val();
                var productPRICE = $(this).parent().find("input[name='pprice']").val();
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
                        PRODUCT_QUANTITY: productQUANTITY,
                        PRODUCT_DETAIL_URL: productDETAILurl,
                        ACTION: "ADDTOCART"
                    },
                    success: function(data)
                    {
                        $("#srcart span").html(data);
                        $(".itadded").fadeIn(600);
                        $(".itadded").fadeOut(900);
                    }
                });
            });
        });
    </script>


    <div class="karousel_top" style="position: relative;">

        <div class="">

            <div class="karousel_top_wrap">
                <?

                if ($arResult['ITEMS']) {
                    foreach ($arResult['ITEMS'] as $key => $arItem):
                        if (strlen($arItem['PREVIEW_PICTURE']['SRC']) > 0) {
                            $picture = $arItem['PREVIEW_PICTURE']['SRC'];
                        } else {
                            $picture
                                = "/bitrix/templates/koloristika_org/img/nophotomin.png";
                        }
                        ?>

                        <div class="l_card news-on-main ">
                            <?
                            include($_SERVER['DOCUMENT_ROOT']."/include_arear/viwer_element/viwer_element.php");
                            ?>
                        </div>
                    <?endforeach;
                } ?>
            </div>

        </div>


    </div>
<? endif; ?>