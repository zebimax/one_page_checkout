<?php

namespace Form\Component;


class TextComponent extends AbstractFormComponent
{
    protected $text;

    public function __construct($name, $text)
    {
        $this->name = $name;
        $this->text = $text;
    }

    public function make()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }
}