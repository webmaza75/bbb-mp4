<?php

namespace ProfIT\Bbb\layout;

class TitleBar extends Box
{
    public $relX = 0;
    public $relY = 0;
    public $relW = 1;
    public $h = 30;

    public function render($canvas)
    {
        parent::render($canvas);

        $this->parent->addOffset(0, $this->absH);
    }
}