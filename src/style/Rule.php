<?php

namespace ProfIT\Bbb\style;

class Rule
{
    public $selector;
    public $properties;

    public function __construct($selector, $declaration)
    {
        $this->selector = trim($selector);
        $declaration = trim($declaration);

        foreach (explode(';', $declaration) as $row) {
            list ($name, $value) = explode(':', $row);

            $name = trim($name);

            if (! $name) continue;

            $this->properties[$name] = trim($value);
        }
    }
}