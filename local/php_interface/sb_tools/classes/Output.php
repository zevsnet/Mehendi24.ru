<?php
namespace SB;

abstract class Output
{
    abstract public function write($text);

    abstract public function writeln($text);

}