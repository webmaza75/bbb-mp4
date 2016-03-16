<?php

namespace ProfIT\Bbb;

class Layout
{

    protected $xml;

    public $windows;

    /**
     * Layout constructor.
     * @param string $filename
     * @throws \Exception
     */
    public function __construct(string $filename, string $name)
    {
        $xml = @simplexml_load_file($filename);
        if (false === $xml) {
            throw new \Exception('Layout file can not be loaded: ' . $filename);
        }
        $this->xml = $xml;
        $this->windows = $this->getLayoutWindows($name);
    }

    /**
     * @param string $name
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function getLayout(string $name)
    {
        $res = @$this->xml->xpath('//layouts/layout[@name="bbb.layout.name.' . $name . '"]');
        if (false === $res) {
            throw new \Exception('Invalid layout');
        }
        return $res[0];
    }

    public function getLayoutWindows(string $name)
    {
        $layout = $this->getLayout($name);
        $ret = [];
        foreach ($layout->window as $window) {
            $ret[(string)$window->attributes()->name] = new LayoutWindow([
                'x' => (float)$window->attributes()->x,
                'y' => (float)$window->attributes()->y,
                'width' => (float)$window->attributes()->width,
                'height' => (float)$window->attributes()->height,
                'minWidth' => (float)$window->attributes()->minWidth ?: null,
                'minHeight' => (float)$window->attributes()->minHeight ?: null,
            ]);
        }
        return $ret;
    }

}