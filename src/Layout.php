<?php

namespace ProfIT\Bbb;

class Layout
{
    protected $xml;
    public $name;
    public $windows;

    //protected $skippedWindows = ['NotesWindow'];
    protected $skippedWindows = ['ViewersWindow', 'NotesWindow', 'BroadcastWindow', 'UsersWindow'];

    /**
     * Layout constructor.
     * @param string $filename
     * @throws \Exception
     */
    public function __construct(string $filename, string $name)
    {
        $this->name = $name;
        $xml = @simplexml_load_file($filename);

        if (false === $xml) {
            throw new \Exception('Layout file can not be loaded: ' . $filename);
        }

        $this->xml = $xml;
    }

    /**
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    protected function getData()
    {
        $name = $this->name;
        $res = @$this->xml->xpath('//layouts/layout[@name="bbb.layout.name.' . $name . '"]');

        if (false === $res) {
            throw new \Exception('Invalid layout');
        }

        return $res[0];
    }

    public function getWindows()
    {
        $data = $this->getData();
        $ret = [];

        foreach ($data->window as $window) {
            $name = (string) $window->attributes()->name;

            if ('defaultlayout' == $this->name && in_array($name, $this->skippedWindows)) {
                continue;
            }

            $attributes = $window->attributes();

            if (! $attributes->width || ! $attributes->height) continue;

            $ret[$name] = new layout\Window([
                'name'   => $name,
                'relX'   => (float) $attributes->x,
                'relY'   => (float) $attributes->y,
                'relW'   => (float) $attributes->width,
                'relH'   => (float) $attributes->height,
                'minW'   => (int)   $attributes->minWidth ?: null,
                'minH'   => (int)   $attributes->minHeight ?: null,
                'hidden' => $attributes->hidden == true,
                'pad'    => 2
            ]);

            /**
             * Изменены размеры блоков за счет удаления одного из левой колонки:
             * верхний блок - список студентов (слушателей) - ListenersWindow,
             * нижний блок - видео-камеры - VideoDock
             */

           // отправная точка - высота окна PresentationWindow
            if('VideoDock' == $name && 'defaultlayout' == $this->name) {
                $ret[$name]->relH = $ret['PresentationWindow']->relH/3 + 0.02;
                $ret[$name]->relY = $ret['PresentationWindow']->relH - $ret[$name]->relH;
            }
            if('ListenersWindow' == $name && 'defaultlayout' == $this->name) {
                $ret[$name]->relY = 0;
                $ret[$name]->relH = $ret['VideoDock']->relY;
            }
            if('ChatWindow' == $name && 'defaultlayout' == $this->name) {
                    $ret[$name]->relH = $ret['PresentationWindow']->relH;
            }
        }
        return $ret;
    }

    public function setStyleSheet($filename)
    {
        $styleSheet = new style\Sheet($filename);
    }

}