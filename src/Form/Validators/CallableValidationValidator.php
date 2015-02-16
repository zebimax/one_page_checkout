<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 17:35
 */

namespace Form\Validators;


use Form\Validators\Traits\CallableValidationTrait;
use Form\Validators\Traits\ErrorMessageValidatorTrait;
use Form\Validators\Traits\NamedValidatorTrait;

class CallableValidationValidator extends AbstractCallableValidationValidator
{
    use CallableValidationTrait;
    use ErrorMessageValidatorTrait;
    use NamedValidatorTrait;

    public function __construct($name = null, $error = null, callable $validation = null)
    {
        $this->name = $name;
        $this->error = $error;
        $this->validation = $validation;
    }
}