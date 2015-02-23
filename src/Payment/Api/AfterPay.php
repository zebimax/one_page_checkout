<?php
namespace Payment\Api;

class AfterPay
{
    /** @var \SoapClient */
    private $soapClient;
    private $authorization;
    private $mode;
    private $order;
    private $orderLines = [];
    private $orderType;
    private $orderTypeName;
    private $orderTypeFunction;
    private $orderResult;
    private $totalOrderAmount = 0;
    private $wsdl;
    private $country = 'NL';
    private $orderManagement = false;
    private $orderManagementAction = null;

    /**
     * @param $merchantId
     * @param $portfolio
     * @param $password
     */
    public function __construct($merchantId, $portfolio, $password)
    {
        $this->order = new \stdClass();
        $this->order->shopper = new \stdClass();
        $this->authorization = new \StdClass();
        $this->authorization->merchantId = $merchantId;
        $this->authorization->portfolioId = $portfolio;
        $this->authorization->password = $password;
    }

    /**
     * @param $action
     */
    public function setOrderManagement($action)
    {
        $this->orderManagement = true;
        $this->orderManagementAction = $action;
    }

    /**
     * @param $order
     * @param $orderType
     */
    public function setOrder($order, $orderType)
    {
        $this->setOrderType($orderType);
        if ($this->orderType == 'OM') {
            switch ($this->orderManagementAction) {
                case 'capture_full':
                    $this->order->invoicenumber = $order['invoicenumber'];
                    $this->order->transactionkey = new \stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    $this->order->capturedelaydays = 0;
                    $this->order->shippingCompany = '';
                    break;
                case 'capture_partial':
                    $this->order->invoicelines = $this->orderLines;
                    $this->order->invoicenumber = $order['invoicenumber'];
                    $this->order->transactionkey = new \stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    $this->order->capturedelaydays = 0;
                    $this->order->shippingCompany = '';
                    break;
                case 'cancel':
                    $this->order->transactionkey = new \stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    break;
                case 'refund_full':
                    $this->order->invoicenumber = $order['invoicenumber'];
                    $this->order->transactionkey = new \stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    $this->order->creditInvoicenNumber = $order['creditinvoicenumber'];
                    break;
                case 'refund_partial':
                    $this->order->invoicelines = $this->orderLines;
                    $this->order->invoicenumber = $order['invoicenumber'];
                    $this->order->transactionkey = new \stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    $this->order->creditInvoicenNumber = $order['creditinvoicenumber'];
                    break;
                case 'void':
                    $this->order->transactionkey = new \stdClass();
                    $this->order->transactionkey->ordernumber = $order['ordernumber'];
                    break;
                default:
                    break;
            }
            return;
        }
        $billToAddress = $shipToAddress = null;
        if ($this->orderType == 'B2C') {
            $billToAddress = 'b2cbilltoAddress';
            $shipToAddress = 'b2cshiptoAddress';
        } elseif ($this->orderType == 'B2B') {
            $billToAddress = 'b2bbilltoAddress';
            $shipToAddress = 'b2bshiptoAddress';
        }

        if ($order['billtoaddress']['isocountrycode'] == 'BE') {
            $this->country = 'BE';
        } elseif ($order['billtoaddress']['isocountrycode'] == 'DE') {
            $this->country = 'DE';
        } else {
            $this->country = 'NL';
        }
        if ($billToAddress && $shipToAddress) {
            $this->order->$billToAddress = new \stdClass();
            $this->order->$shipToAddress = new \stdClass();

            if ($this->orderType == 'B2C') {
                $this->order->$billToAddress->referencePerson = new \stdClass();
                $this->order->$shipToAddress->referencePerson = new \stdClass();
            }

            $this->order->$billToAddress->city = $order['billtoaddress']['city'];
            $this->order->$billToAddress->housenumber = $order['billtoaddress']['housenumber'];
            $this->order->$billToAddress->isoCountryCode = $order['billtoaddress']['isocountrycode'];
            $this->order->$billToAddress->postalcode = $order['billtoaddress']['postalcode'];
            $this->order->$billToAddress->streetname = $order['billtoaddress']['streetname'];

            $this->order->$shipToAddress->city = $order['shiptoaddress']['city'];
            $this->order->$shipToAddress->housenumber = $order['shiptoaddress']['housenumber'];
            $this->order->$shipToAddress->isoCountryCode = $order['shiptoaddress']['isocountrycode'];
            $this->order->$shipToAddress->postalcode = $order['shiptoaddress']['postalcode'];
            $this->order->$shipToAddress->streetname = $order['shiptoaddress']['streetname'];

            if ($this->orderType == 'B2C') {
                $this->order->$billToAddress->referencePerson->dateofbirth = $order['billtoaddress']['referenceperson']['dob'];
                $this->order->$billToAddress->referencePerson->emailaddress = $order['billtoaddress']['referenceperson']['email'];
                $this->order->$billToAddress->referencePerson->gender = $order['billtoaddress']['referenceperson']['gender'];
                $this->order->$billToAddress->referencePerson->initials = $order['billtoaddress']['referenceperson']['initials'];
                $this->order->$billToAddress->referencePerson->isoLanguage = $order['billtoaddress']['referenceperson']['isolanguage'];
                $this->order->$billToAddress->referencePerson->lastname = $order['billtoaddress']['referenceperson']['lastname'];
                $this->order->$billToAddress->referencePerson->phonenumber1 = $this->cleanPhone(
                    $order['billtoaddress']['referenceperson']['phonenumber'],
                    $order['billtoaddress']['isocountrycode']
                );

                $this->order->$shipToAddress->referencePerson->dateofbirth = $order['shiptoaddress']['referenceperson']['dob'];
                $this->order->$shipToAddress->referencePerson->emailaddress = $order['shiptoaddress']['referenceperson']['email'];
                $this->order->$shipToAddress->referencePerson->gender = $order['shiptoaddress']['referenceperson']['gender'];
                $this->order->$shipToAddress->referencePerson->initials = $order['shiptoaddress']['referenceperson']['initials'];
                $this->order->$shipToAddress->referencePerson->isoLanguage = $order['shiptoaddress']['referenceperson']['isolanguage'];
                $this->order->$shipToAddress->referencePerson->lastname = $order['shiptoaddress']['referenceperson']['lastname'];
                $this->order->$shipToAddress->referencePerson->phonenumber1 = $this->cleanPhone(
                    $order['shiptoaddress']['referenceperson']['phonenumber'],
                    $order['billtoaddress']['isocountrycode']
                );
            }

            if ($this->orderType == 'B2B') {
                $this->order->company->cocnumber = $order['company']['cocnumber'];
                $this->order->company->companyname = $order['company']['companyname'];
                $this->order->company->vatnumber = $order['company']['vatnumber'];

                $this->order->person->dateofbirth = $order['billtoaddress']['referenceperson']['dob'];
                $this->order->person->emailaddress = $order['billtoaddress']['referenceperson']['email'];
                $this->order->person->initials = $order['billtoaddress']['referenceperson']['initials'];
                $this->order->person->isoLanguage = $order['billtoaddress']['referenceperson']['isolanguage'];
                $this->order->person->lastname = $order['billtoaddress']['referenceperson']['lastname'];
                $this->order->person->phonenumber1 = $this->cleanPhone(
                    $order['billtoaddress']['referenceperson']['phonenumber'],
                    $order['billtoaddress']['isocountrycode']
                );
            }
        }
        $this->order->ordernumber = $order['ordernumber'];
        $this->order->bankaccountNumber = $order['bankaccountnumber'];
        $this->order->currency = $order['currency'];
        $this->order->ipAddress = $order['ipaddress'];
        $this->order->shopper->profilecreated = '2013-01-01T00:00:00';
        $this->order->parentTransactionreference = false;
        $this->order->orderlines = $this->orderLines;
        $this->order->totalOrderAmount = $this->totalOrderAmount;
    }

