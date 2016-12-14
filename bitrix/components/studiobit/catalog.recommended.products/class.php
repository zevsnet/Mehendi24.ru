<?php
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\SystemException as SystemException;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CBitrixComponent::includeComponentClass("bitrix:catalog.viewed.products");

Loc::loadMessages(__FILE__);

class CCatalogRecommendedProductsComponent extends CCatalogViewedProductsComponent
{
	/**
	 * @override
	 */
	public function onIncludeComponentLang()
	{
		parent::onIncludeComponentLang();
		$this->includeComponentLang(basename(__FILE__));
	}
	/**
	 * @param $params
	 * @override
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);
		if(!isset($params["CACHE_TIME"]))
			$params["CACHE_TIME"] = 86400;

		if(isset($params['ID']))
			$params['ID'] = (int)$params["ID"];
		else
			$params['ID'] = -1;

		if(isset($params['CODE']))
			$params['CODE'] = trim($params['CODE']);
		else
			$params['CODE'] = '';

		if(isset($params['IBLOCK_ID']))
			$params['IBLOCK_ID'] = (int)$params['IBLOCK_ID'];
		else
			$params['IBLOCK_ID'] = -1;

		if(!isset($params['PROPERTY_LINK']) || !strlen($params['PROPERTY_LINK']) )
		{
			$params['PROPERTY_LINK'] = 'RECOMMEND';
		}
		else
		{
			$params['PROPERTY_LINK'] = trim($params['PROPERTY_LINK']);
		}

		if(!isset($params['OFFERS_PROPERTY_LINK']) || !strlen($params['OFFERS_PROPERTY_LINK']) )
		{
			$params['OFFERS_PROPERTY_LINK'] = 'RECOMMEND';
		}
		else
		{
			$params['OFFERS_PROPERTY_LINK'] = trim($params['OFFERS_PROPERTY_LINK']);
		}

		return $params;
	}


	/**
	 * @override
	 *
	 * @return bool
	 */
	protected function extractDataFromCache()
	{
		if($this->arParams['CACHE_TYPE'] == 'N')
			return false;

		global $USER;

		return !($this->StartResultCache(false, $USER->GetGroups()));
	}

	protected function prepareData()
	{
		if ($this->arParams['ID'] <= 0)
		{
			CIBlockFindTools::getElementID (
				$this->arParams["ID"],
				$this->arParams["CODE"],
				false,
				false,
				array(
					"IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
					"IBLOCK_LID" => SITE_ID,
					"IBLOCK_ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"ACTIVE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
					"MIN_PERMISSION" => 'R'
				)
			);
		}
		if ($this->arParams['ID'] <= 0)
		{
			throw new SystemException(Loc::getMessage("CATALOG_RECOMMENDED_PRODUCTS_COMPONENT_PRODUCT_ID_REQUIRED"));
		}
		{
			parent::prepareData();
		}
	}

	protected function putDataToCache()
	{
		$this->endResultCache();
	}

	protected function abortDataCache()
	{
		$this->AbortResultCache();
	}


