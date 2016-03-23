<?php

namespace ProfIT\Bbb\layout;

use ProfIT\Bbb\Layout;

class Image extends Box
{
    public $x = 0;
    public $y = 0;
    public $canvas;

    protected $layout;

    public function loadLayout(Layout $layout)
    {
        $this->layout = $layout;

        foreach ($layout->getWindows() as $child) {
            /** @var Window $child */
            $child->title = $child->name;
            $child->createTitleBar();

            $this->addChild($child);
        }
    }

    public function generatePng($filename)
    {
        $canvas = imagecreatetruecolor($this->absW, $this->absH);
        $this->canvas = $canvas;

        $this->render($canvas);

        imagepng($canvas, $filename);
    }
}