<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 13.02.15
 * Time: 19:46
 */

namespace Payment;


use Form\CheckoutForm;
use Form\Component\Field\Input;
use Form\Component\Text\Label;
use Form\Component\TextComponent;
use Form\Validators\CallableValidationValidator;
use Form\ValidatorsConfigurableInterface;
use Payment\Api\Afterpay;
use Payment\Interfaces\ActivatableValidatorsInterface;
use Payment\Traits\ValidatorsConfigurableTrait;

class AfterPayPayment extends AbstractPaymentMethod
    implements ValidatorsConfigurableInterface,
    ActivatableValidatorsInterface
{
    use ValidatorsConfigurableTrait;
    private $sharedComponents = [];
    protected $api;

    public function __construct(AfterPay $api)
    {
        $this->code = 'afterpay';
        $this->api = $api;
    }

    public function addOwnFieldsToCheckoutForm(CheckoutForm $checkoutForm)
    {
        $checkoutForm->addCustomField('afterpay', function() {
            return
                $this->getSharedTextComponent('text', '', '<div class="afterpay_fields">')->make() .
                $this->getSharedTextComponent('text', '', '<span>AfterPay</span><br>')->make() .
                $this->getSharedTextComponent('label', 'afterpay[day_of_birth]', 'Day of birth')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[day_of_birth]', 'value' => 1])->make() .
                $this->getSharedTextComponent('label', 'afterpay[month_of_birth]', 'Month')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[month_of_birth]', 'value' => 1])->make() .
                $this->getSharedTextComponent('label', 'afterpay[year_of_birth]', 'Year')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[year_of_birth]', 'value' => 1989])->make() .
                $this->getSharedTextComponent('text', '', '</div>')->make();
        });
    }

    public function checkSuccessOrder($paymentOrderId)
    {
        return true;
    }

    public function process($orderId, array $data)
    {
        $this->transactionInfo = ['order_id' => $orderId, 'payment_order_id' => 'sfasf'];
        return true;
        $sku = 'PRODUCT1';
        $name = 'Product name 1';
        $qty = 3;
        $price = 3000; // in cents
        $tax_category = 1; // 1 = high, 2 = low, 3, zero, 4 no tax
        $this->api->create_order_line( $sku, $name, $qty, $price, $tax_category );
        // Set up the bill to address
        $aporder['billtoaddress']['city'] = 'Heerenveen';
        $aporder['billtoaddress']['housenumber'] = '90';
        $aporder['billtoaddress']['isocountrycode'] = 'NL';
        $aporder['billtoaddress']['postalcode'] = '8441ER';
        $aporder['billtoaddress']['referenceperson']['dob'] = '1980-12-12T00:00:00';
        $aporder['billtoaddress']['referenceperson']['email'] = 'test@afterpay.nl';
        $aporder['billtoaddress']['referenceperson']['gender'] = 'M';
        $aporder['billtoaddress']['referenceperson']['initials'] = 'A';
        $aporder['billtoaddress']['referenceperson']['isolanguage'] = 'NL';
        $aporder['billtoaddress']['referenceperson']['lastname'] = 'de Tester';
        $aporder['billtoaddress']['referenceperson']['phonenumber'] = '0513744112';
        $aporder['billtoaddress']['streetname'] =  'KR Poststraat';

// Set up the ship to address
        $aporder['shiptoaddress']['city'] = 'Heerenveen';
        $aporder['shiptoaddress']['housenumber'] = '90';
        $aporder['shiptoaddress']['isocountrycode'] = 'NL';
        $aporder['shiptoaddress']['postalcode'] = '8441ER';
        $aporder['shiptoaddress']['referenceperson']['dob'] = '1980-12-12T00:00:00';
        $aporder['shiptoaddress']['referenceperson']['email'] = 'test@afterpay.nl';
        $aporder['shiptoaddress']['referenceperson']['gender'] = 'M';
        $aporder['shiptoaddress']['referenceperson']['initials'] = 'A';
        $aporder['shiptoaddress']['referenceperson']['isolanguage'] = 'NL';
        $aporder['shiptoaddress']['referenceperson']['lastname'] = 'de Tester';
        $aporder['shiptoaddress']['referenceperson']['phonenumber'] = '0513744112';
        $aporder['shiptoaddress']['streetname'] =  'KR Poststraat';

// Set up the additional information
        $aporder['ordernumber'] = 'ORDER123';
        $aporder['bankaccountnumber'] = '12345'; // or IBAN 'NL32INGB0000012345';
        $aporder['currency'] = 'EUR';
        $aporder['ipaddress'] = $_SERVER['REMOTE_ADDR'];

// Create the order object for B2C or B2B
        $this->api->set_order($aporder, 'B2C');
        $authorisation['merchantid'] = '137393105';
        $authorisation['portfolioid'] = '9';
        $authorisation['password'] = 'f31f4a4417';
        $modus = 'test'; // or 'live' for production
        $this->api->do_request($authorisation, $modus);
        /*
         *     afterPayMerchantID: '137393105'
    afterPayPortfolioID: '9'
    afterPayPassword: f31f4a4417
         *
         *
         * */
    }

    public function extractPaymentInfo(array $data)
    {
        return [];
    }

    public function getCode()
    {
        return $this->code;
    }

    function getOrderInfo($orderId)
    {
        return [];
    }

    function activateValidators()
    {
        $this->validators = [
            new CallableValidationValidator(
                'afterpay',
                'not valid day_of_birth',
                function ($value) {
                    return isset($value['day_of_birth']) &&
                    $value['day_of_birth'] > 0 &&
                    $value['day_of_birth'] < 32;
                }
            ),
            new CallableValidationValidator(
                'afterpay',
                'not valid month_of_birth',
                function ($value) {
                    return isset($value['month_of_birth']) &&
                    $value['month_of_birth'] > 0 &&
                    $value['month_of_birth'] < 13;
                }
            ),
            new CallableValidationValidator(
                'afterpay',
                'not valid year_of_birth',
                function ($value) {
                    return isset($value['year_of_birth']) &&
                    $value['year_of_birth'] > 1900 &&
                    $value['year_of_birth'] < 2100;
                }
            )
        ];
    }

    /**
     * @param $type
     * @param $name
     * @param $text
     * @return TextComponent
     * @throws \Exception
     */
    private function getSharedTextComponent($type, $name, $text)
    {
        if (!isset($this->sharedComponents[$type]) || !($this->sharedComponents[$type] instanceof TextComponent)) {
            switch ($type) {
                case 'label':
                    $component = new Label('xxx', 'xxx');
                    break;
                case 'text':
                    $component = new TextComponent('xxx', 'xxx');
                    break;
                default:
                    throw new \Exception(sprintf('Unknown form component %s in %d:%s', $type, __LINE__,  __CLASS__));
                    break;
            }
            $this->sharedComponents[$type] = $component;
        }
        $this->sharedComponents[$type]->setText($text);
        $this->sharedComponents[$type]->setName($name);
        return $this->sharedComponents[$type];
    }

    /**
     * @param $params
     * @return Input
     */
    private function getSharedInputComponent($params)
    {
        if (!isset($this->sharedComponents['input']) || !($this->sharedComponents['input'] instanceof Input)) {
            $this->sharedComponents['input'] = new Input();
        }
        $this->sharedComponents['input']->setOptions($params);
        return $this->sharedComponents['input'];
    }
}