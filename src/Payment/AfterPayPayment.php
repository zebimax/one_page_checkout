<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 13.02.15
 * Time: 19:46
 */

namespace Payment;


use Application\Interfaces\PaymentOrdersInfoModelAwareInterface;
use Form\CheckoutForm;
use Form\Component\Field\Input;
use Form\Component\Text\Label;
use Form\Component\TextComponent;
use Form\Validators\CallableValidationValidator;
use Form\ValidatorsConfigurableInterface;
use Model\PaymentOrderInfo;
use Payment\Api\Afterpay;
use Payment\Interfaces\ActivatableValidatorsInterface;
use Payment\Interfaces\CurrentPaymentTransactionInfoInterface;
use Payment\Traits\CurrentPaymentTransactionInfoTrait;
use Payment\Traits\ValidatorsConfigurableTrait;

class AfterPayPayment extends AbstractPaymentMethod
    implements ValidatorsConfigurableInterface,
    ActivatableValidatorsInterface,
    CurrentPaymentTransactionInfoInterface,
    PaymentOrdersInfoModelAwareInterface
{
    use ValidatorsConfigurableTrait;
    use CurrentPaymentTransactionInfoTrait;

    const SUCCESS_STATUS_CODE = 'A';
    const AFTER_PAY = 'afterpay';

    private $sharedComponents = [];
    protected $api;
    private $currency = 'EUR';
    /** @var PaymentOrderInfo */
    protected $paymentOrdersInfoModel;

    public function __construct(AfterPay $api, array $options = [])
    {
        $this->code = 'afterpay';
        $this->api = $api;
        if (isset($options['currency'])) {
            $this->currency = $options['currency'];
        }
    }

    /**
     * @return PaymentOrderInfo
     * @throws \Exception
     */
    public function getPaymentOrdersInfoModel()
    {
         if (!$this->paymentOrdersInfoModel) {
             throw new \Exception('payment order info model must be set before!');
         }
        return $this->paymentOrdersInfoModel;
    }

    /**
     * @param PaymentOrderInfo $paymentOrdersInfoModel
     * @return $this
     */
    public function setPaymentOrdersInfoModel(PaymentOrderInfo $paymentOrdersInfoModel)
    {
        $this->paymentOrdersInfoModel = $paymentOrdersInfoModel;
        return $this;
    }

    /**
     * @param CheckoutForm $checkoutForm
     */
    public function addOwnFieldsToCheckoutForm(CheckoutForm $checkoutForm)
    {
        $checkoutForm->addCustomField('afterpay', function() {
            return
                $this->getSharedTextComponent('text', '', '<div class="afterpay_fields">')->make() .
                $this->getSharedTextComponent('text', '', '<span>AfterPay</span><br>')->make() .
                $this->getSharedTextComponent('label', 'afterpay[day_of_birth]', 'Day of birth')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[day_of_birth]'])->make() .
                $this->getSharedTextComponent('label', 'afterpay[month_of_birth]', 'Month')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[month_of_birth]'])->make() .
                $this->getSharedTextComponent('label', 'afterpay[year_of_birth]', 'Year')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[year_of_birth]'])->make() .
                $this->getSharedTextComponent('text', '', '</div>')->make();
        });
    }

    /**
     * @param $paymentOrderId
     * @return bool
     * @throws \Exception
     */
    public function checkSuccessOrder($paymentOrderId)
    {
        $paymentInfo = $this->getPaymentOrdersInfoModel()->getPaymentInfo($paymentOrderId);
        return isset($paymentInfo['statusCode']) && $paymentInfo['statusCode'] == self::SUCCESS_STATUS_CODE;
    }

    /**
     * @param $orderId
     * @param array $data
     * @return bool
     */
    public function process($orderId, array $data)
    {
        $this->api->createOrderLine(
            $data['product_id'],
            $data['product_name'],
            $data['quantity'],
            $data['product_price_in_cents'],
            $data['product_tax_category']
        );
        $data = array_merge($data, $this->makeAdditionalInfo($data));
        $order['billtoaddress']['city'] = $data['city'];
        $order['billtoaddress']['housenumber'] = $data['house_number'];
        $order['billtoaddress']['isocountrycode'] = $data['country'];
        $order['billtoaddress']['postalcode'] = $data['post_code'];
        $order['billtoaddress']['referenceperson']['dob'] = $data['dob'];
        $order['billtoaddress']['referenceperson']['email'] = $data['email'];
        $order['billtoaddress']['referenceperson']['gender'] = $data['sex'];//MV
        $order['billtoaddress']['referenceperson']['initials'] = $data['initials'];//'A';
        $order['billtoaddress']['referenceperson']['isolanguage'] = $data['country_language'];
        $order['billtoaddress']['referenceperson']['lastname'] = $data['last_name'];
        $order['billtoaddress']['referenceperson']['phonenumber'] = $data['phone'];
        $order['billtoaddress']['phonenumber1'] = $data['phone'];
        $order['billtoaddress']['streetname'] =  $data['street_name'];

        $order['shiptoaddress']['city'] = $data['ship_city'];
        $order['shiptoaddress']['housenumber'] = $data['ship_house_number'];
        $order['shiptoaddress']['isocountrycode'] = $data['ship_country_code'];
        $order['shiptoaddress']['postalcode'] = $data['ship_post_code'];
        $order['shiptoaddress']['referenceperson']['dob'] = $data['ship_dob'];
        $order['shiptoaddress']['referenceperson']['email'] = $data['ship_email'];
        $order['shiptoaddress']['referenceperson']['gender'] = $data['ship_sex'];;
        $order['shiptoaddress']['referenceperson']['initials'] = $data['ship_initials'];
        $order['shiptoaddress']['referenceperson']['isolanguage'] = $data['ship_language'];
        $order['shiptoaddress']['referenceperson']['lastname'] = $data['ship_last_name'];
        $order['shiptoaddress']['referenceperson']['phonenumber'] = $data['ship_phone'];
        $order['shiptoaddress']['streetname'] =  $data['ship_street_name'];

        $order['ordernumber'] = 'ORDER_' . $orderId;
        $order['bankaccountnumber'] = $data['bank_account_number'];
        $order['currency'] = $data['currency'];
        $order['ipaddress'] = $data['ip_address'];
        $this->api->setOrder($order, 'B2C');
        $this->api->doRequest();
        $orderResult = $this->api->getOrderResult();
        if (isset($orderResult->return->statusCode) && $orderResult->return->statusCode == self::SUCCESS_STATUS_CODE) {
            $this->transactionInfo = [
                'order_id' => $orderId,
                'payment_order_id' => $orderResult->return->afterPayOrderReference
            ];
            $this->currentPaymentTransactionInfo = (array)$orderResult->return;
            return true;
        } elseif (isset($orderResult->return->rejectDescription)) {
            $this->transactionErrors[] = $orderResult->return->rejectDescription;
        } elseif(isset($orderResult->return->failures)) {
            foreach ((array)$orderResult->return->failures as $failure) {
                $this->transactionErrors[] = $failure->suggestedvalue;
            }
        } else {
            $this->transactionErrors[] = 'Unknown error(AfterPay)';
        }
        return false;
    }

    /**
     * @param array $data
     * @return array
     */
    private function makeAdditionalInfo(array $data)
    {
        $dob = \DateTime::createFromFormat(
            'Y-m-d',
            "{$data['afterpay']['year_of_birth']}-{$data['afterpay']['month_of_birth']}-{$data['afterpay']['day_of_birth']}"
        )->format('c');

        $country = $data['country'];
        $postCode = strtoupper($data['post_code']);
        $sex = (strtolower($data['sex']) == 'w') ? 'V' : 'M';
        $initials = strtoupper($data['first_name']{0});
        return [
            'post_code' => $postCode,
            'dob' => $dob,
            'sex' => $sex,
            'initials' => $initials,
            'country_language' => $country,
            'street_name' => $data['street'],
            'ship_city' => $data['city'],
            'ship_house_number' => $data['house_number'],
            'ship_country_code' => $country,
            'ship_post_code' => $postCode,
            'ship_dob' => $dob,
            'ship_email' => $data['email'],
            'ship_sex' => $sex,
            'ship_initials' => $initials,
            'ship_language' => $country,
            'ship_last_name' => $data['last_name'],
            'ship_phone' => $data['phone'],
            'ship_street_name' => $data['street'],
            'bank_account_number' => '12345',// or IBAN 'NL32INGB0000012345';
            'currency' => $this->currency,
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $orderId
     * @return array
     */
    public function getOrderInfo($orderId)
    {
        $paymentInfo = $this->getPaymentOrdersInfoModel()->getPaymentInfo($orderId);
        if (empty($paymentInfo)) {
            return false;
        }
        $dateTime = new \DateTime();
        $dateTime->setTimestamp(round($paymentInfo['timestampOut']/1000));
        $paymentInfo['created'] = $dateTime->format('c');
        return $paymentInfo;
    }

    public function activateValidators()
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