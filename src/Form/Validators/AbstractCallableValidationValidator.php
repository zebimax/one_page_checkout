<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 17:34
 */

namespace Form\Validators;


use Application\Interfaces\ErrorMessageInterface;
use Application\Interfaces\NamedInterface;
use Form\Validators\Interfaces\CallableValidationValidatorInterface;

abstract class AbstractCallableValidationValidator
    implements CallableValidationValidatorInterface,
    ErrorMessageInterface,
    NamedInterface
{
    abstract public function setValidation(callable $validation);
    abstract public function getError();
    abstract public function getName();
}