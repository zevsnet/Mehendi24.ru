<?php
namespace SB;

class BufferOutput extends Output
{
    protected $buffer = '';

    public function __construct()
    {

    }

    public function write($text)
    {
        $this->buffer .= $text;
    }

    public function writeln($text)
    {
        $this->buffer .= $text . PHP_EOL;
    }

    public function getBuffer()
    {
        return $this->buffer;
    }
}