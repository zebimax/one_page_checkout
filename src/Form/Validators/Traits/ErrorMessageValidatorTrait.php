<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 17:50
 */

namespace Form\Validators\Traits;


trait ErrorMessageValidatorTrait
{
    protected $error = '';

    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }
}