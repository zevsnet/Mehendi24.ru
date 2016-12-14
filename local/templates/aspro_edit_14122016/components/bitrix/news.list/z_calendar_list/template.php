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
                $start = substr($time, 0, strpos($time, ':'));

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
                        <th scope="row" style="text-align: left"><?= $arItem['DISPLAY_ACTIVE_FROM'] ?></th>

                        <? $tmp_i = 0;
                        foreach ($arItem['PROPERTIES']['Z_END_TIME']['VALUE'] as $key => $time) { ?>
                            <?
                            $start = $time;// substr($time, 0, strpos($time, '-'));
                            if (!strpos($time, ':')) {
                                $time_value = $start . ':00';
                            } else {
                                $time_value = $start . '';
                            }
                            if ($arItem['DISPLAY_ACTIVE_FROM'] == date("d.m.Y")) {
                            if (date("H") + 7 > $time_value) {
                                ?>
                                <script>
                                    var z_date = {
                                        ACTION: 'DELETETIME',
                                        IDELEMENT: '<?= $arItem['ID'] ?>',
                                        IDTIME: '<?= $arItem['PROPERTIES']['Z_END_TIME']['PROPERTY_VALUE_ID'][$tmp_i] ?>',
                                        DATE: '<?= $arItem['DISPLAY_ACTIVE_FROM'] ?>',
                                        TIME: '<?= $start ?>'
                                    };

                                    $.ajax({
                                        url: "/ajax/service.php",
                                        data: z_date,
                                        type: "GET",
                                        success: function (data) {

                                        }
                                    });
                                </script>
                            <?
                            }
                            else
                            {
                            ?>
                                <td id_element="<?= $arItem['ID'] ?>"
                                    id_time="<?= $arItem['PROPERTIES']['Z_END_TIME']['PROPERTY_VALUE_ID'][$tmp_i] ?>"
                                    date="<?= $arItem['DISPLAY_ACTIVE_FROM'] ?>" time="<?= $start ?>"
                                    id="<?= str_replace('.', '', $arItem['DISPLAY_ACTIVE_FROM']) . '_' . $start ?>"><?= $time_value ?></td>
                            <?
                            }
                            }
                            else{
                            ?>
                                <td id_element="<?= $arItem['ID'] ?>"
                                    id_time="<?= $arItem['PROPERTIES']['Z_END_TIME']['PROPERTY_VALUE_ID'][$tmp_i] ?>"
                                    date="<?= $arItem['DISPLAY_ACTIVE_FROM'] ?>" time="<?= $start ?>"
                                    id="<?= str_replace('.', '', $arItem['DISPLAY_ACTIVE_FROM']) . '_' . $start ?>"><?= $time_value ?></td>
                                <?
                            }
                            $tmp_i++;
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
        $('.z_table tr td').click(function () {
            if (id_td_activity != 0)
                $('#' + id_td_activity).removeClass('activity');
            $(this).addClass('activity');
            id_td_activity = $(this).attr('id');
        });
    });
</script>