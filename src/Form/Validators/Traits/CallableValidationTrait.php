<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 17:37
 */

namespace Form\Validators\Traits;


trait CallableValidationTrait
{
    protected $validation;
    public function validate($value)
    {
        if (!$this->validation) {
            throw new \Exception('Callable validation doesn\'t set');
        }
        return call_user_func($this->validation, $value);
    }

    public function setValidation(callable $validation)
    {
        $this->validation = $validation;
        return $this;
    }
}