    /**
     * @param $id
     * @param $description
     * @param $quantity
     * @param $unitPrice
     * @param $vatCategory
     */
    public function createOrderLine($id, $description, $quantity, $unitPrice, $vatCategory)
    {
        $orderLine = new \stdClass();
        $orderLine->articleId = $id;
        $orderLine->articleDescription = $description;
        $orderLine->quantity = $quantity;
        $orderLine->unitprice = $unitPrice;
        $orderLine->vatcategory = $vatCategory;
        $this->totalOrderAmount = $this->totalOrderAmount + ($quantity * $unitPrice);
        $this->orderLines[] = $orderLine;
    }

    /**
     * @param string $mode
     * @throws \Exception
     * Process request to SOAP webservice
     */
    public function doRequest($mode = 'test')
    {
        $this->setMode($mode);
        $this->setSoapClient();
        try {
            $this->orderResult = $this->soapClient->__soapCall(
                $this->orderTypeName,
                array(
                    $this->orderTypeName => array(
                        'authorization' => $this->authorization,
                        $this->orderTypeFunction => $this->order
                    )
                )
            );
        } catch (\Exception $e) {
            $this->orderResult = $e;
        }
    }

    /**
     * @return mixed
     */
    public function getOrderResult()
    {
        return $this->orderResult;
    }

    /**
     * @param $orderType
     * Set order types to correct webservice calls and function names
     * Set orderType, options are B2C, B2B, OM
     */    
    private function setOrderType($orderType)
    {

        if (!$this->orderManagement) {

            switch ($orderType) {
                case 'B2C':
                    $this->orderType = 'B2C';
                    $this->orderTypeName = 'validateAndCheckB2COrder';
                    $this->orderTypeFunction = 'b2corder';
                    break;
                case 'B2B':
                    $this->orderType = 'B2B';
                    $this->orderTypeName = 'validateAndCheckB2BOrder';
                    $this->orderTypeFunction = 'b2border';
                    break;
                default:
                    break;
            }
        } else {

            switch ($this->orderManagementAction) {
                case 'capture_full':
                    $this->orderType = 'OM';
                    $this->orderTypeName = 'captureFull';
                    $this->orderTypeFunction = 'captureobject';
                    break;
                case 'capture_partial':
                    $this->orderType = 'OM';
                    $this->orderTypeName = 'capturePartial';
                    $this->orderTypeFunction = 'captureobject';
                    break;
                case 'cancel':
                    $this->orderType = 'OM';
                    $this->orderTypeName = 'cancelOrder';
                    $this->orderTypeFunction = 'ordermanagementobject';
                    break;
                case 'refund_full':
                    $this->orderType = 'OM';
                    $this->orderTypeName = 'refundFullInvoice';
                    $this->orderTypeFunction = 'refundobject';
                    break;
                case 'refund_partial':
                    $this->orderType = 'OM';
                    $this->orderTypeName = 'refundInvoice';
                    $this->orderTypeFunction = 'refundobject';
                    break;
                case 'void':
                    $this->orderType = 'OM';
                    $this->orderTypeName = 'doVoid';
                    $this->orderTypeFunction = 'ordermanagementobject';
                    break;
            }
        }
    }

