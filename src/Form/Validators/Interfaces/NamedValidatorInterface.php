<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 16:49
 */

namespace Form\Validators\Interfaces;


interface NamedValidatorInterface extends ValidatorInterface
{
    function getName();
}