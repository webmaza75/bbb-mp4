<?php

namespace ProfIT\Bbb;

class Layout
{
    protected $xml;
    public $name;
    public $windows;

    //protected $skippedWindows = ['NotesWindow'];
    protected $skippedWindows = ['ViewersWindow', 'NotesWindow', 'BroadcastWindow', 'UsersWindow'];
    public $offsetH = 40/720; // Отступ сверху для меню с кнопками
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
                $ret['PresentationWindow']->relH = $ret['PresentationWindow']->relH - $this->offsetH;
                $ret[$name]->relH = $ret['PresentationWindow']->relH/3 + 0.02 - $this->offsetH;
                // + ~1 пиксель, т.к. блок располагается ниже по вертикали
                $ret[$name]->relY = $ret['PresentationWindow']->relH - $ret[$name]->relH + $this->offsetH + 0.001;
            }
            if('ListenersWindow' == $name && 'defaultlayout' == $this->name) {
                $ret[$name]->relY = $this->offsetH;
                $ret[$name]->relH = $ret['VideoDock']->relY - $this->offsetH;
            }
            if('ChatWindow' == $name && 'defaultlayout' == $this->name) {
                $ret[$name]->relH = $ret['PresentationWindow']->relH;
                $ret[$name]->relY = $this->offsetH;
            }
            if('PresentationWindow' == $name && 'defaultlayout' == $this->name) {
                $ret[$name]->relY = $this->offsetH;
            }
        }
        return $ret;
    }

    public function setStyleSheet($filename)
    {
        $styleSheet = new style\Sheet($filename);
    }

}