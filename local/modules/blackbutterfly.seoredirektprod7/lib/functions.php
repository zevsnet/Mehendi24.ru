<?php
namespace Blackbutterfly\Seoredirektprod7;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Loader;

class Functions
{

    static function getModuleId()
    {
        return 'blackbutterfly.seoredirektprod7';
    }

    static function deleteByIdWithEntityName($id, $entityName)
    {
        eval($entityName . '::delete(' . $id . ');');
    }
	static function takeElement($firsturl, $lasturl, $type, $sEntity, $id)
	{
		if ($id == '' ) {
			/* ������ �� ������. ��� �������� ��������*/
				$rs = $sEntity::getList(array('filter' => array('FIRSTURL' => $firsturl, "LASTURL"=> $lasturl)));
				while ($ar = $rs->fetch()) {
					\Blackbutterfly\Seoredirektprod7\Functions::deleteByIdWithEntityName($ar['ID'] , $sEntity);
				}			
			/* ----------------------- */
		}
		/* ������ �� ����������� ���������. ��� �������� � �������������� ��������*/
			$rs = $sEntity::getList(array('filter' => array('FIRSTURL' => $lasturl, "LASTURL"=> $firsturl)));
			while ($ar = $rs->fetch()) {
					\Blackbutterfly\Seoredirektprod7\Functions::deleteByIdWithEntityName($ar['ID'] , $sEntity);
			}
		/* ----------------------- */
		/* ������ �� �������� ��������� */
				$rs = $sEntity::getList(array('filter' => array('LASTURL' => $firsturl)));
				while ($ar = $rs->fetch()) {
					$ar['LASTURL'] = $lasturl;
					$res = $sEntity::update($ar['ID'],$ar);
				}
		/* ----------------------- */
	}	
}