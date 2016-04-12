<?php
/**
 * @use php test.php --src=./resources/layout.xml --css=./resources/style/css/BBBDefault.css --width=1280 --height=720
 * php test.php --src=./resources/layout.xml --css=./resources/style/css/BBBDefault.css
 */
//
$options = getopt('', ['src:', 'css:', 'width::', 'height::']);
// Источник src и css
$resFileName = realpath($options['src']);
$cssFileName = realpath($options['css']);
$w = $options['width'] ? : 1280;
$h = $options['height'] ? : 720;


require __DIR__ . '/autoload.php';

$layout = new \ProfIT\Bbb\Layout($resFileName, 'defaultlayout');
$layout->setStyleSheet($cssFileName);
$image = new \ProfIT\Bbb\layout\Image([
    'w' => $w,
    'h' => $h
]);

$image->loadLayout($layout);
$image->generatePng('test.png');