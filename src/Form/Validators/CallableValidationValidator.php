<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 17:35
 */

namespace Form\Validators;


use Application\Traits\ErrorMessageTrait;
use Application\Traits\NamedTrait;
use Form\Validators\Traits\CallableValidationTrait;

class CallableValidationValidator extends AbstractCallableValidationValidator
{
    use CallableValidationTrait;
    use ErrorMessageTrait;
    use NamedTrait;

    public function __construct($name = null, $error = null, callable $validation = null)
    {
        $this->name = $name;
        $this->error = $error;
        $this->validation = $validation;
    }
}