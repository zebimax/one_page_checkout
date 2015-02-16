<?php

namespace Form;


use Form\Component\AbstractFormComponent;
use Form\Component\Field\AbstractField;
use Form\Component\Field\Input;
use Form\Component\Field\Select;
use Form\Component\TextComponent;
use Form\Component\Text\Label;
use Form\Data\AbstractFormData;
use Form\Validators\Interfaces\ValidatorInterface;
use Payment\PaymentMethodInterface;

class CheckoutForm extends AbstractFrom
    implements ValidatorsConfigurableInterface
{

    const FIRST_NAME = 'first_name';
    const LAST_NAME  = 'last_name';
    const PHONE = 'phone';
    const EMAIL = 'email';
    const PROFESSION = 'profession';
    const COUNTRY = 'country';
    const POST_CODE = 'post_code';
    const STREET = 'street'; //straatnaam
    const LOCATION = 'location'; //plaats
    const HOUSE_NUMBER = 'house_number'; //huisnummer
    const SUBMIT = 'checkout';
    const BR_TAG = 'br_tag';
    const LABEL = 'label';
    const PAYMENT_METHOD = 'payment_method';
    const QUANTITY = 'quantity';

    protected $name = 'checkout_form';
    protected $validators = [];

    private $custom = [];
    protected $fields = array(
        self::FIRST_NAME,
        self::LAST_NAME,
        self::PHONE,
        self::EMAIL,
        self::PROFESSION,
        self::COUNTRY,
        self::POST_CODE,
        self::STREET,
        self::LOCATION,
        self::HOUSE_NUMBER,
        self::SUBMIT,
        self::BR_TAG,
        self::LABEL,
        self::PAYMENT_METHOD,
        self::QUANTITY
    );
    /**
     * @var PaymentMethodInterface[]
     */
    protected $paymentMethods = [];

    public function __construct(array $formOptions = array(), $action = '', $method = 'post')
    {
        parent::__construct($formOptions, $action, $method);
    }

    public function addCustomField($fieldName, callable $fieldMaker)
    {
        if (!in_array($fieldName, $this->fields)) {
            $this->fields[] = $fieldName;
            $this->custom[$fieldName] = $fieldMaker;
            $this->formComponents[] = $this->createComponent($fieldName);
        }
    }

    public function setPaymentMethods(array $paymentMethods)
    {
        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod instanceof PaymentMethodInterface) {
                $this->paymentMethods[$paymentMethod->getCode()] = $paymentMethod;
            }
        }
        return $this;
    }

    public function selectPaymentMethod()
    {
        foreach ($this->paymentMethods as $paymentMethod) {
            if ($paymentMethod->isCanProcess($this)) {
                return $paymentMethod;
            }
        }
        $this->validationErrors[] = 'Can\'t select payment method';
        return false;
    }

    public function make()
    {
        foreach ($this->paymentMethods as $paymentMethod) {
            $paymentMethod->addOwnFieldsToCheckoutForm($this);
        }
        return parent::make();
    }

    /**
     * @return ValidatorInterface[]
     */
    public function getValidators()
    {
        return $this->validators;
    }

    public function setData(AbstractFormData $formData)
    {
        parent::setData($formData);
        foreach ($this->validators as $validator) {
            $this->data->addValidator($validator);
        }
        foreach ($this->paymentMethods as $paymentMethod) {
            if ($paymentMethod instanceof ValidatorsConfigurableInterface) {
                foreach ($paymentMethod->getValidators() as $paymentMethodValidator) {
                    $this->data->addValidator($paymentMethodValidator);
                }

            }
        }
        return $this;
    }

    /**
     * @param ValidatorInterface[] $validators
     * @return $this
     */
    public function setValidators(array $validators)
    {
        $this->validators = $validators;
        return $this;
    }

    protected function createComponent($fieldName, array $params = array())
    {
        switch ($fieldName) {
            case self::FIRST_NAME :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::FIRST_NAME,
                    AbstractField::FIELD_ID => self::FIRST_NAME,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::LAST_NAME :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::LAST_NAME,
                    AbstractField::FIELD_ID => self::LAST_NAME,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::PHONE :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::PHONE,
                    AbstractField::FIELD_ID => self::PHONE,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::EMAIL :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::EMAIL,
                    AbstractField::FIELD_ID => self::EMAIL,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::PROFESSION :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::PROFESSION,
                    AbstractField::FIELD_ID => self::PROFESSION,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::POST_CODE :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::POST_CODE,
                    AbstractField::FIELD_ID => self::POST_CODE,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::STREET :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::STREET,
                    AbstractField::FIELD_ID => self::STREET,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::LOCATION :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::LOCATION,
                    AbstractField::FIELD_ID => self::LOCATION,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::HOUSE_NUMBER :
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::HOUSE_NUMBER,
                    AbstractField::FIELD_ID => self::HOUSE_NUMBER,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::QUANTITY:
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : '';
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::QUANTITY,
                    AbstractField::FIELD_ID => self::QUANTITY,
                    AbstractField::FIELD_TYPE => Input::TEXT_TYPE,
                    AbstractField::FIELD_CLASS => '',
                    AbstractField::FIELD_VALUE => htmlspecialchars($value),
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::COUNTRY:
                $params = $this->checkSelectOptions($params);
                $fieldParams = array_merge($params, array(
                    AbstractField::COMPONENT_NAME => self::COUNTRY,
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Select($fieldParams);
                break;
            case self::PAYMENT_METHOD:
                $params = $this->checkSelectOptions($params);
                $fieldParams = array_merge($params, array(
                    AbstractField::COMPONENT_NAME => self::PAYMENT_METHOD,
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Select($fieldParams);
                break;
            case self::SUBMIT:
                $value = isset($params[AbstractField::FIELD_VALUE])
                    ? $params[AbstractField::FIELD_VALUE]
                    : self::SUBMIT;
                $fieldParams = array_merge($params, array(
                    AbstractFormComponent::COMPONENT_NAME => self::SUBMIT,
                    AbstractField::FIELD_TYPE => Input::SUBMIT_TYPE,
                    AbstractField::FIELD_VALUE => $value,
                    AbstractField::FIELD_ATTRIBUTES => array(),
                ));
                return new Input($fieldParams);
                break;
            case self::BR_TAG:
                return new TextComponent(self::BR_TAG, '<br />');
                break;
            case self::LABEL:
                $text = isset($params['text']) ? $params['text'] : '';
                $labelFor = isset($params['labelFor']) ? $params['labelFor'] : '';
                return new Label($labelFor, $text);
                break;
            default:
                if (isset($this->custom[$fieldName])) {
                    return new TextComponent('custom-' . $fieldName ,$this->custom[$fieldName]());
                }
                break;
        }
    }

    /**
     * @param array $options
     * @return string
     */
    protected function makeComponents(array $options = array())
    {
        return parent::makeComponents(array('initial' => '<div class="clear"></div>'));
    }

    /**
     * @param array $params
     * @return array
     * @throws \Exception
     */
    protected function checkSelectOptions(array $params)
    {
        if (!isset($params[Select::SELECT_OPTIONS]) || !is_array($params[Select::SELECT_OPTIONS])) {
            throw new \Exception('Options not specified');
        }
        return $params;
    }
}