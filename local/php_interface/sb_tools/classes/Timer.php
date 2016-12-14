<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 19.02.2016
 * Time: 15:23
 */
namespace SB;

class Timer
{
    static private $Timer = null;
    private $startPoint = null;
    private $stopPoint = null;
    private $interval = null;


    static function Instance($new = false)
    {
        if (!self::$Timer || $new)
            self::$Timer = new Timer();

        return self::$Timer;
    }

    public function start()
    {
        $this->startPoint = microtime(true);
    }

    public function stop()
    {
        $this->stopPoint = microtime(true);

        $this->interval += $this->stopPoint - $this->startPoint;

        // Возвращаем в мили секундах
        return $this->interval*1000;
    }

    public function getInterval($type = 'ms')
    {
        switch($type)
        {
            case 'ms':
                $k = 1000;
                break;
            case 's':
                $k = 1000000;
                break;
            case 'micros':
                $k = 1;
                break;
            default:
                $k=1000;
        }

        return $this->interval*$k;
    }

    public function out($type = 'ms')
    {
        echo $this->getInterval($type);
    }










}