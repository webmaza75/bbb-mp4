<?php

namespace ProfIT\Bbb;

class LayoutWindow
{

    public $width;
    public $height;
    public $x;
    public $y;
    public $minWidth;
    public $minHeight;

    public function __construct(array $props = [])
    {
        foreach ($props as $key => $val) {
            if (null !== $val) {
                $this->$key = $val;
            }
        }
    }

}