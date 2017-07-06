<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Model_Web_Service_Nvp_Client extends Indies_Recurringandrentalpayments_Model_Web_Service_Client
{

    const VERSION = "62.0";
    const NVP_URI = "https://api-3t.paypal.com/nvp";
    const NVP_SANDBOX_URI = "https://api-3t.sandbox.paypal.com/nvp";

    const AUTH_REQUEST = 'USER=%s&PWD=%s&SIGNATURE=%s';


    /**
     * Initializes NVP service
     * @return
     */
    protected function _initService()
    {

        $this->setService(new Varien_Object);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $this->getData('service')->setHandler($ch);

    }

    /**
     * Initializes NVP service or returns existing
     * @return Varien_Object
     */
    public function getService($function = null)
    {
        if (!$this->getData('service')) {
            $this->_initService();
        }
        return $this->getData('service');
    }

    /**
     * Prepares request for send
     * @return string
     */
    protected function _prepareRequest()
    {
        $out = array();
        foreach ($this->getRequest()->getData() as $k => $v) {
            $out[] = urlencode($k) . "=" . urlencode($v);
        }
        return implode("&", $out);
    }

    /**
     * Prepares "auth" part of NVP request.
     * @return string
     */
    protected function _prepareAuthRequest()
    {
        return sprintf(self::AUTH_REQUEST, urlencode($this->getAPIUsername()), urlencode($this->getAPIPassword()), urlencode($this->getAPISignature()));
    }


    /**
     * Runs request to API.
     * @param string $method
     * @throws Indies_Recurringandrentalpayments_Exception
     * @return array
     */
    public function _runRequest($method)
    {
        $ch = $this->getService()->getHandler();

        if (Indies_Recurringandrentalpayments_Model_Payment_Method_Paypal_Direct::ENDPOINT_SANDBOX == $this->getAPIEndpoint()) {
            $API_Endpoint = self::NVP_SANDBOX_URI;
        } else {
            $API_Endpoint = self::NVP_URI;
        }
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);


        // Assemble request and set it
        $req = implode('&', array(
                                 $this->_prepareAuthRequest(),
                                 'VERSION=' . self::VERSION,
                                 'METHOD=' . urlencode($method), 'useraction=commit',
                                 $this->_prepareRequest()
                            ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

        // getting response from server
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            throw new Indies_Recurringandrentalpayments_Exception("PayPal Direct $method failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }


        $httpResponseAr = explode("&", $httpResponse);
        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = urldecode($tmpAr[1]);
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            throw new Indies_Recurringandrentalpayments_Exception("PayPal Direct: Invalid HTTP Response for POST to $API_Endpoint.");
        }
        return $httpParsedResponseAr;
    }

    /**
     * Wrapper for ->_runRequest
     * @param object $methosd
     * @return array
     */
    public function runRequest($method)
    {
        return $this->_runRequest($method);
    }

}