<?php
namespace SB;

class Tools
{
    /**
     *
     * Возвращает подмассив из $aSrc только с ключами $mKeys
     *
     * @param mixed $mKeys - массив либо строка с разделителем ","
     * @param mixed $bUseKeyExists - Для определения существования элемента будет использоваться функция array_key_exists.
     *                               требуется вслучие когда нужно сохранить значения null
     * @param mixed $mFillValue - заполнять заначеними $mFillValue отсутствующие элемент в $aSrc
     *
     *
     * @author V. Shiryakov <04.10.2013, Implementation>
     * @author V. Shiryakov <15.01.2014, Добавлен $bUseKeyExists>
     * @author V. Shiryakov <21.07.2014, Добавлен $bCreateNull>
     * @author D. Panachev <02.10.2014, Добавил проверку на пустой входной масив $aSrc>
     */
    public static function extract_array($aSrc, $mKeys, $bUseKeyExists = false, $mFillValue = false)
    {
        if(empty($mKeys))
            return array();

        if(empty($aSrc) && $mFillValue === false)
            return array();

        if(is_array($mKeys))
            $aKeys = $mKeys;
        else
            $aKeys = explode(',', $mKeys);

        $aResult = array();

        if($bUseKeyExists)
        {
            foreach($aKeys as $sKey)
            {
                if(array_key_exists($sKey, $aSrc))
                    $aResult[$sKey] = $aSrc[$sKey];
                elseif($mFillValue !== false)
                    $aResult[$sKey] = $mFillValue;
            }
        }
        else
        {
            foreach($aKeys as $sKey)
            {
                if(isset($aSrc[$sKey]))
                    $aResult[$sKey] = $aSrc[$sKey];
                elseif($mFillValue !== false)
                    $aResult[$sKey] = $mFillValue;
            }
        }

        return $aResult;
    }

    /**
     * Заменяет ключ в $aRows на значени элемента подмассива c ключем $sKey
     *
     * $aResult[$aRows[i][$sKey]] = $aRows[i]
     *
     * @author V. Shiryakov <19.03.2014, Implementation>
     */
    public static function replace_key_array($aRows, $sKey)
    {
        $aResult = array();
        foreach($aRows as $iKey => $aRow)
        {
            if(empty($aRow[$sKey]))
                continue;

            $aResult[$aRow[$sKey]] = $aRow;
        }

        return $aResult;
    }

    /**
     * Получает список. Ключ - значение элемента $aRows с ключем $sKey, Значение - значение элемента $aRows с ключем $sKeyValue
     *
     * @param $aRows
     * @param $sKey
     * @param $sKeyValue
     *
     * @return array
     */
    public static function getList($aRows, $sKey, $sKeyValue)
    {
        $aResult = array();
        foreach($aRows as $iKey => $aRow)
        {
            if(empty($aRow[$sKey]))
                continue;

            $aResult[$aRow[$sKey]] = $aRow[$sKeyValue];
        }

        return $aResult;
    }

    /**
     * Получает список. Ключ - ключ из $aRows, Значение - значение элемента $aRows с ключем $sKeyValue
     *
     * @param $aRows
     * @param $sKeyValue
     *
     * @return array
     */
    public static function getListInOrder($aRows, $sKeyValue)
    {
        $aResult = array();
        foreach($aRows as $iKey => $aRow)
        {
            $aResult[$iKey] = $aRow[$sKeyValue];
        }

        return $aResult;
    }

    /**
     * Делает трим для элементов одномерного массива
     *
     * @param $aRes
     * @param bool $aKeys
     *
     * @return bool
     */
    public static function array_trim(&$aRes, $aKeys=false)
    {
        if (!$aKeys)
        {
            array_walk($aRes, 'trim');
            return true;
        }

        foreach ($aKeys as $sKey)
        {
            if (!empty($aRes[$sKey]) && is_string($aRes[$sKey]))
                $aRes[$sKey] = trim($aRes[$sKey]);
        }

        return true;
    }

