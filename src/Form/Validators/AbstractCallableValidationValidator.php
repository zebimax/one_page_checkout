<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 17:34
 */

namespace Form\Validators;


use Form\Validators\Interfaces\CallableValidationValidatorInterface;
use Form\Validators\Interfaces\ErrorMessageValidatorInterface;
use Form\Validators\Interfaces\NamedValidatorInterface;

abstract class AbstractCallableValidationValidator
    implements CallableValidationValidatorInterface,
    ErrorMessageValidatorInterface,
    NamedValidatorInterface
{
    abstract public function setValidation(callable $validation);
    abstract public function getError();
    abstract public function getName();
}