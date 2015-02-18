<?php

namespace Form\Component;


class TextComponent extends AbstractFormComponent
{
    protected $text;

    /**
     * @param $name
     * @param $text
     */
    public function __construct($name, $text)
    {
        $this->name = $name;
        $this->text = $text;
    }

    /**
     * @return mixed
     */
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