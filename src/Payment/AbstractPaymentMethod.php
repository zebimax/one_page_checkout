<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 13.02.15
 * Time: 12:23
 */
namespace Payment;


use Form\CheckoutForm;

abstract class AbstractPaymentMethod implements PaymentMethodInterface
{
    protected $code;

    abstract public function addOwnFieldsToCheckoutForm(CheckoutForm $checkoutForm);
    abstract public function process(CheckoutForm $checkoutForm);
    public function isCanProcess(CheckoutForm $checkoutForm)
    {
        return $checkoutForm->getFormDataValue('payment_method') === $this->code;
    }
}