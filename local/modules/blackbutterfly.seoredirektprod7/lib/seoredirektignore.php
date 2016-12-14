<?php
namespace Blackbutterfly\Seoredirektprod7;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class SeoredirektignoreTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> URL string(255) mandatory
 * </ul>
 *
 * @package Bitrix\Blackbutterfly
 **/

class SeoredirektignoreTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_blackbutterfly_seoredirektignore';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('SEOREDIREKTIGNORE_ENTITY_ID_FIELD'),
			),
			'URL' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateUrl'),
				'title' => Loc::getMessage('SEOREDIREKTIGNORE_ENTITY_URL_FIELD'),
			),
		);
	}
	public static function validateUrl()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
}