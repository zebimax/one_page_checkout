<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 19:51
 */

namespace Payment\Traits;


use Form\Validators\Interfaces\ValidatorInterface;

trait ValidatorsConfigurableTrait
{
    protected $validators = [];

    /**
     * @return ValidatorInterface[]
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @param ValidatorInterface[] $validators
     * @return $this
     */
    public function setValidators(array $validators)
    {
        $this->validators = $validators;
        return $this;
    }
}