	/**
	 * Get Linked product ids
	 *
	 * @param $productId
	 * @param $propertyName
	 *
	 * @return array
	 */
	protected function getRecommendedIds($productId, $propertyName)
	{

		if(!$productId)
			return array();

		$elementIterator = CIBlockElement::getList(
			array(),
			array("ID" => $productId),
			false,
			false,
			array("ID", "IBLOCK_ID")
		);

		$linked = array();
		if($element = $elementIterator->getNextElement())
		{
			$props = $element->getProperties();
			$linked = $props[$propertyName]['VALUE'];
		}

		if(empty($linked))
			$linked = -1;

		$productIterator = CIBlockElement::getList(
			array(),
			array("ID" => $linked),
			false,
			array("nTopCount" => $this->arParams['PAGE_ELEMENT_COUNT']),
			array("ID")
		);

		$ids = array();
		while($item = $productIterator->fetch())
			$ids[] =  $item['ID'];

		return $ids;
	}
	/**
	 * @override
	 * @return integer[]
	 */
	protected function getProductIds()
	{
		if(!$this->arParams['ID'])
			return array();

		$ids = array();

		$res = CIBlockElement::getList(
			array(),
			$arFilter = array("IBLOCK_ID" => 9, "PROPERTY_RECOMMEND" => $this->arParams['ID']),
			false,
			false,
			array("ID", "IBLOCK_ID")
		);
		while($ob = $res->GetNextElement()){
			$arFields = $ob->GetFields();
			if(!in_array($arFields["ID"], $ids)) {
				//if($this->arParams['ID'] != $arFields['ID'])
				$ids[] = $arFields['ID'];
			}
		}

		/* код из стандартного компонента
		$info = CCatalogSku::GetProductInfo($this->arParams['ID']);

		$ids = array();
		if($info) // SKU
		{
			$ids = $this->getRecommendedIds($this->arParams['ID'], $this->arParams['OFFERS_PROPERTY_LINK']);

			if(!count($ids))
			{
				$ids = $this->getRecommendedIds($info['ID'], $this->arParams['PROPERTY_LINK']);
			}
		}
		else
		{
			$ids = $this->getRecommendedIds($this->arParams['ID'], $this->arParams['PROPERTY_LINK']);
		}
		*/

		/* Studiobit modified START
		 * PROPERTY_ID_RECOMMEND_SECTIONS - список ID секций, в которых надо показывать рекомендацию
		 * $this->arParams["STUDIOBIT_IBLOCK_SECTION_ID"] - текущая секция, где находится посетитель
		 * Собираю текущую ID секции и всех её родителей в переменную $arSectionAll
		 * Выбираю рекомендации для всех ID в $arSectionAll
		 * Недостаток: явно указываю символьный код свойств, откуда брать. В данном случае ID_RECOMMEND_SECTIONS
		 */
		if(!empty($this->arParams["STUDIOBIT_IBLOCK_SECTION_ID"])) {
			$arSectionAll = array();
			$nav = CIBlockSection::GetNavChain(false, $this->arParams["STUDIOBIT_IBLOCK_SECTION_ID"]);
			while ($arSectionPath = $nav->GetNext()) {
				//if ($GLOBALS['USER']->IsAdmin()){ echo '<pre>';print_r($arSectionPath);echo '</pre>';}
				$arSectionAll[] = $arSectionPath['ID'];
			}
			//_::d($arSectionAll);
		}


		/* выбираю рекомендации для раздела, в котором находится просматриваемый товар и рекомендации для разделов выше
		 * при условии что
		 * $ids будет содержать все рекомендации без повторений товаров
		 */
		if(!empty($arSectionAll)){
			foreach ($arSectionAll as $value) {
				$arSelect = Array("ID", "IBLOCK_ID");
				$arFilter = Array(
					"IBLOCK_ID" => "9",
					"PROPERTY_ID_RECOMMEND_SECTIONS" => $value
				);

				$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
				while ($ob = $res->GetNextElement()) {
					$arFields = $ob->GetFields();
					// проверить нет ли товара с этим ID уже в массиве, если нет, то добавить ID в массив
					if(!in_array($arFields["ID"], $ids)) {
						$ids[] = $arFields['ID'];
					}
				}
			}
		}

		/* выбираю рекомендации для бренда, которому принадлежит просматриваемый товар
		 * при условии что у товара назначен бренд
		 */
		if(!empty($this->arParams["STUDIOBIT_BRAND_VALUE"])) {
			$arSelect = Array("ID", "IBLOCK_ID");
			$arFilter = Array("IBLOCK_ID" => "9", "PROPERTY_NAME_RECOMMEND_BRANDS" => $this->arParams["STUDIOBIT_BRAND_VALUE"]);

			$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			while ($ob = $res->GetNextElement()) {
				$arFields = $ob->GetFields();
				// проверить нет ли товара с этим ID уже в массиве, если нет, то добавить ID в массив
				if(!in_array($arFields["ID"], $ids)) {
					//if($this->arParams['ID'] != $arFields['ID'])
					$ids[] = $arFields['ID'];
				}
			}
		}
		//удаляем из массива рекомендаций сам товар, для которого показываем
		$key = array_search($this->arParams['ID'], $ids);
		if ($key !== false)
			unset($ids[$key]);
		//Studiobit modified END
		return $ids;
	}

}