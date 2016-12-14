<?php
namespace Blackbutterfly\Seoredirektprod7;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class SeoredirektTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> FIRSTURL string(255) mandatory
 * <li> EROR404 int mandatory
 * <li> COUNT404 int mandatory
 * <li> LASTURL string(255) mandatory
 * <li> TYPEBLOCK string(255) mandatory
 * <li> SITEID string(255) mandatory
 * </ul>
 *
 * @package Bitrix\Blackbutterfly
 **/

class SeoredirektTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_blackbutterfly_seoredirekt';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('SEOREDIREKT_ENTITY_ID_FIELD'),
			),
			'FIRSTURL' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateFirsturl'),
				'title' => Loc::getMessage('SEOREDIREKT_ENTITY_FIRSTURL_FIELD'),
			),
			'LASTURL' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateLasturl'),
				'title' => Loc::getMessage('SEOREDIREKT_ENTITY_LASTURL_FIELD'),
			),
			'TYPEBLOCK' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateTypeblock'),
				'title' => Loc::getMessage('SEOREDIREKT_ENTITY_TYPEBLOCK_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteid'),
				'title' => Loc::getMessage('SEOREDIREKT_ENTITY_SITEID_FIELD'),
			),
			'ADDURLDATE' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('SEOREDIREKT_ENTITY_ADDURLDATE_FIELD'),
			),
			'USERID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('SEOREDIREKT_ENTITY_USERID_FIELD'),
			),
		);
	}
	public static function validateFirsturl()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	public static function validateLasturl()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	public static function validateTypeblock()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	public static function validateSiteid()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
}