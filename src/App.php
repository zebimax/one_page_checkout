<?php
use Application\Interfaces\PaymentOrdersInfoModelAwareInterface;
use Application\Tools\Interfaces\SaverInterface;
use Application\Tools\XMLSaver;
use Form\CheckoutForm;
use Form\Component\Field\Select;
use Form\Data\FormData;
use Form\Validators\Interfaces\ValidatorInterface;
use Model\Model;
use Model\Orders;
use Model\PaymentOrderInfo;
use Payment\AbstractPaymentMethod;
use Payment\AfterPayPayment;
use Payment\IdealPayment;
use Payment\Interfaces\CurrentPaymentTransactionInfoInterface;
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
    private $saver;
    private $orders;

    /**
     * @param ConfigInterface $configInterface
     * @param MysqlDb $db
     */
    public function __construct(ConfigInterface $configInterface, MysqlDb $db)
    {
        $this->config = $configInterface;
        $this->db = $db;
    }

    public function index()
    {
        $checkoutForm = $this->createCheckoutForm();
        $this->data['checkoutForm'] = $checkoutForm->make();
    }

    public function test()
    {
        $this->getPaymentMethod('afterpay')->process(123, []);
    }

    /**
     * @param $paymentOrderId
     * @return string
     */
    public function success($paymentOrderId)
    {
        $paymentOrderInfo = $this->getPaymentOrdersInfoModel();
        /** @var AbstractPaymentMethod $paymentMethod */
        $paymentMethod = $this->getPaymentMethod(
            $paymentOrderInfo->getPaymentByPaymentOrderId($paymentOrderId)
        );
        if ($paymentMethod instanceof PaymentOrdersInfoModelAwareInterface) {
            $paymentMethod->setPaymentOrdersInfoModel($paymentOrderInfo);
        }
        if (!$this->checkSuccessOrder($paymentMethod, $paymentOrderId)) {
            $this->data['errors'][] = 'Order is invalid';
            $this->index();
            return false;
        }
        return true;
    }

    /**
     * @param array $data
     * @return string
     */
    public function checkout(array $data)
    {
        $result = false;
        $checkoutForm = $this->createCheckoutForm()
            ->setValidators($this->getCheckoutFormValidators())
            ->setData(new FormData($data));

        if (($paymentMethod = $checkoutForm->selectPaymentMethod()) === false || !$checkoutForm->isValid()) {
            $this->data['errors'] = $checkoutForm->getValidationErrors();
        } else {
            $formData = $checkoutForm->getData()->getData();
            $paymentData = $paymentMethod->extractPaymentInfo($formData);
            $paymentData['total'] = $this->calculateTotal($checkoutForm->getQuantity());
            $orderId = $this->makeOrder($data, $paymentData);

            if (
            $paymentMethod->process(
                $orderId,
                array_merge($data, $paymentData, $this->getAdditionalData())
            )
            ) {
                $transactionInfo = $paymentMethod->getTransactionInfo();
                $currentTransactionInfo = $paymentMethod instanceof CurrentPaymentTransactionInfoInterface
                    ? $paymentMethod->getCurrentPaymentTransactionInfo()
                    : [];

                if (
                !$this->setPaymentOrder(
                    $transactionInfo['order_id'],
                    $transactionInfo['payment_order_id'],
                    $currentTransactionInfo
                )
                ) {
                    $this->data['errors'] = ['Can not update order'];
                } else {
                    if ($paymentMethod instanceof ReturnRedirectUrlInterface) {
                        header('location:' . $paymentMethod->returnRedirectUrl());
                    } else {
                        $this->setSaver(new XMLSaver(APP_DIR . 'xml' . DIRECTORY_SEPARATOR));
                        return $this->success($transactionInfo['payment_order_id']);
                    }
                }
            } else {
                $this->data['errors'] = $paymentMethod->getTransactionErrors();
            }
        }
        if (!$result) {
            $this->data['checkoutForm'] = $checkoutForm->make();
        }
        return $result;
    }

    /**
     * @param SaverInterface $saver
     * @return $this
     */
    public function setSaver(SaverInterface $saver)
    {
        $this->saver = $saver;
        return $this;
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->data['title'] = $title;
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

    /**
     * @param $layout
     */
    public function render($layout)
    {
        if (file_exists($layout)) {
            extract($this->data);
            include $layout;
        }
    }

    /**
     * @return SaverInterface
     * @throws Exception
     */
    private function getSaver()
    {
        if (!$this->saver || !$this->saver instanceof SaverInterface) {
            throw new Exception('Saver must implements SaverInterface');
        }
        return $this->saver;
    }

    /**
     * @return array
     */
    private function getCheckoutFormOptions()
    {
        $configOptions = $this->config->get('checkout_form_options');
        $this->fillFormValues($configOptions);
        return array_merge($configOptions, [
            ['name' => 'label', 'params' => ['labelFor' => CheckoutForm::COUNTRY, 'text' => 'Select countries']],
            ['name' => CheckoutForm::COUNTRY, 'params' => [Select::SELECT_OPTIONS => $this->getCountries(), Select::SELECT_SELECTED => 'NL']],
        ]);
    }

    /**
     * @param array $formOptions
     */
    private function fillFormValues(array &$formOptions)
    {
        foreach ($formOptions as &$option) {
            if (isset($_POST[$option['name']])) {
                $option['params']['value'] = $this->sanitizeStr($_POST[$option['name']]);
            }
        }

    }

    /**
     * @param $str
     * @return string
     */
    private function sanitizeStr($str)
    {
        return trim(htmlspecialchars($str));
    }

    /**
     * @return array|bool
     * @throws Exception
     */
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

    /**
     * @param array $orderData
     * @param array $paymentData
     * @return bool|mixed
     */
    private function makeOrder(array $orderData, array $paymentData)
    {
        return $this->getOrders()->makeOrder($orderData, $paymentData);
    }

    /**
     * @param $orderId
     * @param $paymentOrderId
     * @param array $paymentData
     * @return bool
     */
    private function setPaymentOrder($orderId, $paymentOrderId, array $paymentData = [])
    {
        $paymentOrderInfo = $this->getPaymentOrdersInfoModel();
        $result = $paymentOrderInfo->setPaymentOrder($orderId, $paymentOrderId);
        if (!empty($paymentData)) {
            $paymentOrderInfo->updatePaymentInfo($paymentOrderId, $paymentData);
        }
        return $result;
    }

    /**
     * @return array
     */
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
     * @param $configKey
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

    /**
     * @param $quantity
     * @return mixed
     */
    private function calculateTotal($quantity)
     {
         return $quantity * $this->getPrice();
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

    /**
     * @param AbstractPaymentMethod $paymentMethod
     * @param $paymentOrderId
     * @return bool
     * @throws Exception
     */
    private function saveOrder(AbstractPaymentMethod $paymentMethod, $paymentOrderId)
    {
        $info = $this->getPaymentOrdersInfoModel()->getInfo($paymentOrderId);
        $orderInfo = $paymentMethod->getOrderInfo($paymentOrderId);
        if (!is_array($orderInfo)) {
            throw new \Exception('Not valid order data!');
        }
        $this->getSaver()->save($this->prepareOrderForXml(
                $info,
                json_decode($info['payment_data'], JSON_OBJECT_AS_ARRAY),
                $orderInfo
            )
        );
    }

    /**
     * @param array $data
     * @param array $paymentData
     * @param array $orderInfo
     * @return array
     */
    private function prepareOrderForXml(array $data, array $paymentData, array $orderInfo)
    {
        $address = [
            'elements' => [
                'company' => ['text' => $data['company']],
                'name_first' => ['text' => $data['first_name']],
                'name_last' => ['text' => $data['last_name']],
                'address1' => ['text' => "{$data['street']} {$data['house_number']}"],
                'address2' => ['text' => ''],
                'city' => ['text' => $data['city']],
                'zip' => ['text' => trim(preg_replace('/\D/', '', $data['post_code']))],
                'state' => ['text' => trim(preg_replace('/\d/', '', $data['post_code']))],
                'country' => ['text' => $data['country_code']],
                'phone' => ['text' => str_replace(['+', '-', '(', ')'], '', $data['phone'])]
            ]
        ];
        $created = new DateTime($orderInfo['created']);
        return [
            'id' => 'order_' . $data['order_id'], 'version' => '1.0',
            'data' => [
                'ordercollection' => [
                    'attributes' => ['xmlns:php' => 'http://php.net/xsl'],
                    'elements' => [
                        'order' => [
                            'elements' => [
                                'id' => [
                                    'elements' => [
                                        'primary_id' => ['text' => $data['order_id']],
                                        'created_at' => ['text' => $created->format('Y-m-d H:i:s')]
                                    ]
                                ],
                                'ginger_order_id' => ['text' => $data['payment_code'] == IdealPayment::IDEAL ? $orderInfo['id'] : ''],
                                'after_pay_order_reference' => ['text' => $data['payment_code'] == AfterPayPayment::AFTER_PAY ? $orderInfo['afterPayOrderReference'] : ''],
                                'customer_email' => ['text' => $data['email']],
                                'payment' => [
                                    'elements' => [
                                        'method' => ['text' => $data['payment_code']],
                                        'method_title' => ['text' => $data['payment_name']],
                                    ]
                                ],
                                'billing' => [
                                    'elements' => [
                                        'total' => ['text' => $paymentData['total']],
                                        'discount_amount' => ['text' => '0.0000'],
                                        'shipping_amount' => ['text' => '0.0000'],
                                        'payment_fee' => ['text' => '0'],
                                        'address' => $address
                                    ]
                                ],
                                'time' => [
                                    'elements' => [
                                        'order_time' => ['text' => time()]
                                    ]
                                ],
                                'ship_data' => [
                                    'elements' => [
                                        'address' => $address,
                                        'box' => [
                                            'elements' => [
                                                'item' => [
                                                    'elements' => [
                                                        'products_name' => ['text' => PRODUCT_NAME],
                                                        'quantity' => ['text' => $data['quantity']],
                                                        'ean_code' => ['text' => PRODUCT_EAN],
                                                        'price' => ['text' => $this->getPrice()],
                                                        'price_incl_tax' => ['text' => $this->getPriceInclTax()],
                                                        'tax_percent' => ['text' => $this->getTaxPercent()],
                                                        'row_total' => ['text' => $this->getRawTotal()],
                                                        'row_total_incl_tax' => ['text' => $this->getRawTotalInclTax()]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return mixed
     */
    private function getPrice()
    {
        return $this->config->get('product_price');
    }

    /**
     * @return mixed
     */
    private function getPriceInclTax()
    {
        return $this->getPrice() * (1 + $this->getTaxPercent() / 100);
    }

    /**
     * @return mixed
     */
    private function getTaxPercent()
    {
        return $this->config->get('product_tax_percent');
    }

    /**
     * @return mixed
     */
    private function getRawTotal()
    {
        return $this->config->get('product_raw_total');
    }

    /**
     * @return mixed
     */
    private function getRawTotalInclTax()
    {
        return $this->getRawTotal() * (1 + $this->getTaxPercent() / 100);
    }

    /**
     * @return CheckoutForm
     */
    private function createCheckoutForm()
    {
        $checkoutForm = new CheckoutForm($this->getCheckoutFormOptions());
        $checkoutForm->setAction('/')->setPaymentMethods($this->getPaymentMethods());
        return $checkoutForm;
    }

    /**
     * @return Orders
     */
    public function getOrders()
    {
        if (!$this->orders) {
            $this->orders = new Orders($this->db, Model::ORDERS_TABLE);
        }
        return $this->orders;
    }

    /**
     * @return array
     */
    protected function getAdditionalData()
    {
        $paymentData = [
            'product_tax_category' => $this->config->get('product_tax_category'),
            'product_price_in_cents' => $this->config->get('product_price_in_cents'),
            'product_id' => PRODUCT_ID,
            'product_name' => PRODUCT_NAME
        ];
        return $paymentData;
    }

    /**
     * @param $paymentMethod
     * @param $paymentOrderId
     * @return bool
     * @throws Exception
     */
    private function checkSuccessOrder($paymentMethod, $paymentOrderId)
    {
        $paymentOrderInfo = $this->getPaymentOrdersInfoModel();

        if (
            !$paymentMethod instanceof AbstractPaymentMethod ||
            $paymentOrderInfo->getStatus($paymentOrderId) != PaymentOrderInfo::STATUS_ACCEPTED
        ) {
            return false;
        }

        if ($paymentMethod->checkSuccessOrder($paymentOrderId)) {
            $updateStatus = $paymentOrderInfo->updateStatusByPaymentOrderId(
                $paymentOrderId,
                PaymentOrderInfo::STATUS_SUCCESS
            );
            if ($updateStatus == PaymentOrderInfo::STATUS_SUCCESS) {
                $this->data['successMessage'] = 'Success payment: order_id ' . $paymentOrderId;
                $this->saveOrder($paymentMethod, $paymentOrderId);
                $this->setTitle('Success page');
                return true;
            }
        }
        return false;
    }
}