    /**
     * @param $arSrc
     * @param $arMap
     * @param bool $deleteNullValue
     *
     * @return null
     */
    static function arrayMapOne($arSrc, $arMap, $deleteNullValue=true)
    {
        $arItems = self::arrayMap(array($arSrc), $arMap, $deleteNullValue);

        if ($arItems)
            return $arItems[0];

        return null;
    }

    /**
     * Конвертирует массивы
     *
     * @param array $arSrc - Исходный массив
     * @param array $arMap - Схема конвертации
     *        $arMap[<ключ в результирующем массиве>] = <ключ в исходном массиве>
     *        $arMap[] =
     *          array('inKey'=><ключ в исходном массиве>,
     *                'outKey'=><ключ в результирующем массиве>,
     *                'eval'=><выражени для обработки результирующего значения>)
     *
     *        <ключ в исходном массиве> - может быть строкой или масиивом со следующей структурой, array(<ключ1>,<ключ2>,<ключ3>,...) показывающий размерность массива соответственно
     *        <ключ в результирующем массиве> - может быть строкой или масиивом со следующей структурой, array(<ключ1>,<ключ2>,<ключ3>,...) показывающий размерность массива соответственно
     *
     * @return array
     */

    /*
     *  Пример использования:
     *
        $arSrc = array(
            'ID'=>1,
            'NAME'=>'Анальгин',
            'USER'=>array(
                'ID'=>100,
                'NAME'=>'admin',
                'GROUP'=>array(1,2,5),
            ),
        );

        $arMap['id'] = 'ID';
        $arMap['name'] = 'NAME';
        $arMap[] = array('inKey'=>array('USER','ID'), 'outKey'=>'user_id');
        $arMap[] = array('inKey'=>array('USER','NAME'), 'outKey'=>'user_name', 'eval'=>'$value = strtoupper($value);');

        $arResult = \SB\Tools::arrayMapOne($arSrc, $arMap);
     */
    static function arrayMap($arSrc, $arMap, $deleteNullValue=true)
    {
        if (!$arSrc)
        {
            return $arSrc;
        }

        // Обработка входного массива $arMap. Приводим к единову виду с коючами inKey, outKey
        foreach($arMap as $key=>$arMapItem)
        {
            if (!is_numeric($key))
            {
                if(!is_array($arMapItem))
                    $arMapItem = array($arMapItem);

                $arMap[] = array('inKey'=>$arMapItem, 'outKey'=>array($key));
                unset($arMap[$key]);
                continue;
            }

            // В случае когда переносится элемент как есть
            if (!is_array($arMapItem))
            {
                $arMap[$key] = array('inKey'=>array($arMapItem), 'outKey'=>array($arMapItem));
                continue;
            }

            if(!is_array($arMapItem['inKey']))
            {
                $arMap[$key]['inKey'] = array($arMapItem['inKey']);
            }

            if(!is_array($arMapItem['outKey']))
            {
                $arMap[$key]['outKey'] = array($arMapItem['outKey']);
            }
        }

        $arResult = array();
        foreach($arSrc as $key => &$arItem)
        {
            $arResult[$key] = array();
            foreach($arMap as $arMapItem)
            {
                $inKey = $arMapItem['inKey'];
                $outKey = $arMapItem['outKey'];

                $value = &$arItem;

                foreach($inKey as $key1)
                {
                    if (!is_array($value))
                        break;

                    if (array_key_exists($key1, $value))
                        $value = &$value[$key1];
                }

                if ($value === $arItem)
                    continue;

                unset($linkValue);
                $linkValue = &$arResult[$key];

                foreach($outKey as $key1)
                {
                    if (empty($linkValue[$key1]))
                        $linkValue[$key1] = array();
                    $linkValue = &$linkValue[$key1];
                }

                if (!empty($arMapItem['eval']))
                    eval($arMapItem['eval']);

                $linkValue = $value;
            }
        }

        // Удаляем все null значения
        if ($deleteNullValue)
            self::deleteNullValue($arResult);

        return $arResult;
    }

