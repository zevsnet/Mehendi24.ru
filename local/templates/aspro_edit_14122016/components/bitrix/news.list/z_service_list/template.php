<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
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

<h2></h2>
<p></p>
<table class="table table-condensed service_list_for_js">
    <thead>
    <tr>
        <th>Название</th>
        <th>Цена</th>
    </tr>
    </thead>
    <tbody>
    <? foreach($arResult["ITEMS"] as $arItem): ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <tr class="service_list" id="<?=$this->GetEditAreaId($arItem['ID']);?>" dataid="<?=$arItem['ID']?>">
            <td style="text-align: center">
                <input class="checkbox" id="input_<?=$arItem['ID']?>" type="checkbox" name="service" value="<?=$arItem['ID']?>" style="display: none">
                <label for="input_<?=$arItem['ID']?>"><span></span></label>
            </td>
            <td><?=$arItem["NAME"]?></td>
            <? /*<td><?
					if(CModule::IncludeModule("catalog"))
					{
						//Дёргаем цену и элемента с id - $id
						$ar_price = GetCatalogProductPrice($arItem['ID'], 2);
						//Конвертируем валюту в рубли, вам может и не понадобится
						if(isset($ar_price['CURRENCY']) && $ar_price['CURRENCY']!="RUB") $ar_price['PRICE'] = CCurrencyRates::ConvertCurrency($ar_price['PRICE'], $ar_price["CURRENCY"], "RUB");
						//В переменной $price теперь содержится цена товара
						$price = $ar_price['PRICE'];
					}?>

					<?=$price.'руб.'?>
				</td>*/ ?>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>
</div>

<script>
    /*$(document).ready(function()
    {

        $('.service_list_for_js .service_list').on("click", (function()
        {
            //$(this).find('input').click();
            var check = $(this).find('input');
            if(!check.prop("checked"))
            {
                check.prop("checked", true);
            }
            else
            {
                check.prop("checked", false);
            }

            //$( this ).trigger( "click" );
            var trElements = document.getElementsByClassName('service_list');
            var inputElements = $(trElements).find('input');

            var f_activitu = false;
            for(var i = 0; inputElements[i]; ++i)
            {
                if(inputElements[i].checked)
                {
                    f_activitu = true;
                }
            }
            if(f_activitu)
            {
                $('#step_4').show();
                $('#step_4').animate({'opacity': '1'}, 600);
            }
            else
            {
                $('#step_4').hide();
                $('#step_4').animate({'opacity': '0'}, 600);
            }
        }));

        $('label').on("click", function()
        {
            $('#' + $(this).attr('for')).click();
        });
    });*/
</script>	