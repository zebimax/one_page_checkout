<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 23.02.15
 * Time: 16:07
 */

namespace Application\Interfaces;


use Model\PaymentOrderInfo;

interface PaymentOrdersInfoModelAwareInterface
{
    /**
     * @param PaymentOrderInfo $paymentOrderInfo
     * @return mixed
     */
    function setPaymentOrdersInfoModel(PaymentOrderInfo $paymentOrderInfo);
}