    /**
     * Рекурсивно Удаляет все значения null
     *
     * @param $arResult
     */
    static function deleteNullValue(&$arResult)
    {
        foreach($arResult as $key=>&$arItem)
        {
            if (is_array($arItem) && empty($arItem))
            {
                unset($arResult[$key]);
            }
            elseif (is_array($arItem))
            {
                self::deleteNullValue($arItem);
            }
            elseif ($arItem===null)
            {
                unset($arResult[$key]);
            }
        }
    }

    /**
     * Формирует урл адрес с get-параметрами. без указания параметра $arArgs работает как GetPagePath()
     *
     * @param bool $page
     * @param null $get_index_page
     * @param array $arArgs - массив параметров которые надо подставить, если элемент равен null параметр удаляется
     *
     * @return string
     */
    static function GetPagePath($page = false, $get_index_page=null, $arArgs = array())
    {
        $result = GetPagePath($page, $get_index_page);

        if (!$arArgs)
            return $result;

        $args = htmlspecialcharsbx(DeleteParam(array_keys($arArgs)));

        $arArgs = array_filter($arArgs,
            function ($item)
            {
                return ($item !== null);
            });

        if ($arArgs)
        {
            if($args)
                $args .= '&';

            foreach($arArgs as $key=>$item)
            {

                $arArgs[$key] = $key . '=' . $item;
            }

            $args .= implode('&', $arArgs);
        }

        if ($args)
            $result .= '?' . $args;

        return $result;
    }

    /**
     * Получает элемент массива
     *
     * @author V. Shiryakov <09.12.2013, Implementation>
     * @author D. Panachev <20.02.2014, Param $aArr should be an a pointer>
     */
    public static function get(&$aArr, $mKey, $mReturn = null)
    {
        if (!is_array($mKey))
            return is_array($aArr) ? (array_key_exists($mKey, $aArr) ? $aArr[$mKey] : $mReturn) : $mReturn;

        $ptr = $aArr;
        foreach($mKey as $sKey)
        {

            if (!array_key_exists($sKey, $ptr))
                return $mReturn;

            $ptr = &$ptr[$sKey];
        }

        return $ptr;
    }

    /**
     * Получает атрибуты из урл. часть пути разделенное слешем
     *
     * site.ru/<атрибут 0>/<атрибут 1>/<атрибут 2>/.../
     *
     *
     * @param bool $num
     *
     * @return null
     *
     * @author V. Shiryakov <10.11.2015, Implementation>
     */
    public static function getAttrUrl($num = false)
    {
        static $arUrl;

        if($arUrl === null)
        {
            $url = $_SERVER['REQUEST_URI'];

            if (($pos = strpos($url, '?')) !== false)
            {
                $url = substr($url, 0, $pos);
            }

            if(SITE_DIR != DIRECTORY_SEPARATOR && strpos($url, SITE_DIR) === 0)
            {
                $url = str_replace(SITE_DIR, '', $url);
            }
            $url = trim($url, DIRECTORY_SEPARATOR);
            $arUrl = explode(DIRECTORY_SEPARATOR, $url);
        }

        return \SB\Tools::get($arUrl, $num, null);
    }

    /**
     * Шифрует по ключу, и переводит в base64
     *
     * @param $key - ключ шифрования
     * @param $text - шифруемый текст
     *
     * @return string
     */
    public static function fastEncrypt($key, $text)
    {
        $encodedText = mcrypt_encrypt( MCRYPT_RIJNDAEL_128, $key , $text, MCRYPT_MODE_ECB);
        return base64_encode($encodedText);
    }

    /**
     * Расшировывает данные зашифрованные функцией fastEncrypt
     *
     * @param $key - ключ шифрования
     * @param $code - шифрованные данные
     *
     * @return string
     */
    public static function fastDecrypt($key, $code)
    {
        $code = base64_decode($code);
        return mcrypt_decrypt( MCRYPT_RIJNDAEL_128, $key , $code, MCRYPT_MODE_ECB);
    }

