<?php

/**
 * debug class, just for convinient
 * @author skondratov
 **/

include_once 'classes/dump.php';

class _
{
    protected static $instance;
    protected $counter;
    protected $prev_time;

    protected static $prev_time_simple;
    protected static $i = 1;
    protected static $memory;
    protected static $sEndOfMessageSymbol = PHP_EOL;

    protected static $aQuery = array();

    public static function getInstance()
    {
        if(is_null(self::$instance))
        {
            self::$instance = new _;
            self::$instance->counter = 0;
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Dump
     */
    static function d()
{
    return \debugger\dumper::dump(func_get_args());
}

    /**
     * Just print string and PHP_EOL
     */
    static function ds()
    {
        foreach(func_get_args() as $sString)
        {
            print ($sString . PHP_EOL);
        }
    }

    /**
     * Dump & Die
     */
    static function dd()
    {
        print \debugger\dumper::dump(func_get_args());
        die();
    }

    /**
     * Дамп в консоль браузера. Обязательно должна быть подключена jQuery
     *
     * @param $mData
     */
    public static function dJS($mData)
    {
        $json = json_encode($mData);

        echo "<script type='text/javascript'>
                jQuery(document).ready(function(){
                    console.log($json);
                });
            </script>";
    }

    /**
     * @author D. Panachev <22.11.2013, Implementation>
     */
    static function get_caller_info($bFullFilePath = false)
    {
        $sCalled = '';
        $sFunction = '';
        $sFile = '';
        $iLine = '';
        $sClass = '';
        $aTrace = debug_backtrace();

        if(isset($aTrace[1]))
        {
            $iLine = isset($aTrace[1]['line']) ? $aTrace[1]['line'] : '';
            $sFile = isset($aTrace[1]['file']) ? $aTrace[1]['file'] : '';
        }

        if(isset($aTrace[2]))
        {
            $sFunction = $aTrace[2]['function'];

            $sAction = substr($sFunction, 0, 7);
            if($sAction == 'include' || $sAction == 'require')
            {
                $sFunction = '';
            }
        }
        if(isset($aTrace[3]['class']))
        {
            $sClass = $aTrace[3]['class'];
            $sFunction = $aTrace[3]['function'];
            $sFile = $aTrace[2]['file'];
        }
        else if(isset($aTrace[2]['class']))
        {
            $sClass = $aTrace[2]['class'];
            $sFunction = $aTrace[2]['function'];
            $sFile = $aTrace[1]['file'];
        }

        if(!$bFullFilePath and $sFile != '')
        {
            $sFile = basename($sFile);
        }

        $sCalled = $sFile . ($iLine ? ":" . $iLine : '') . ' - ';
        $sCalled .= ($sClass) ? $sClass . "->" : "";
        $sCalled .= ($sFunction) ? $sFunction . "()" : "";

        return $sCalled;
    }

    static function dump_caller_info($bFullFilePath = false)
    {
        self::d(self::get_caller_info());
    }

    static function t($print = true)
    {
        if($print)
            print '<pre>' . (microtime(true) - self::getInstance()->prev_time) . ' - (' . self::getInstance()->counter . ') - (' . self::GetCallingMethodName() . ')</pre>' . PHP_EOL;
        self::getInstance()->counter++;
        self::getInstance()->prev_time = microtime(true);
    }

    /**
     * Time simple (in milliseconds).
     * First call will start timer.
     *
     * @author D. Panachev <24.07.2013, Implementation>
     * @author D. Panachev <18.11.2013, number_format call added>
     */
    static function ts($sSomeText = '')
    {
        // On first call
        if(!self::$prev_time_simple)
        {
            self::$prev_time_simple = microtime(true);

            //print '<pre>Timer started</pre>' . PHP_EOL;
            return;
        }

        $sTime = (microtime(true) - self::$prev_time_simple) * 1000;
        print '<pre>' . ($sSomeText ? $sSomeText . ': ' : '') . number_format($sTime, 3, ',', ' ') . '<pre>' . PHP_EOL;
    }

    /**
     * Накапливаемый счетчик времени в мс
     * нечетный/четный вызовы старт/стоп
     *
     * $bOut - флаг вывода данных
     * $sId - ид таймера
     *
     * @author V. Shiryakov <04.04.2014, Implementation>
     */
    static function timer($bOut = false, $sId = 1)
    {
        static $aTimers;

        if($bOut)
        {
            return self::d($aTimers[$sId]);
        }

        $sTime = microtime(true);

        $aTimers[$sId]['interval'][] = $sTime;

        if(count($aTimers[$sId]['interval']) == 2)
        {
            if(empty($aTimers[$sId]['time']))
            {
                $aTimers[$sId]['time'] = 0;
                $aTimers[$sId]['count'] = 0;
            }
            $aTimers[$sId]['time'] += ($aTimers[$sId]['interval'][1] - $aTimers[$sId]['interval'][0]) * 1000 - 0.00063;
            $aTimers[$sId]['count']++;

            unset($aTimers[$sId]['interval']);
        }

        return $aTimers[$sId];
    }

    /**
     * @author D. Panachev <17.07.2013, Implementation>
     */
    static function i()
    {
        self::d(self::$i++);
    }

    static function get_calling_method_name()
    {
        $e = new Exception();
        $trace = $e->getTrace();
        //position 0 would be the line that called this function so we ignore it
        $last_call = $trace[1]['file'] . ' - ' . $trace[1]['line'];

        return $last_call;
    }

    static function add_query($sQuery, $iTime, $aParams = array())
    {
        $aQuery = array(
            'time'  => number_format($iTime, 7),
            'query' => $sQuery,
        );

        if($aParams)
        {
            $aQuery['params'] = $aParams;
        }

        self::$aQuery[] = $aQuery;
    }

    private static function queries_cmp_function($a, $b)
    {
        if($a['time'] === $b['time'])
        {
            return 0;
        }

        return ($a['time'] > $b['time']) ? -1 : 1;
    }

    static function show_queries($bSortByTime = false)
    {
        $aQuery = self::$aQuery;

        if($bSortByTime)
        {
            uasort($aQuery, 'self::queries_cmp_function');
        }

        self::d(array('total' => count(self::$aQuery), 'queries' => $aQuery));
    }

    /**
     * Show livestreet stats on function call place
     *
     * @author D. Panachev <11.10.2013, Implementation>
     */
    static function show_stats_performance()
    {
        $oEngine = Engine::getInstance();

        $iTimeInit = $oEngine->GetTimeInit();
        $iTimeFull = round(microtime(true) - $iTimeInit, 3);

        $aStats = $oEngine->getStats();
        $aStats['cache']['time'] = round($aStats['cache']['time'], 5);

        $aMemoryStats['memory_limit'] = ini_get('memory_limit');
        $aMemoryStats['usage'] = adm::size(memory_get_usage());
        $aMemoryStats['peak_usage'] = adm::size(memory_get_peak_usage(true));

        $sStat = 'Time to function call: ' . $iTimeFull . PHP_EOL . 'MySql' . ' [query: ' . $aStats['sql']['count'] . ']' . ' [time: ' . $aStats['sql']['time'] . ']' . PHP_EOL . 'Cache' . ' [query: ' . $aStats['cache']['count'] . ']' . ' [set: ' . $aStats['cache']['count_set'] . ']' . ' [get: ' . $aStats['cache']['count_get'] . ']' . ' [time: ' . $aStats['cache']['time'] . ']' . PHP_EOL . 'PHP' . ' [time load modules: ' . $aStats['engine']['time_load_module'] . ']' . PHP_EOL . 'Memory' . ' [memory limit: ' . $aMemoryStats['memory_limit'] . ']' . ' [usage: ' . $aMemoryStats['usage'] . ']' . ' [peak usage: ' . $aMemoryStats['peak_usage'] . ']';

        self::d($sStat);
    }

    /**
     * Using:
     *
     * _::MemoryUsageReset();
     * //
     * // Write code
     * //
     * _::MemoryUsageShow();
     * //
     * // Write code
     * //
     * _::MemoryUsageShow('Last call');
     *
     *
     * @author D. Panachev <18.12.2013, Implementation>
     */
    static function memory_usage_show($sPreText = '')
    {
        if($sPreText)
            printf($sPreText . '|');
        unset($sPreText);
        $iDiff = memory_get_usage() - self::$memory;
        $iDiff = $iDiff / 1024; // kB
        printf('Memory usage: ' . number_format($iDiff, 2, '.', '\'') . ' kB' . self::$sEndOfMessageSymbol);
        flush();
    }

    static function memory_usage_reset($sEndOfMessageSymbol = PHP_EOL)
    {
        // Обязательно два раза, т.к. часть уходит на объявление
        self::$memory = memory_get_usage();
        self::$memory = memory_get_usage();

        // Установим символ конца сообщения
        self::$sEndOfMessageSymbol = $sEndOfMessageSymbol;
    }

    static function write_to_log($sText, $sType = 'a', $sFile = '/home/skondratov/public_html/sararu/debug_log')
    {
        try
        {
            $file = fopen($sFile, $sType);
            fwrite($file, $sText .= PHP_EOL, strlen($sText));
        }
        catch(Exception $e)
        {
            self::d($e);
        }
    }

    static function time($time)
    {
        _::d(date('Y-m-d H-i', $time));
    }
}