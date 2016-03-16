<?php

namespace ProfIT\Bbb;

class Layout
{

    protected $xml;

    /**
     * Layout constructor.
     * @param string $filename
     * @throws \Exception
     */
    public function __construct(string $filename)
    {
        $xml = @simplexml_load_file($filename);
        if (false === $xml) {
            throw new \Exception('Layout file can not be loaded: ' . $filename);
        }
        $this->xml = $xml;
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

    public function getLayoutWindow(string $name)
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