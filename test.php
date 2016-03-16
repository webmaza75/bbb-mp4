<?php

require __DIR__ . '/autoload.php';
$layout = new \ProfIT\Bbb\Layout(__DIR__ . '/resources/layout.xml', 'defaultlayout');

$image = new \ProfIT\Bbb\LayoutImage($layout);
$image->generatePng(1024, 768);