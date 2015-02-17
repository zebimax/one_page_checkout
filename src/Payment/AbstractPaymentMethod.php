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
    const PAYMENT_URL = '/payment';
    protected $code;
    protected $transactionErrors = [];
    protected $transactionInfo = [];

    abstract public function addOwnFieldsToCheckoutForm(CheckoutForm $checkoutForm);
    abstract public function process($orderId, array $data);
    abstract public function extractPaymentInfo(array $data);
    public function isCanProcess(CheckoutForm $checkoutForm)
    {
        return $checkoutForm->getFormDataValue('payment_method') === $this->code;
    }

    /**
     * @return array
     */
    public function getTransactionInfo()
    {
        return $this->transactionInfo;
    }

    /**
     * @return array
     */
    public function getTransactionErrors()
    {
        return $this->transactionErrors;
    }

    protected function getPaymentDescription(array $data)
    {
        return sprintf(
            '%s payment from %s for %s %s, email: %s of % product, quantity: %s',
            $this->getCode(),
            PRODUCT_HOST,
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            PRODUCT_NAME,
            $data['quantity']
        );
    }

    /**
     * @return string
     */
    protected function getReturnUrl()
    {
        return PRODUCT_HOST . self::PAYMENT_URL;
    }
}