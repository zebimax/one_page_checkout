<?php
namespace Form\Component\Text;
use Form\Component\TextComponent;

/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 13.02.15
 * Time: 16:38
 */

class Label extends TextComponent
{
    private $labelFor;
    public function setLabelFor($labelFor)
    {
        $this->labelFor = $labelFor;
    }

    public function make()
    {
        $labelFor = $this->labelFor ? $this->labelFor : $this->name;
        return "<label for='{$labelFor}'>{$this->text}</label>";
    }
}