<div class="catalog_sort">
    <div style="float: right; margin-right: 10px">
        <div class="sort_text">Cортировать по:</div>
        <div class="sort_varius">
            <div class="sort_varius_active" sortby='<?=$catsortby?>' sortdirection="<?=$catsortdirection?>"
                 sortid="<?=$casortid?>"><?=$catsortname?><?
                ?></div>
            <div class="sort_varius_list">
                <ul>
                    <li sortby='NAME' sortdirection="ASC" sortid="sort1" <? if($casortid
                    == "sort1")
                    { ?>style="display:none;"<? } ?>>Наименованию
                        а-я
                    </li>
                    <li sortby='NAME' sortdirection="DESC" sortid="sort2" <? if($casortid
                    == "sort2")
                    { ?>style="display:none;"<? } ?>>Наименованию
                        я-а
                    </li>
                    <li sortby='catalog_PRICE_2' sortdirection="DESC" sortid="sort3" <? if($casortid
                    == "sort3")
                    { ?>style="display:none;"<? } ?>>Убыванию
                        цены
                    </li>
                    <li sortby='catalog_PRICE_2' sortdirection="ASC" sortid="sort4" <? if($casortid
                    == "sort4")
                    { ?>style="display:none;"<? } ?>>Возрастанию
                        цены
                    </li>
                </ul>
            </div>
        </div>
        <div class="sort_lt">
            <div class="sort_lt_c" type="CARD">
                <? if($catshowtype== "CARD") : ?>
                <img src="<?=SITE_TEMPLATE_PATH?>/img/ico02_1.png" alt="">
                <? else: ?>
                <img src="<?=SITE_TEMPLATE_PATH?>/img/ico02_2.png" alt="">
                <? endif; ?>
            </div>
            <? /*<div class="sort_lt_c" type="LIST">
                    <? if ($catshowtype== "LIST") :?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/ico03_1.png" alt="">
                    <?else:?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/ico03_2.png" alt="">
                    <?endif;?>
                </div>*/ ?>
        </div>
        <div class="sort_open_seach fa fa-search"></div>
        <div class="catsearchfield">
            <input class="z_input" type="text"/>

            <div class="sort_seach" ></div>
            <div class="catsearch_sub"></div>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        z_timer_open_search = function()
        {
            if($('.z_input').val().length > 1)
            {
                $('.sort_open_seach').hide();
                $('.catsearchfield').show();
            }
        }
        setTimeout(z_timer_open_search,500);
        $('.sort_open_seach').click(function(){
            $(this).hide();
            $('.catsearchfield').show();
        });
    });
</script>