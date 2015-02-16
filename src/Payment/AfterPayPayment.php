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
use Payment\Interfaces\ActivatableValidatorsInterface;
use Payment\Traits\ValidatorsConfigurableTrait;

class AfterPayPayment extends AbstractPaymentMethod
    implements ValidatorsConfigurableInterface,
    ActivatableValidatorsInterface
{
    use ValidatorsConfigurableTrait;
    private $sharedComponents = [];

    public function __construct()
    {
        $this->code = 'afterpay';
    }

    public function addOwnFieldsToCheckoutForm(CheckoutForm $checkoutForm)
    {
        $checkoutForm->addCustomField('afterpay', function() {
            return
                $this->getSharedTextComponent('text', '', '<div class="afterpay_fields">')->make() .
                $this->getSharedTextComponent('text', '', '<span>AfterPay</span><br>')->make() .
                $this->getSharedTextComponent('label', 'afterpay[day_of_birth]','Day of birth')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[day_of_birth]'])->make() .
                $this->getSharedTextComponent('label', 'afterpay[month_of_birth]','Month')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[month_of_birth]'])->make() .
                $this->getSharedTextComponent('label', 'afterpay[year_of_birth]','Year')->make() .
                $this->getSharedInputComponent(['name' => 'afterpay[year_of_birth]'])->make() .
                $this->getSharedTextComponent('text', '', '</div>')->make();
        });
    }

    public function process(CheckoutForm $checkoutForm)
    {

    }

    public function getCode()
    {
        return $this->code;
    }

    function activateValidators()
    {
        $this->validators = [
            new CallableValidationValidator(
                'afterpay[day_of_birth]',
                'not valid day_of_birth',
                function ($value) {
                    return $value > 0 && $value < 32;
                }
            ),
            new CallableValidationValidator(
                'afterpay[month_of_birth]',
                'not valid month_of_birth',
                function ($value) {
                    return $value > 0 && $value < 13;
                }
            ),
            new CallableValidationValidator(
                'afterpay[year_of_birth]',
                'not valid year_of_birth',
                function ($value) {
                    return $value > 1900 && $value < 2100;
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