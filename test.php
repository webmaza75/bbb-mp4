<?php

require __DIR__ . '/autoload.php';
$layout = new \ProfIT\Bbb\Layout(__DIR__ . '/resources/layout.xml', 'defaultlayout');

var_dump($layout->windows);