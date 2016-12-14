<?php
namespace SB;


class CsvException extends \Exception
{


}

class CsvRow
{
    /**
     * @var Csv null
     */
    public $Csv = null;

    private $arCells = array();

    public function __construct($Csv)
    {
        $this->Csv = $Csv;
    }

    public function getCells()
    {
        return $this->arCells;
    }

    public function setCells($arCells)
    {
        $this->arCells = $arCells;
    }

    public function setCell($columnIndex, $value)
    {
        $this->arCells[$columnIndex] = $value;
    }

    public function addCell($value)
    {
        $this->arCells[] = $value;
    }

    public function setCellByName($column, $value)
    {
        if ($this->Csv->registerIgnore)
            $column = mb_strtolower($column);

        $index = $this->Csv->getIndexByName($column);

        $this->setCell($index, $value);
    }

    /**
     * Получает колонку по индексу
     *
     * @param $index
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCell($index)
    {
        if (!array_key_exists($index, $this->arCells))
            throw new \Exception("Column index {$index} haven`t find");

        return $this->arCells[$index];
    }


    /**
     * Получает ячейку текущей строки по названию колонки
     *
     * @param $column
     *
     * @return bool
     */
    public function getCellByName($column)
    {
        if ($this->Csv->registerIgnore)
            $column = mb_strtolower($column);

        $index = $this->Csv->getIndexByName($column);

        return $this->getCell($index);
    }
}

class Csv
{
    // Строка с названими колонок
    private $HeadRow = null;

    // Строка с названими колонок, для доступа по названию
    protected $arHeadRowFlip = array();

    /**
     * @var CsvRow array
     */
    private $arRows = array();
    private $delimiter = ';';
    private $rowIterator = -1;
    private $encoding = 'UTF-8';

    public $registerIgnore = true;


    public function __construct()
    {

    }

    /**
     * Получает индекс колонки по названию
     *
     * @param $columnName
     *
     * @return mixed
     * @throws \Exception
     */
    public function getIndexByName($columnName)
    {
        if (!array_key_exists($columnName, $this->arHeadRowFlip))
            throw new \Exception("Column {$columnName} haven`t find");

        return $this->arHeadRowFlip[$columnName];
    }

    /**
     * Устанавливает внутренюю кодировку
     *
     * @param $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    public function getRow($rowIndex)
    {
        if (!array_key_exists($rowIndex, $this->arRows))
            throw new \Exception("Row index {$rowIndex} haven`t find: ");

        return $this->arRows[$rowIndex];
    }

    /**
     * Итератор строк
     *
     * @return CsvRow
     */
    public function eachRow()
    {
        $this->rowIterator++;

        $Row = $this->getCurrentRow();

        if (!$Row)
            $this->resetIterator();

        return $Row;
    }

    /**
     * Получает объект текущей строки
     *
     * @return CsvRow
     */
    public function getCurrentRow()
    {
        try
        {
            return $this->getRow($this->rowIterator);
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * @param $filePath
     * @param bool $headRow - флаг что в первой строке идут названия колонок
     * @param string $fileEncoding
     *
     * @return bool
     * @throws \Exception
     */
    public function readFile($filePath, $fileEncoding = 'WINDOWS-1251', $headRow = true)
    {
        if (!$filePath)
            throw new \Exception("Filename is empty: ".$filePath);

        if (!file_exists($filePath))
            throw new \Exception("File dosen't exist. file: ".$filePath);

        $fh = fopen($filePath, 'r');

        // Читаем строку с названиями колонок
        if ($headRow)
        {
            $arRowHead = fgetcsv($fh, null, $this->delimiter);
            if($this->encoding != $this->fileEncoding)
                $arRowHead = \SB\Tools::iconvArray($arRowHead, $fileEncoding, $this->encoding);

            $this->setHeadRow($arRowHead);
        }

        $this->arRows = array();

        while (($arRow = fgetcsv($fh, null, $this->delimiter)) !== FALSE)
        {
            $Row = new CsvRow($this);

            if ($this->encoding != $this->fileEncoding)
                $arRow = \SB\Tools::iconvArray($arRow, $fileEncoding, $this->encoding);

            $Row->setCells($arRow);

            $this->arRows[] = $Row;
        }

        if (!fclose($fh))
            return false;

        $this->resetIterator();

        return true;
    }

    public function resetIterator()
    {
        $this->rowIterator= -1;
    }


    /**
     * Создает массив с названиями колонок $this->arHeadRowFlip
     *
     */
    public function prepareHeadRow()
    {
        // Генериуем массив для доступа по названию колонок
        foreach($this->HeadRow->getCells() as $key=>$item)
        {
            if ($this->registerIgnore)
                $item = strtolower($item);

            $this->arHeadRowFlip[$item] = $key;
        }
    }

    /**
     * Добавляет строку
     *
     * @param string|array $column1
     * @param string $column2
     * @param string $column3
     * ...
     *
     */
    public function setHeadRow()
    {
        $arArgs = func_get_args();

        if (is_array($arArgs[0]))
            $arValues = $arArgs[0];
        else
            $arValues = $arArgs;

        $CsvRow = new CsvRow($this);
        $CsvRow->setCells($arValues);

        $this->HeadRow = $CsvRow;
        $this->prepareHeadRow();
    }

    /**
     * Сбрасывает строку с названиями колонок, чтобы не записалась в файл
     *
     */
    public function clearHeadRow()
    {
        unset($this->HeadRow);
        $this->HeadRow = null;
    }


    /**
     * Добавляет строку
     *
     * @param string|array $value1
     * @param string $value2
     * @param string $value3
     * ...
     *
     */
    public function addRow()
    {
        $arArgs = func_get_args();

        if (is_array($arArgs[0]))
            $arValues = $arArgs[0];
        else
            $arValues = $arArgs;


        $CsvRow = new CsvRow($this);
        $CsvRow->setCells($arValues);

        $this->rowIterator = count($this->arRows);
        $this->arRows[$this->rowIterator] = $CsvRow;
    }

    /**
     * Сохраняет файл
     *
     * @param $filePath
     * @param string $fileEncoding
     */
    public function saveFile($filePath, $fileEncoding='WINDOWS-1251')
    {
        $fh = fopen($filePath, 'w');

        // Записываем в файл заголовок
        if ($this->HeadRow)
        {
            if ($this->encoding != $fileEncoding)
                fputcsv($fh, \SB\Tools::iconvArray($this->HeadRow->getCells(), $this->encoding, $fileEncoding), $this->delimiter);
            else
                fputcsv($fh, $this->HeadRow->getCells(), $this->delimiter);
        }

        foreach($this->arRows as $arRow)
        {
            if ($this->encoding != $fileEncoding)
                fputcsv($fh, \SB\Tools::iconvArray($arRow->getCells(), $this->encoding, $fileEncoding), $this->delimiter);
            else
                fputcsv($fh, $arRow->getCells(), $this->delimiter);
        }

        fclose($fh);
    }
}