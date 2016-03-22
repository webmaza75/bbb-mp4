<?php

namespace ProfIT\Bbb;

class Layout
{

    protected $xml;

    public $windows;

    protected $skippedWindows = ['NotesWindow'];

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
    protected function getLayout(string $name)
    {
        $res = @$this->xml->xpath('//layouts/layout[@name="bbb.layout.name.' . $name . '"]');
        if (false === $res) {
            throw new \Exception('Invalid layout');
        }
        return $res[0];
    }

    protected function getLayoutWindows(string $name)
    {
        $layout = $this->getLayout($name);
        $ret = [];

        foreach ($layout->window as $window) {
            $name = (string)$window->attributes()->name;
            /*
            if (in_array($name, $this->skippedWindows)) {
                continue;
            }
            */

            $attributes = $window->attributes();

            if (! $attributes->width || ! $attributes->height) continue;

            $ret[$name] = new LayoutWindow([
                'name'   => $name,
                'rX'     => (float) $attributes->x,
                'rY'     => (float) $attributes->y,
                'rW'     => (float) $attributes->width,
                'rH'     => (float) $attributes->height,
                'minW'   => (int)   $attributes->minWidth ?: null,
                'minH'   => (int)   $attributes->minHeight ?: null,
                'hidden' => $attributes->hidden == true,
            ]);

            var_dump($ret[$name]);
        }

        return $ret;
    }

}