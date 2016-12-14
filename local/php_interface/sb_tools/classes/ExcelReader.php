<?php
namespace SB;

if (!class_exists('\PHPExcel'))
{
    throw new \Exception("PHPExcel doesn`t include. Include PHPExcel now!");
}

class ExcelReader
{
    // Разделитель используемый при формировании csv файла
    public $delimiter = ';';

    private $startColumn;
    private $endColumn;

    // Колиество пустых строк подряд, при котором будет определен конец файла
    public $maxCountEmptyRows = 5;

    private $partSize = 300;

    public $readerType = 'Excel2007';

    // Итератор прочитанных строк. соответствует нумерации в файле excel
    private $rowIterator = 0;

    private $arColumns = array();

    // Читаемая книга
    private $book = false;

    // Читаемый файл
    private $filePath = false;

    /**
     * @param $filePath
     * @param bool $book - Название книги в документе excel
     *
     * @throws \Exception
     */
    function __construct($filePath, $book = false)
    {
        $this->filePath = $filePath;
        $this->book = $book;

        if (!file_exists($this->filePath))
            throw new \Exception("File dosen't exist. file: ".$this->filePath);

        $arPathInfo = pathinfo($filePath);

        if ($arPathInfo['extension'] == 'xls')
            $this->readerType = 'Excel5';
        elseif ($arPathInfo['extension'] == 'xlsx')
            $this->readerType = 'Excel2007';

        $this->arRows = array();
    }

    /**
     * Устанавливает диапазон колонок, которые читаем
     *
     * @param $startColumn
     * @param $endColumn
     */
    public function setRangeColumns($startColumn, $endColumn)
    {
        $this->startColumn  = $startColumn;
        $this->endColumn    = $endColumn;

        $arAlphabet[] = '';
        for($i = ord('A'); $i <= ord('Z'); $i++)
        {
            $arAlphabet[] = chr($i);
        }

        $arColumns = array();
        for($i=\SB\Tools::convertToDec($arAlphabet, $this->startColumn); $i<=\SB\Tools::convertToDec($arAlphabet, $this->endColumn); $i++)
        {
            $key = \SB\Tools::decToAlphabet($arAlphabet, $i);
            $arColumns[$key] = $key;
        }

        $this->arColumns = $arColumns;
    }

    /**
     * Устанавливает номер строки с кторой читать данные
     *
     * @param $startRow
     */
    public function setStartRow($startRow)
    {
        $this->rowIterator = $startRow;
    }

    public function getRowIterator()
    {
        return $this->rowIterator;
    }

    /**
     * Устанавливает количество строки читаемое за раз
     *
     * @param $partSize
     */
    public function setPartSize($partSize)
    {
        $this->partSize = $partSize;
    }

    /**
     * Читает часть excel файла
     *
     * @return bool
     * @throws \Exception
     */
    public function readPartOfFile()
    {
        if(!file_exists($this->filePath))
        {
            throw new \Exception("File dosen't exist. file: ". $this->filePath);
        }

        if(empty($this->arColumns))
        {
            throw new \Exception("Columns rage doesn`t set. Execute function setRangeColumns!". $this->filePath);
        }

        $Reader = \PHPExcel_IOFactory::createReader($this->readerType);

        $ChunkFilter = new chunkReadFilter();
        $ChunkFilter->setRows($this->rowIterator, $this->partSize);

        $Reader->setReadFilter($ChunkFilter);
        $Reader->setReadDataOnly(true);

        $Excel = $Reader->load($this->filePath);

        if ($this->book)
            $worksheet = $Excel->getSheetByName($this->book);
        else
            $worksheet = $Excel->getActiveSheet();

        //получим итератор строки и пройдемся по нему циклом
        $endFile = false;
        $countEmptyRows = 0;

        for($i = $this->rowIterator; $i < $this->rowIterator + $this->partSize; $i++)
        {
            $rowString = '';
            $arRow = array();

            foreach($this->arColumns as $column)
            {
                $value = trim($worksheet->getCell($column . $i)->getValue());
                $arRow[] = $value;
                $rowString .= $value;
            }

            $rowString = trim($rowString);

            if ($rowString == '')
            {
                $countEmptyRows++;
            }
            else
            {
                $countEmptyRows = 0;
            }

            $this->arRows[] = $arRow;

            if ($countEmptyRows>=$this->maxCountEmptyRows)
            {
                $endFile = true;
                break;
            }
        }

        // Удаляем последнии пустые строки
        if ($endFile)
        {
            while(1)
            {
                if (!$this->arRows)
                    break;

                $arRow = array_pop($this->arRows);

                if (trim(implode('',$arRow))!='')
                {
                    array_push($this->arRows, $arRow);
                    break;
                }
            }
        }

        $rowIteratorNextPosition = $this->rowIterator  + $this->partSize;
        $this->rowIterator = $rowIteratorNextPosition;

        if ($endFile)
        {
            return true;
        }

        return false;
    }

    public function getRows()
    {
        return $this->arRows;
    }

    /**
     * Сохраняет Данные в csv файл
     *
     * @param $filePath
     */
    public function saveToCsv($filePath)
    {
        foreach($this->arColumns as $column)
            $arRowHead[] = $column;

        $fh = fopen($filePath, 'w');

        fputcsv($fh, $arRowHead, $this->delimiter);

        foreach($this->arRows as $arRow)
        {
            fputcsv($fh,$arRow, $this->delimiter);
        }

        fclose($fh);
    }
}

class chunkReadFilter implements \PHPExcel_Reader_IReadFilter
{
    private $_startRow = 0;
    private $_endRow = 0;

    public function setRows($startRow, $chunkSize)
    {
        $this->_startRow = $startRow;
        $this->_endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if(($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow))
        {
            return true;
        }

        return false;
    }
}