<?php

require __DIR__ . '/autoload.php';
$layout = new \ProfIT\Bbb\Layout(__DIR__ . '/resources/layout.xml', 'defaultlayout');

$image = new \ProfIT\Bbb\layout\Image([
    'w' => 1280,
    'h' => 720
]);
$image->loadLayout($layout);
$image->generatePng('test.png');