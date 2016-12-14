
<div class="catalog_sort">
    <div style="">
        <div class="catsearchfield">
            <input type="text"/>
            <div class="sort_seach"></div>
            <div class="catsearch_sub"></div>
        </div><?
        ?><div><?
			?><div class="sort_text">Cортировать по:</div><?
			?><div class="sort_varius"><?
				?><div class="sort_varius_active" sortby='<?= $catsortby ?>' sortdirection="<?= $catsortdirection ?>"
                     sortid="<?= $casortid ?>"><?= $catsortname ?><?
				?></div>
                <div class="sort_varius_list">
                    <ul>
                        <li sortby='NAME' sortdirection="ASC" sortid="sort1" <? if ($casortid
                        == "sort1"){ ?>style="display:none;"<? } ?>>Наименованию
                            а-я
                        </li>
                        <li sortby='NAME' sortdirection="DESC" sortid="sort2" <? if ($casortid
                        == "sort2"){ ?>style="display:none;"<? } ?>>Наименованию
                            я-а
                        </li>
                        <li sortby='catalog_PRICE_2' sortdirection="DESC" sortid="sort3" <? if ($casortid
                        == "sort3"){ ?>style="display:none;"<? } ?>>Убыванию
                            цены
                        </li>
                        <li sortby='catalog_PRICE_2' sortdirection="ASC" sortid="sort4" <? if ($casortid
                        == "sort4"){ ?>style="display:none;"<? } ?>>Возрастанию
                            цены
                        </li>
                    </ul>
                </div>
            </div>
            <div class="sort_lt">
                <div class="sort_lt_c" type="CARD">
                    <? if ($catshowtype== "CARD") :?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/ico02_1.png" alt="">
                    <?else:?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/ico02_2.png" alt="">
                    <?endif;?>
                </div>
                <?/*<div class="sort_lt_c" type="LIST">
                    <? if ($catshowtype== "LIST") :?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/ico03_1.png" alt="">
                    <?else:?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/ico03_2.png" alt="">
                    <?endif;?>
                </div>*/?>
            </div>


        </div>
    </div>
</div>
