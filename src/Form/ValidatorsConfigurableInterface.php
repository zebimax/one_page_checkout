<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 16:56
 */

namespace Form;



use Form\Validators\Interfaces\ValidatorInterface;

interface ValidatorsConfigurableInterface
{
    function getValidators();

    /**
     * @param ValidatorInterface[] $validators
     * @return mixed
     */
    function setValidators(array $validators);
}