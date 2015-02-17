<?php
use Form\CheckoutForm;
use Form\Component\Field\Select;
use Form\Data\FormData;
use Form\Validators\Interfaces\ValidatorInterface;
use Model\Model;
use Model\Orders;
use Model\PaymentOrderInfo;
use Payment\AbstractPaymentMethod;
use Payment\Interfaces\ReturnRedirectUrlInterface;

/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 12.02.15
 * Time: 19:08
 */

class App 
{
    private $view;
    private $data = [];
    private $config;
    private $db;
    private $paymentOrderInfoModel;

    public function __construct(ConfigInterface $configInterface, MysqlDb $db)
    {
        $this->config = $configInterface;
        $this->db = $db;
    }

    public function index()
    {
        $checkoutForm = new CheckoutForm($this->getCheckoutFormOptions());
        $checkoutForm->setPaymentMethods($this->getPaymentMethods());

        $this->data = [
            'checkoutForm' => $checkoutForm->make(),
        ];
    }

    public function success($paymentOrderId)
    {
        $view = 'error';
        $this->data['errors'] = 'Order is invalid';
        $paymentOrderInfo = $this->getPaymentOrdersInfoModel();
        $paymentCode = $paymentOrderInfo->getPaymentByPaymentOrderId($paymentOrderId);
        if ($paymentCode) {
            /** @var AbstractPaymentMethod $paymentMethod */
            $paymentMethod = $this->getPaymentMethod($paymentCode);
            if ($paymentMethod && $paymentMethod->checkSuccessOrder($paymentOrderId)) {
                $updateStatus = $paymentOrderInfo
                    ->updateStatusByPaymentOrderId($paymentOrderId, PaymentOrderInfo::STATUS_SUCCESS);
                if ($updateStatus == PaymentOrderInfo::STATUS_SUCCESS||1) {
                    $view = 'success';
                    $this->data['successMessage'] = 'Success payment: order_id ' . $paymentOrderId;
                    $this->saveOrder($paymentOrderId);
                }
            }
        }
        if ($view == 'error') {
            $this->index();
        }
        return $view;
    }

    public function error()
    {
        
    }

    public function checkout(array $data)
    {
        $checkoutForm = new CheckoutForm($this->getCheckoutFormOptions());
        $checkoutForm
            ->setPaymentMethods($this->getPaymentMethods())
            ->setValidators($this->getCheckoutFormValidators())
            ->setData(new FormData($data));
        if (($paymentMethod = $checkoutForm->selectPaymentMethod()) === false || !$checkoutForm->isValid()) {
            $this->data = [
                'errors' => $checkoutForm->getValidationErrors(),
            ];
            $view = 'error';
        } else {
            $formData = $checkoutForm->getData()->getData();
            $paymentData = $paymentMethod->extractPaymentInfo($formData);
            $paymentData['total'] = $this->calculateTotal($checkoutForm->getQuantity(), 999);
            $orderId = $this->makeOrder($data, $paymentData);
            if (!$orderId) {
                $view = 'error';
                $this->data = [
                    'errors' => ['Can not create order']
                ];
            } else {
                $result = $paymentMethod->process($orderId, array_merge($paymentData, $data));
                if (!$result) {
                    $this->data['errors'] = $paymentMethod->getTransactionErrors();
                    $view = 'error';
                } else {
                    $transactionInfo = $paymentMethod->getTransactionInfo();
                    if (!$this->setPaymentOrder($transactionInfo['order_id'], $transactionInfo['payment_order_id'])) { //todo move logic to paymentMethod
                        $this->data['errors'] = ['Can not update order'];
                        $view = 'error';
                    } else {
                        if ($paymentMethod instanceof ReturnRedirectUrlInterface) {
                            header('location:' . $paymentMethod->returnRedirectUrl());
                        } else {
                            header('location:http://' . PRODUCT_HOST . '/success?order_id=' . $transactionInfo['payment_order_id']);
                        }
                        $this->data = ['successMessage' => 'Good' . $paymentMethod->getCode()];
                        $view = 'success';
                    }
                }
            }
        }
        if ($view == 'error') {
            $this->data['checkoutForm'] = $checkoutForm->make();
        }
        return $view;
    }

    /**
     * @param $view
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    public function render($layout)
    {
        if (file_exists($layout)) {
            extract($this->data);
            include $layout;
        }
    }

    /**
     * @return array
     */
    private function getCheckoutFormOptions()
    {
        $configOptions = $this->config->get('checkout_form_options');
        return array_merge($configOptions, [
            ['name' => 'label', 'params' => ['labelFor' => CheckoutForm::COUNTRY, 'text' => 'Select countries']],
            ['name' => CheckoutForm::COUNTRY, 'params' => [Select::SELECT_OPTIONS => $this->getCountries(), Select::SELECT_SELECTED => 'NL']],
        ]);
    }

    private function getCountries()
    {
        $countriesModel = new Model($this->db, Model::COUNTRIES_TABLE);
        return $countriesModel->tableSelect(['value' => 'iso1_code', 'name']);
    }

    /**
     * @return AbstractPaymentMethod[]
     */
    private function getPaymentMethods()
    {
        $paymentMethods = [];
        foreach ($this->getArrayFromConfig('available_payment_methods') as $paymentMethod) {
            if ($paymentMethod instanceof AbstractPaymentMethod) {
                $paymentMethods[] = $paymentMethod;
            }
        }
        return $paymentMethods;
    }

    /**
     * @param $code
     * @return AbstractPaymentMethod|null
     */
    private function getPaymentMethod($code)
    {
        $paymentMethod = null;
        $paymentsFromConfigs = $this->config->get('available_payment_methods');
        if (
            isset($paymentsFromConfigs[$code]) &&
            is_callable($paymentsFromConfigs[$code])
        ) {
            $paymentMethod = $paymentsFromConfigs[$code]($this->config);
        }
        return $paymentMethod instanceof AbstractPaymentMethod ? $paymentMethod : null;
    }

    private function makeOrder(array $orderData, array $paymentData)
    {
        /** @var Orders $ordersModel*/
        $ordersModel = new Orders($this->db, Model::ORDERS_TABLE);
        return $ordersModel->makeOrder($orderData, $paymentData);
    }

    private function setPaymentOrder($orderId, $paymentOrderId)
    {
        $paymentOrderInfo = $this->getPaymentOrdersInfoModel();
        return $paymentOrderInfo->setPaymentOrder($orderId, $paymentOrderId);
    }

    private function getCheckoutFormValidators()
    {
        $validators = [];
        foreach ($this->getArrayFromConfig('checkout_form_validators') as $validator) {
            if ($validator instanceof ValidatorInterface) {
                $validators[] = $validator;
            }
        }
        return $validators;
    }

    /**
     * @return array
     */
    private function getArrayFromConfig($configKey)
    {
        $arrayFromConfig = [];
        $configArray = $this->config->get($configKey);
        if (is_array($configArray)) {
            foreach ($configArray as $config) {
                $arrayFromConfig[] = is_callable($config) ? $config($this->config) : $config;
            }

        }
        return $arrayFromConfig;
    }
     private function calculateTotal($quantity, $price)
     {
         return $quantity * $price;
     }

    /**
     * @return PaymentOrderInfo
     */
    private function getPaymentOrdersInfoModel()
    {
        if (!$this->paymentOrderInfoModel) {
            $this->paymentOrderInfoModel = new PaymentOrderInfo($this->db);
        }
        return $this->paymentOrderInfoModel;
    }

    private function saveOrder($paymentOrderId)
    {
        $info = $this->getPaymentOrdersInfoModel()->getInfo($paymentOrderId);
    }
}