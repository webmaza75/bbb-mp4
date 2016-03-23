<?php

namespace ProfIT\Bbb\layout;

use ProfIT\Bbb\Layout;

class Image extends Box
{
    public $x = 0;
    public $y = 0;

    protected $layout;

    public function loadLayout(Layout $layout)
    {
        $this->layout = $layout;
        $this->setChildren($layout->getWindows());
    }

    public function generatePng($filename)
    {
        $this->render();
        imagepng($this->canvas, $filename);
    }
}