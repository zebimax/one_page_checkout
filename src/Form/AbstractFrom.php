<?php

namespace Form;


use Form\Component\AbstractFormComponent;
use Form\Component\Field\AbstractField;
use Form\Data\AbstractFormData;

abstract class AbstractFrom
{
    protected $fields = [];
    /** @var AbstractFormComponent[] */
    protected $formComponents = [];
    /** @var AbstractFormData */
    protected $data;
    protected $action = '';
    protected $method = 'get';
    protected $name;
    protected $componentsGlue = '';
    protected $validationErrors = [];
    protected $formTemplate = '<form name="%s" action="%s" method="%s">%s</form>';

    /**
     * @param array $formOptions
     * @param string $action
     * @param string $method
     */
    public function __construct(array $formOptions = array(), $action = '', $method = 'post')
    {
        $this->setAction($action);
        $this->setMethod($method);
        foreach ($formOptions as $componentParams) {
            if (isset($componentParams['name'])) {
                $params = array();
                if (isset($componentParams['params']) && is_array($componentParams['params'])) {
                    $params = $componentParams['params'];
                }
                $this->setComponent($componentParams['name'], $params);
            }
        }

    }

    /**
     * @param $name
     * @param array $params
     */
    public function setComponent($name, array $params = array())
    {
        if (in_array($name, $this->fields)) {
            $this->formComponents[] = $this->createComponent($name, $params);
        }
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getFormComponents()
    {
        return $this->formComponents;
    }

    /**
     * @return string
     */
    public function make()
    {
        return sprintf(
            $this->formTemplate,
            $this->getName(),
            $this->getAction(),
            $this->getMethod(),
            $this->makeComponents()
        );
    }

    /**
     * @param AbstractFormData $formData
     * @return $this
     */
    public function setData(AbstractFormData $formData)
    {
        $this->data = $formData;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (!$this->data) {
            return false;
        }
        $this->data->process();
        return $this->data->isValid();
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        if (!$this->data) {
            return ['Form data not set'];
        }
        return array_merge($this->validationErrors, $this->data->getValidationErrors());
    }

    /**
     * @param $key
     * @return bool
     */
    public function getFormDataValue($key)
    {
        return $this->data->getRawValue($key);
    }

    /**
     * @return AbstractFormData
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $name
     * @param array $params
     * @return AbstractFormComponent
     */
    abstract protected function createComponent($name, array $params = array());

    /**
     * @param array $options
     * @return array
     */
    protected function makeComponents(array $options = array())
    {
       return array_reduce(
           $this->formComponents,
           function($carry, $item) {
               /** @var AbstractFormComponent $item */
               if (method_exists($item, 'make')) {
                   $carry .= $item->make();
               }
               return $carry;
           },
           ''
       );
    }
}