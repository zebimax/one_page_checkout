<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 13.02.15
 * Time: 14:44
 */

namespace Payment;


use Form\CheckoutForm;
use Form\Component\Field\AbstractField;
use Form\Component\Field\Select;
use Form\Component\Text\Label;
use Form\Component\TextComponent;
use Form\Validators\CallableValidationValidator;
use Form\ValidatorsConfigurableInterface;
use Payment\Api\GingerApi;
use Payment\Interfaces\ActivatableValidatorsInterface;
use Payment\Interfaces\ReturnRedirectUrlInterface;
use Payment\Traits\ReturnRedirectUrlTrait;
use Payment\Traits\ValidatorsConfigurableTrait;

class IdealPayment extends AbstractPaymentMethod
    implements ValidatorsConfigurableInterface,
    ActivatableValidatorsInterface,
    ReturnRedirectUrlInterface
{
    use ValidatorsConfigurableTrait;
    use ReturnRedirectUrlTrait;
    const IDEAL = 'ideal';
    protected $api;
    private $sharedComponents = [];
    private $gingerIssuers = [];

    /**
     * @param GingerApi $api
     */
    public function __construct(GingerApi $api)
    {
        $this->api = $api;
        $this->code = self::IDEAL;
    }

    /**
     * @param $paymentOrderId
     * @return bool
     */
    public function checkSuccessOrder($paymentOrderId)
    {
        $orderStatus = $this->api->getOrderStatus($paymentOrderId);
        return $orderStatus == GingerApi::ORDER_STATUS_COMPLETED;
    }

    /**
     * @param $orderId
     * @param array $data
     * @return bool
     */
    public function process($orderId, array $data)
    {
        $gingerCreateIdealOrder = $this->api->gingerCreateIdealOrder(
            $orderId,
            $data['total'],
            $data['issuer_id'],
            $this->getReturnUrl(),
            $this->getPaymentDescription($data)
        );
        if (!is_array($gingerCreateIdealOrder) || array_key_exists('error', $gingerCreateIdealOrder)) {
            $this->transactionErrors[] = 'Selected payment method is not available now.';
            return false;
        }
        else {
            $this->transactionInfo = ['order_id' => $orderId, 'payment_order_id' => $gingerCreateIdealOrder['id']];
            $this->redirectUrl = $gingerCreateIdealOrder['transactions'][0]['payment_url'];
        }
        return true;
    }

    /**
     * @param $orderId
     * @return array|mixed|string
     */
    public function getOrderInfo($orderId)
    {
        return $this->api->getOrderDetails($orderId);
    }

    /**
     * @param array $data
     * @return array
     */
    public function extractPaymentInfo(array $data)
    {
        if(isset($data['ideal']['banks'])) {
            return [
                'issuer_id' => $data['ideal']['banks']
            ];
        }
        return [];
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     *
     */
    function activateValidators()
    {
        $this->validators = [
            new CallableValidationValidator(
                'ideal',
                'not valid ideal bank',
                function ($value) {
                    return (isset($value['banks'])) && isset($this->getGingerIssuers()[$value['banks']]);
                }
            )
        ];
    }

    /**
     * @param CheckoutForm $checkoutForm
     */
    public function addOwnFieldsToCheckoutForm(CheckoutForm $checkoutForm)
    {
        $checkoutForm->addCustomField('ideal', function() {
            //$idealBlock = %s</div>';
            return
                $this->getSharedTextComponent('text', '', '<div class="ideal_fields">')->make() .
                $this->getSharedTextComponent('text', '', '<span>Ideal</span><br>')->make() .
                $this->getSharedTextComponent('label', 'ideal[banks]','banks')->make() .
                $this->getSharedTextComponent('text', '', '<br>')->make() .
                (new Select([
                    Select::SELECT_OPTIONS => array_reduce($this->getGingerIssuers(), function($carry, $item) {
                        $carry[] = ['value' => $item['id'], 'name' => $item['id']];
                        return $carry;
                    }, [['value' => 0, 'name' => '']]),
                    AbstractField::COMPONENT_NAME => 'ideal[banks]'
                ]))->make() .
                $this->getSharedTextComponent('text', '', '</div>')->make();
        });
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
     * @return array
     */
    private function getGingerIssuers()
    {
        if (empty($this->gingerIssuers)) {
            $gingerGetIssuers = $this->api->gingerGetIssuers();
            $this->gingerIssuers = array_reduce((array)$gingerGetIssuers, function($carry, $item) {
                if (isset($item['id'])) {
                    $carry[$item['id']] = $item;
                }
                return $carry;
            }, []);
        }
        return $this->gingerIssuers;
    }


}