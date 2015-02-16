<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 16:13
 */

namespace Payment;


use Form\CheckoutForm;

interface PaymentMethodInterface
{
    function addOwnFieldsToCheckoutForm(CheckoutForm $checkoutForm);
    function isCanProcess(CheckoutForm $checkoutForm);
    function process(CheckoutForm $checkoutForm);
    function extractPaymentInfo(array $data);
    function getCode();
}