<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 13:06
 */

namespace Form\Data;


use Application\Interfaces\ErrorMessageInterface;
use Application\Interfaces\NamedInterface;
use Form\Validators\Interfaces\ValidatorInterface;

abstract class AbstractFormData
{
    protected $rawData = [];
    protected $validators = [];
    protected $valid = false;
    protected $validationErrors = [];
    protected $data = [];

    public function __construct(array $rawData = [])
    {
        $this->rawData = $rawData;
    }

    public function processValidation()
    {
        $fieldsValids = [];
        foreach ($this->rawData as $rawField => $rawValue) {
            foreach ($this->getFieldValidators($rawField) as $fieldValidator) {
                if (!$fieldValidator->validate($rawValue)) {
                    $fieldsValids[$rawField] = false;
                    $this->validationErrors[] = $fieldValidator instanceof ErrorMessageInterface
                        ? $fieldValidator->getError()
                        : 'error';
                    break(2);
                }
            }
            $fieldsValids[$rawField] = true;
        }
        $this->valid = (!in_array(false, $fieldsValids));
    }

    public function process()
    {
        $this->processValidation();
        $this->data = $this->rawData;
    }

    /**
     * @param array $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    public function addValidator(ValidatorInterface $validatorInterface, $name = null)
    {
        if ($validatorInterface instanceof NamedInterface) {
            $this->validators[$validatorInterface->getName()][] = $validatorInterface;
        } elseif ($name) {
            $this->validators[$name][] = $validatorInterface;
        } else {
            throw new \Exception ('Validator must be named!');
        }
    }

    public function getRawValue($key)
    {
        if (isset($this->rawData[$key])) {
            return $this->rawData[$key];
        }
        return false;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $field
     * @return ValidatorInterface[]
     */
    private function getFieldValidators($field)
    {
        if (isset($this->validators[$field])) {
            return $this->validators[$field];
        } else {
            return [];
        }
    }
}