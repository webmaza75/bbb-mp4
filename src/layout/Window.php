<?php

namespace ProfIT\Bbb\layout;

class Window extends Box
{
    public $name;
    public $title;

    public function createTitleBar()
    {
        $titleBar = new TitleBar([
            'relX' => 0,
            'relY' => 0,
            'relW' => 1,
            'h'    => 30
        ]);

        $this->addChild($titleBar);
    }
}