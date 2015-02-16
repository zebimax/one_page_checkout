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
use Payment\Traits\ValidatorsConfigurableTrait;

class IdealPayment extends AbstractPaymentMethod
    implements ValidatorsConfigurableInterface,
    ActivatableValidatorsInterface
{
    use ValidatorsConfigurableTrait;
    private $api;
    private $sharedComponents = [];
    private $gingerIssuers = [];

    public function __construct(GingerApi $api)
    {
        $this->api = $api;
        $this->code = 'ideal';
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
                'ideal[banks]',
                'not valid ideal bank',
                function ($value) {
                    return in_array($value, $this->getGingerIssuers());
                }
            )
        ];
    }

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
            $this->gingerIssuers = $this->api->gingerGetIssuers();
        }
        return $this->gingerIssuers;
    }
}