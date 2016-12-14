<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
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
<div class="feature-content">
    <div class="row">
        <?

        $arrDate = array();
        $arrTime = array();
        foreach ($arResult['ITEMS'] as $arItem) {
            $arrDate[] = $arItem['DISPLAY_ACTIVE_FROM'];
            foreach ($arItem['PROPERTIES']['Z_END_TIME']['VALUE'] as $time) {
                $start = substr($time, 0, strpos($time, '-'));

                if (!in_array($start, $arrTime)) {
                    $arrTime[] = $start;
                }

            }
        }

        sort($arrTime);


        $max_col = 0;
        foreach ($arResult['ITEMS'] as $arItem) {
            if ($max_col > count($arItem['PROPERTIES']['Z_END_TIME']['VALUE'])) {
                $max_col = count($arItem['PROPERTIES']['Z_END_TIME']['VALUE']);
            }
        }
        ?>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <table class="z_table">
                <tbody>
                <?
                foreach ($arResult['ITEMS'] as $arItem) {
                    ?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                    <tr>
                        <th scope="row" style="text-align: left"><?= $arItem['DISPLAY_ACTIVE_FROM'] ?></
                        >

                        <? $tmp_i = 0;
                        foreach ($arItem['PROPERTIES']['Z_END_TIME']['VALUE'] as $time) { ?>
                            <?
                            $start = substr($time, 0, strpos($time, '-'));
                            $finish = substr($time, strpos($time, '-') + 1, strlen($time));
                            $time_value = $start . ':00';
                            ?>
                            <td date= "<?=$arItem['DISPLAY_ACTIVE_FROM']?>" time="<?=$start?>" id="<?=str_replace('.','',$arItem['DISPLAY_ACTIVE_FROM']) . '_' . $start ?>"><?= $time_value ?></td>
                            <? $tmp_i++;
                        }

                        while ($tmp_i <= $max_col) {
                            echo '<td></td>';
                            $tmp_i++;
                        }
                        ?>

                    </tr>
                    <?
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        var id_td_activity = 0;
        $('.z_table tr td').click(function(){
            if(id_td_activity !=0)
                $('#'+id_td_activity).removeClass('activity');
            $(this).addClass('activity');
            id_td_activity = $(this).attr('id');


			//$(this).each(function() {
				//if($(this).attr('class') == 'activity'){
					//var date = $(this).attr('date');
					//var time = $(this).attr('time');
					//$('#step_3').show();
				//}
			//});
			 
            //console.log($(this).attr('id'));
        });
    });
</script>











<? return; ?>
<div class="feature-content">
    <div class="row">
        <?
        $arrDate = array();
        $arrTime = array();
        foreach ($arResult['ITEMS'] as $arItem) {
            $arrDate[] = $arItem['DISPLAY_ACTIVE_FROM'];
            foreach ($arItem['PROPERTIES']['Z_END_TIME']['VALUE'] as $time) {
                $start = substr($time, 0, strpos($time, '-'));
                $finish = substr($time, strpos($time, '-') + 1, strlen($time));
                if (!in_array($start, $arrTime)) {
                    $arrTime[] = $start;
                }
                if (!in_array($finish, $arrTime)) {
                    $arrTime[] = $finish;
                }
            }
        }

        sort($arrTime);

        ?>
        <table class="table">
            <thead>
            <tr>
                <th></th>
                <? foreach ($arrTime as $element) {
                    ?>
                    <th><?= $element ?>:00</th><?
                } ?>
                <th></th>
            </tr>
            </thead>
            <tbody>

            <?
            $i = 0;
            foreach ($arResult['ITEMS'] as $arItem) { ?>
                <tr>
                    <th scope="row"><?= $arrDate[$i] ?></th>
                    <?
                    $test_start = 0;
                    $pos_start_old = 0;
                    $pos_finish_old = 0;
                    foreach ($arItem['PROPERTIES']['Z_END_TIME']['VALUE'] as $time) {
                        $start = substr($time, 0, strpos($time, '-'));
                        $finish = substr($time, strpos($time, '-') + 1, strlen($time));
                        $pos_start = array_search($start, $arrTime);
                        $pos_finish = array_search($finish, $arrTime);


                        $tmp_i = $pos_start_old;
                        while ($tmp_i < count($arrTime)) {
                            if ($arrTime[$tmp_i] != $start) {
                                echo '<td></td>';
                            } else {
                                break;
                            }
                            $tmp_i++;
                        }
                        $pos_start_old = $pos_finish + 1;

                        ?>
                        <td colspan="3<?//=$arrTime[$pos_finish]-$arrTime[$pos_start]+1
                        ?>"><?
                            _::d($time);
                            _::d($arrTime);
                            _::d($pos_start);
                            _::d($pos_finish);
                            _::d($pos_start_old);
                            ?></td>
                        <?

                    }
                    ?>
                </tr>
                <? $i++;
            } ?>
            </tbody>
        </table>
    </div>
</div>