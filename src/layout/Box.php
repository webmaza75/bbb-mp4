<?php

namespace ProfIT\Bbb\layout;

class Box {
    public $x;
    public $y;
    public $w;
    public $h;

    public $rX;
    public $rY;
    public $rW;
    public $rH;
    public $minW;
    public $minH;

    /** @var Box */
    public $parent;
    public $children = [];
    public $canvas;
    public $hidden;

    public function __construct(array $props = [])
    {
        foreach ($props as $key => $val) {
            if (null !== $val) {
                $this->$key = $val;
            }
        }
    }


    public function getCoordinates()
    {
        $x = $this->absX();
        $y = $this->absY();

        return [[$x, $y], [$x + $this->absW(), $y + $this->absH()]];
    }

    public function render()
    {
        if ($this->hidden) return;

        if ($parent = $this->parent) {
            $canvas = $parent->canvas;

            $colorGray = imagecolorallocate($canvas, 0xCC, 0xCC, 0xCC);
            $colorRed = imagecolorallocate($canvas, 0xCC, 0x00, 0x00);
            $colorWhite = imagecolorallocate($canvas, 0xFF, 0xFF, 0xFF);
            $c = $this->getCoordinates();

            echo $this->name;
            print_r($c);

            imagefilledrectangle($canvas, $c[0][0], $c[0][1], $c[1][0], $c[1][1], $colorGray);
            imagerectangle($canvas, $c[0][0], $c[0][1], $c[1][0], $c[1][1], $colorWhite);
        } else {
            $this->canvas = imagecreatetruecolor($this->absW(), $this->absH());
        }


        foreach ($this->children as $child) {
            /** @var Window $child */
            $child->render();
        }
    }

    public function setChildren(array $children)
    {
        foreach ($children as $child) {
            /** @var Window $child */
            $child->parent = $this;
            $child->canvas = $this->canvas;
        }

        $this->children = $children;
    }


    public function absX()
    {
        if ($this->x !== null) return $this->x;

        // ceil($width * $window->x) + floor($width * $window->width) - 1

        return ceil($this->rX * $this->parent->absX()) + floor($this->rX * $this->parent->absW()) - 1;
    }

    public function absY()
    {
        if ($this->y !== null) return $this->y;

        return ceil($this->rY * $this->parent->absY()) + floor($this->rY * $this->parent->absH()) - 1;

    }

    public function absW()
    {
        if ($this->w !== null) return $this->w;

        $w = $this->rW * $this->parent->absW();

        //if ($w < $this->minW) $w = $this->minW;

        return floor($w);
    }

    public function absH()
    {
        if ($this->h !== null) return $this->h;

        $h = $this->rH * $this->parent->absH();

        //if ($h < $this->minH) $h = $this->minH;

        return floor($h);
    }
}