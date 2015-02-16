<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 17:33
 */

namespace Form\Validators\Interfaces;


interface CallableValidationValidatorInterface extends ValidatorInterface
{
    function setValidation(callable $validation);
}