    /**
     * @param $mode
     * @throws \Exception
     */
    private function setMode($mode)
    {
        $this->mode = $mode;
        $this->wsdl = $this->getWsdl($this->country, $mode);
    }

    /**
     * @param $country
     * @param $mode
     * @return null|string
     * @throws \Exception
     */
    private function getWsdl($country, $mode)
    {
        $wsdl = null;
        if (!$this->orderManagement) {

            if ($country == 'NL') {
                if ($mode == 'test') {
                    $wsdl = 'https://test.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
                } elseif ($mode == 'live') {
                    $wsdl = 'https://www.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
                }
            } elseif ($country == 'BE') {
                if ($mode == 'test') {
                    $wsdl = 'https://test.afterpay.be/soapservices/rm/AfterPaycheck?wsdl';
                } elseif ($mode == 'live') {
                    $wsdl = 'https://api.afterpay.be/soapservices/rm/AfterPaycheck?wsdl';
                }
            }

        } else {

            if ($country == 'NL') {
                if ($mode == 'test') {
                    $wsdl = 'https://test.acceptgirodienst.nl/soapservices/om/OrderManagement?wsdl';
                } elseif ($mode == 'live') {
                    $wsdl = 'https://www.acceptgirodienst.nl/soapservices/rm/AfterPaycheck?wsdl';
                }
            } elseif ($country == 'BE') {
                if ($mode == 'test') {
                    $wsdl = 'https://test.afterpay.be/soapservices/om/OrderManagement?wsdl';
                } elseif ($mode == 'live') {
                    $wsdl = 'https://api.afterpay.be/soapservices/om/OrderManagement?wsdl';
                }
            }
        }
        if (!$wsdl) {
            throw new \Exception('Can\'t find correct WSDL endpoint');
        }
        return $wsdl;
    }

    /**
     * @throws \Exception
     */
    private function setSoapClient()
    {
        $options = null;
        if ($this->country == 'NL') {
            $options = [
                'trace' => 0,
                'cache_wsdl' => WSDL_CACHE_NONE
            ];
        } elseif ($this->country == 'BE') {
            $options = [
                'location' => $this->wsdl,
                'trace' => 0,
                'cache_wsdl' => WSDL_CACHE_NONE
            ];
        }
        if (!$options) {
            throw new \Exception('Invalid country');
        }
        $this->soapClient = new \SoapClient($this->wsdl, $options);
    }

    /**
     * @param $phoneNumber
     * @param string $country
     * @return mixed|string
     */
    private function cleanPhone($phoneNumber, $country = 'NL')
    {
        // Replace + with 00
        $phoneNumber = str_replace('+', '00', $phoneNumber);
        // Remove (0) because output is international format
        $phoneNumber = str_replace('(0)', '', $phoneNumber);
        $phoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);

        // Country specific checks
        if ($country == 'NL') {
            if (strlen($phoneNumber) == '10' && substr($phoneNumber, 0, 3) != '0031' && substr($phoneNumber, 0, 1) == '0') {
                $phoneNumber = '0031' . substr($phoneNumber, -9);
            } elseif (strlen($phoneNumber) == '13' && substr($phoneNumber, 0, 3) == '0031') {
                $phoneNumber = '0031' . substr($phoneNumber, -9);
            }
        } elseif ($country == 'BE') {
            // Land lines
            if (strlen($phoneNumber) == '9' && substr($phoneNumber, 0, 3) != '0032' && substr($phoneNumber, 0, 1) == '0') {
                $phoneNumber = '0032' . substr($phoneNumber, -8);
            } elseif (strlen($phoneNumber) == '12' && substr($phoneNumber, 0, 3) == '0032') {
                $phoneNumber = '0032' . substr($phoneNumber, -8);
            }
            // Mobile lines
            if (strlen($phoneNumber) == '10' && substr($phoneNumber, 0, 3) != '0032' && substr($phoneNumber, 0, 1) == '0') {
                $phoneNumber = '0032' . substr($phoneNumber, -9);
            } elseif (strlen($phoneNumber) == '13' && substr($phoneNumber, 0, 3) == '0032') {
                $phoneNumber = '0032' . substr($phoneNumber, -9);
            }
        }

        return $phoneNumber;
    }
}