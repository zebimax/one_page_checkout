<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 17.02.15
 * Time: 13:26
 */

namespace Payment\Traits;


trait ReturnRedirectUrlTrait
{
    protected $redirectUrl;

    /**
     * @return mixed
     */
    public function returnRedirectUrl()
    {
        return $this->redirectUrl;
    }
}