<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 23.02.15
 * Time: 15:45
 */

namespace Payment\Traits;


trait CurrentPaymentTransactionInfoTrait
{
    protected $currentPaymentTransactionInfo;

    public function getCurrentPaymentTransactionInfo()
    {
        return $this->currentPaymentTransactionInfo;
    }
}