<?php

namespace ProfIT\Bbb\layout;

/**
 * Class Box
 * @package ProfIT\Bbb\layout
 * @property-read int $absX calculated absolute x
 * @property-read int $absY calculated absolute y
 * @property-read int $absW calculated absolute width
 * @property-read int $absH calculated absolute height
 */

class Box {
    public $x;
    public $y;
    public $w;
    public $h;

    public $relX;
    public $relY;
    public $relW;
    public $relH;
    public $minW;
    public $minH;

    /** @var Box */
    public $parent;
    public $children = [];
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
        $x = $this->absX;
        $y = $this->absY;

        return [[$x, $y], [$x + $this->absW, $y + $this->absH]];
    }

    public function render($canvas)
    {
        if ($this->hidden) return;

        if ($this->parent) {
            $colorGray = imagecolorallocate($canvas, 0xCC, 0xCC, 0xCC);
            $colorRed = imagecolorallocate($canvas, 0xCC, 0x00, 0x00);
            $colorWhite = imagecolorallocate($canvas, 0xFF, 0xFF, 0xFF);
            $c = $this->getCoordinates();

            print_r($c);

            imagefilledrectangle($canvas, $c[0][0], $c[0][1], $c[1][0], $c[1][1], $colorGray);
            imagerectangle($canvas, $c[0][0], $c[0][1], $c[1][0], $c[1][1], $colorWhite);
        }

        foreach ($this->children as $child) {
            /** @var Window $child */
            $child->render($canvas);
        }
    }

    public function addChild(Box $child)
    {
        $child->parent = $this;

        $this->children[] = $child;
    }

    public function __get($name)
    {
        if (strpos($name, 'abs') === 0) {
            $prop = substr($name, 3);

            if (! in_array($prop, ['X', 'Y', 'W', 'H'])) {
                throw new \Exception("Property $name is not defined");
            }

            if (($direct = $this->{strtolower($prop)}) !== null) return $direct;

            return $this->{'getAbs'. $prop}($this->parent);
        }

        throw new \Exception("Property $name is not defined");
    }

    public function getAbsX(Box $parent)
    {
        $offset = $this->relX * $parent->absW;

        return ceil($parent->absX + $offset);
    }

    public function getAbsY(Box $parent)
    {
        $offset = $this->relY * $parent->absH;

        return ceil($parent->absY + $offset);
    }

    public function getAbsW(Box $parent)
    {
        $w = $this->relW * $parent->absW;

        //if ($w < $this->minW) $w = $this->minW;

        return floor($w);
    }

    public function getAbsH(Box $parent)
    {
        $h = $this->relH * $parent->absH;

        //if ($h < $this->minH) $h = $this->minH;

        return floor($h);
    }
}