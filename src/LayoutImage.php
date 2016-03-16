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
        $windows = $this->calcWindowsPositions($width, $height);
        $image = imagecreatetruecolor($width, $height);
        $colorGray = imagecolorallocate($image, 0xCC, 0xCC, 0xCC);
        $colorRed = imagecolorallocate($image, 0xCC, 0x00, 0x00);
        foreach ($windows as $window) {
            imagefilledrectangle($image, $window['x0']+2, $window['y0']+2, $window['x1']-2, $window['y1']-2, $colorGray);
            imagerectangle($image, $window['x0']+2, $window['y0']+2, $window['x1']-2, $window['y1']-2, $colorRed);
        }
        imagepng($image, 'test.png');
    }

    public function calcWindowsPositions($width, $height)
    {
        $ret = [];
        foreach ($this->layout->windows as $name => $window) {
            /** @var LayoutWindow $window */
            $ret[$name] = [
                'x0' => ceil($width * $window->x),
                'y0' => ceil($height * $window->y),
                'x1' => ceil($width * $window->x) + floor($width * $window->width),
                'y1' => ceil($height * $window->y) + floor($height * $window->height),
            ];
        }
        var_dump($ret);
        return $ret;
    }

}