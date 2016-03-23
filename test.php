<?php

require __DIR__ . '/autoload.php';
$layout = new \ProfIT\Bbb\Layout(__DIR__ . '/resources/layout.xml', 'defaultlayout');

$image = new \ProfIT\Bbb\layout\Image([
    'w' => 1024,
    'h' => 768
]);
$image->loadLayout($layout);
$image->generatePng('test.png');