<?php
/**
 * Created by PhpStorm.
 * User: dnkolosov
 * Date: 08.12.2016
 * Time: 11:55
 */

namespace SB;

class Image
{
    static function getResizeImage($arPicture)
    {
        if(!$arPicture['HEIGHT'] || !$arPicture['WIDTH'])
            throw new \Exception('Не указана высота или ширина картинки');

        $size = self::getMinimalSide($arPicture);

        return \CFile::ResizeImageGet($arPicture, array("width" => $size, "height" => $size), BX_RESIZE_IMAGE_EXACT, true );
    }

    static function getMinimalSide($arPicture)
    {
        return $arPicture['HEIGHT'] >= $arPicture['WIDTH'] ? $arPicture['WIDTH'] : $arPicture['HEIGHT'];
    }
}