<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 12.02.15
 * Time: 19:25
 */
use Form\Validators\CallableValidationValidator;
use Payment\AfterPayPayment;
use Payment\Api\AfterPay;
use Payment\Api\GingerApi;
use Payment\IdealPayment;
error_reporting(E_ALL);
ini_set('display_errors', true);
return [
    'product_id' => 'product_id',
    'product_ean' => 'product_ean', // EAN code,
    'product_name' => 'supradyn vitamine gummies',
    'product_price' => 10,
    'product_price_in_cents' => 1000,
    'product_tax_percent' => 6,
    'product_tax_category' => 1, // 1 = high, 2 = low, 3, zero, 4 no tax
    'product_raw_total' => 1000000,
    'ginger_api_key' => '64c0b3be0b8d4c23b44140a3a8b5234b',
    'afterpay_merchant_id' => 300005635,
    'afterpay_portofolio' => 18,
    'afterpay_password' => '5ad729393a',
    'afterpay_mode' => 'test',
    'mysql' => [
        'host' => 'localhost',
        'port' => 3601,
        'user' => 'root',
        'password' => '',
        'db' => 'product'
    ],
    'checkout_form_options' => [
        ['name' => 'label', 'params' => ['labelFor' => 'first_name', 'text' => 'First Name']],
        ['name' => 'first_name'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'last_name', 'text' => 'Last Name']],
        ['name' => 'last_name'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'phone', 'text' => 'Phone']],
        ['name' => 'phone'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'email', 'text' => 'Email']],
        ['name' => 'email'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'profession', 'text' => 'Profession']],
        ['name' => 'profession'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'post_code', 'text' => 'Post code']],
        ['name' => 'post_code'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'house_number', 'text' => 'House number']],
        ['name' => 'house_number'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'street', 'text' => 'Street']],
        ['name' => 'street'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'location', 'text' => 'location']],
        ['name' => 'location'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'city', 'text' => 'city']],
        ['name' => 'city'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'sex', 'text' => 'sex']],
        ['name' => 'sex'],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'payment_method', 'text' => 'Payment']],
        ['name' => 'payment_method', 'params' => ['selected' => 'ideal', 'options' => [['value' => 'ideal', 'name' => 'iDEAL'], ['value' => 'afterpay', 'name' => 'afterpay']]]],
        ['name' => 'br_tag'],
        ['name' => 'label', 'params' => ['labelFor' => 'quantity', 'text' => 'Product quantity']],
        ['name' => 'quantity', 'params' => ['value' => '1']],
        ['name' => 'br_tag'],
        ['name' => 'checkout', 'params' => ['value' => 'checkout']],
    ],
    'available_payment_methods' => [
        'ideal' => function(ConfigInterface $configInterface) {
            return new IdealPayment(new GingerApi($configInterface->get('ginger_api_key')));
        },
        'afterpay' => function (ConfigInterface $configInterface) {
            return new AfterPayPayment(new AfterPay(
                $configInterface->get('afterpay_merchant_id'),
                $configInterface->get('afterpay_portofolio'),
                $configInterface->get('afterpay_password'),
                $configInterface->get('afterpay_mode')
            ));
        }
    ],
    'checkout_form_validators' => [
        function() {
            return new CallableValidationValidator(
                'first_name',
                'Not valid name',
                function($value) {
                    return preg_match('/^[A-Za-z0-9]{3,64}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'last_name',
                'Not valid last name',
                function($value) {
                    return preg_match('/^[A-Za-z0-9 ]{3,64}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'phone',
                'Not valid phone',
                function($value) {
                    return preg_match('/^\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'email',
                'Not valid email',
                function($value) {
                    return preg_match('/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'profession',
                'Not valid profession',
                function($value) {
                    return preg_match('/^[A-Za-z0-9 ]{3,255}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'post_code',
                'Not valid post_code',
                function($value) {
                    return preg_match('/^[A-Za-z0-9 ]{3,32}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'house_number',
                'Not valid house number',
                function($value) {
                    return preg_match('/^[A-Za-z0-9 ]{1,10}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'street',
                'Not valid street',
                function($value) {
                    return preg_match('/^[A-Za-z0-9 ]{2,255}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'location',
                'Not valid location',
                function($value) {
                    return preg_match('/^[A-Za-z0-9 ]{3,255}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'quantity',
                'Not valid quantity',
                function($value) {
                    return (int)$value;
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'country',
                'Not valid country',
                function($value) {
                    return preg_match('/^[A-Za-z]{2}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'payment_method',
                'Not valid payment method',
                function($value) {
                    return preg_match('/^[A-Za-z0-9 ]{3,64}$/', $value);
                }
            );
        },
        function() {
            return new CallableValidationValidator(
                'sex',
                'Not valid sex',
                function($value) {
                    return strtolower($value) == 'm' || strtolower($value) == 'w';
                }
            );
        },
    ],
];


