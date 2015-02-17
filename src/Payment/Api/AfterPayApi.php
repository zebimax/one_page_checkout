<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 17.02.15
 * Time: 16:21
 */

namespace Payment\Api;


class AfterPayApi
{
    const DEFAULT_SOAP_VAL_NAME = 'orderregel';
    const DEFAULT_SOAP_VAL_TYPE = 'orderregel';

    private $url;
    public function __construct($url)
    {
        $this->url = $url;
//        $portfolio = $this->container->getParameter('afterPayPortfolioID');
//        //$pass         = $paymentConfigurations['MODULE_PAYMENT_AFTERPAY_WACHTWOORD_ACCEPTGIRO'];
//        $pass         = $this->container->getParameter('afterPayPassword');
    }

    /**
     * Instance New Soap Client.
     * @return \SoapClient|\SoapFault
     */
    public function instanceSoapClient()
    {
        try {
            return new \SoapClient($this->url);
        } catch (\SoapFault $soapFault) {
            return $soapFault;
        }

    }

    /**
     * @param $articleDetails
     * @param string $name
     * @param string $type
     * @param bool $elementNs
     * @param bool $typeNs
     * @return \SoapVar
     */
    public function instanceSoapValue(
        $articleDetails,
        $name = self::DEFAULT_SOAP_VAL_NAME,
        $type = self::DEFAULT_SOAP_VAL_TYPE,
        $elementNs = false,
        $typeNs = false
    ) {
        return new \SoapVar($articleDetails, SOAP_ENC_OBJECT, $type, $typeNs, $name, $elementNs);
    }
}