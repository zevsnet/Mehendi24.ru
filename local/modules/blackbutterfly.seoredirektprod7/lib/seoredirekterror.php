<?php
namespace Blackbutterfly\Seoredirektprod7;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class SeoredirekterrorTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> URL string(255) mandatory
 * <li> TRSNSITION string(255) mandatory
 * <li> COUNTSETURL string(255) mandatory
 * <li> SITEID string(255) mandatory
 * </ul>
 *
 * @package Bitrix\Blackbutterfly
 **/

class SeoredirekterrorTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_blackbutterfly_seoredirekterror';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('SEOREDIREKTERROR_ENTITY_ID_FIELD'),
			),
			'URL' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateUrl'),
				'title' => Loc::getMessage('SEOREDIREKTERROR_ENTITY_URL_FIELD'),
			),
			'COUNTSETURL' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('SEOREDIREKTERROR_ENTITY_COUNTSETURL_FIELD'),
			),
			'REFERALURL' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateReferalurl'),
				'title' => Loc::getMessage('SEOREDIREKTERROR_ENTITY_REFERALURL_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteid'),
				'title' => Loc::getMessage('SEOREDIREKTERROR_ENTITY_SITEID_FIELD'),
			),
		);
	}
	public static function validateUrl()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	public static function validateReferalurl()
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