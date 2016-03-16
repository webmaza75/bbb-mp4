<?php

require __DIR__ . '/autoload.php';
$layout = new \ProfIT\Bbb\Layout(__DIR__ . '/resources/layout.xml');

var_dump($layout->getLayoutWindow('defaultlayout'));