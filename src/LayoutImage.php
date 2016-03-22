<?php

namespace ProfIT\Bbb;

class LayoutImage
{

    protected $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function generatePng($width, $height)
    {
        $rootWindow = new LayoutWindow([
            'x' => 0,
            'y' => 0,
            'w' => $width,
            'h' => $height
        ]);
        $rootWindow->setChildren($this->layout->windows);
        $rootWindow->render();

        imagepng($rootWindow->canvas, 'test.png');
    }
}