    /**
     * Получает часть хоста, поддомен
     *
     * @param mixed $position (0,1,2,3,...,-1,-2,...). 0 - зона(ru,com), -1 (самый последний поддомен например: krasnoyars.rabota.1cbit.ru функция вернет krasnoyarsk);
     *
     * @return string
     *
     * @author V. Shiryakov <10.12.2015, Implementation>
     */
    public static function getHostPart($position = -1)
    {
//        static $arHost;
//
//        if($arHost === null)
//        {
//
//        }
            $host = $_SERVER['HTTP_HOST'];

            $arHost = array_reverse(explode('.', $host));

            $num = $position;
            if ($num<0)
            {
                $num = count($arHost)+$num;
            }


        return \SB\Tools::get($arHost, $num, null);
    }

    /**
     * Удаляет все элементы $value из массива $arSrc
     *
     * @param $arSrc
     * @param $value
     * @param bool $strict - строгое соответстиве
     */
    public static function array_clear($arSrc, $value, $strict = false)
    {
        if($strict)
        {
            foreach($arSrc as $key => $item)
                if($item === $value)
                    unset($arSrc[$key]);
        }
        else
        {
            foreach($arSrc as $key => $item)
                if($item == $value)
                    unset($arSrc[$key]);
        }

        return $arSrc;
    }

    /**
     * Конвертирует из Системы счисления алфавита $arAlphabet в десятичную
     *
     * @param $arAlphabet
     * @param $value
     *
     * @return int|mixed
     */
    public static function convertToDec($arAlphabet, $value)
    {
        $alphabetSize = count($arAlphabet);
        $valueDec = 0;

        $n = strlen($value);

        for($i=0;$i<$n;$i++)
        {
            $char = substr($value, $i, 1);

            $index = array_search($char, $arAlphabet);
            $valueDec += $index * pow($alphabetSize, $n-$i-1);
        }

        return $valueDec;
    }

    /**
     * Конвертирует из десятичную в систему счисления алфавита $arAlphabet
     *
     * @param $arAlphabet
     * @param $value
     *
     * @return string
     */
    public static function decToAlphabet($arAlphabet, $value)
    {
        $alphabetSize = count($arAlphabet);

        $valueDiv = floor($value / $alphabetSize);
        $valueMod = $value % $alphabetSize;

        if ($valueDiv)
        {
            $first = self::decToAlphabet($arAlphabet, $valueDiv);
        }

        return $first.$arAlphabet[$valueMod];
    }

    /**
     * Выполняет инклуд файла $filePath, но не выводит на экран, а возвращает получившийся html
     *
     * @param $filePath - Путь до файла
     * @param array $arTplParams - массив переменных выводимых в шаблоне
     *
     * @return string
     */
    public static function renderTpl($filePath, $arTplParams = array())
    {
        ob_start();

        if ($arTplParams)
            extract($arTplParams);

        include($filePath);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Читает директорию, возвращает список полного пути до файлов или каталогов
     *
     * @param $dirPath - полный путь до читаемой директории
     * @param string $type - (file|dir) тип получаемых путей
     *
     * @return array - список путей
     */
    public static function readDir($dirPath, $type = 'dir')
    {
        if ($type !== 'file')
            $type = 'dir';

        $arPath = array();
        $dirPath = rtrim($dirPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if(is_dir($dirPath))
        {
            if($dh = opendir($dirPath))
            {
                while(($file = readdir($dh)) !== false)
                {
                    $filePath = $dirPath . $file;

                    if ($file == '..')
                        continue;

                    if ($file == '.')
                        continue;

                    if (filetype($filePath)!=$type)
                        continue;

                    $arPath[$file] = $filePath;
                }

                closedir($dh);
            }
        }

        return $arPath;
    }

    public static function iconvArray(&$ar, $inCharset, $outCharset)
    {
        foreach($ar as &$item)
        {
            $item = iconv($inCharset, $outCharset, $item);
        }

        return $ar;
    }
}