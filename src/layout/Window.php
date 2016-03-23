<?php

namespace ProfIT\Bbb\layout;

class Window extends Box
{
    public $name;
    public $title;

    public function createTitleBar()
    {
        $titleBar = new TitleBar();

        $this->addChild($titleBar);
    }
}