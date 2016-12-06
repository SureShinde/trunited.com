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

class Indies_Recurringandrentalpayments_Model_Web_Service_Verisign_Client extends Indies_Recurringandrentalpayments_Model_Web_Service_Client
{

	const XML_PATH_VERISIGN_SANDBOX_FLAG = 'payment/verisign/sandbox_flag' ;
	const XML_PATH_VERISIGN_USER = 'payment/verisign/user';
    const XML_PATH_VERISIGN_VENDOR = 'payment/verisign/vendor';
    const XML_PATH_VERISIGN_PARTNER = 'payment/verisign/partner';
	const XML_PATH_VERISIGN_PWD = 'payment/verisign/pwd';
	const XML_PATH_VERISIGN_VERBOSITY = 'payment/verisign/verbosity';
	const XML_PATH_VERISIGN_USE_PROXY = 'payment/verisign/use_proxy';
	const XML_PATH_VERISIGN_PROXY_HOST = 'payment/verisign/proxy_host';
	const XML_PATH_VERISIGN_PROXY_PORT = 'payment/verisign/proxy_port';
	const XML_PATH_VERISIGN_VERIFY_PEER = 'payment/verisign/verify_peer';
	const XML_PATH_VERISIGN_DEBUG = 'payment/verisign/debug';
   
    /**
     * Transaction action codes
     */
    const TRXTYPE_AUTH_ONLY         = 'A';
    const TRXTYPE_SALE              = 'S';
    const TRXTYPE_CREDIT            = 'C';
    const TRXTYPE_DELAYED_CAPTURE   = 'D';
    const TRXTYPE_DELAYED_VOID      = 'V';
    const TRXTYPE_DELAYED_VOICE     = 'F';
    const TRXTYPE_DELAYED_INQUIRY   = 'I';
	const TRXTYPE_RECURRING			= 'R';
	const TRXTYPE_REACTIVATE		= 'R';
	const TRXTYPE_CANCEL			= 'C';

    /**
     * Tender type codes
     */
    const TENDER_CC = 'C';

    /**
     * Gateway request URLs
     */
    const TRANSACTION_URL           = 'https://payflowpro.paypal.com/transaction';
    const TRANSACTION_URL_TEST_MODE = 'https://pilot-payflowpro.paypal.com/transaction';

    /**
     * Response codes
     */
    const RESPONSE_CODE_APPROVED                = 0;
    const RESPONSE_CODE_INVALID_AMOUNT          = 4;
    const RESPONSE_CODE_FRAUDSERVICE_FILTER     = 126;
    const RESPONSE_CODE_DECLINED                = 12;
    const RESPONSE_CODE_DECLINED_BY_FILTER      = 125;
    const RESPONSE_CODE_DECLINED_BY_MERCHANT    = 128;
    const RESPONSE_CODE_CAPTURE_ERROR           = 111;
    const RESPONSE_CODE_VOID_ERROR              = 108;

    protected $_clientTimeout = 45;

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array('user', 'pwd', 'acct', 'expdate', 'cvv2');

    /**
     * Centinel cardinal fields map
     *
     * @var string
     */
    protected $_centinelFieldMap = array(
        'centinel_mpivendor'    => 'MPIVENDOR3DS',
        'centinel_authstatus'   => 'AUTHSTATUS3DS',
        'centinel_cavv'         => 'CAVV',
        'centinel_eci'          => 'ECI',
        'centinel_xid'          => 'XID',
    );


     /**
      * Return request object with basic information for gateway request
      *
      * @param Mage_Sales_Model_Order_Payment $payment
      * @return Varien_Object
      */
    protected function _buildBasicRequest()
    {
        $request = new Varien_Object();
        $request
            ->setUser(Mage::getStoreConfig(self::XML_PATH_VERISIGN_USER))
            ->setVendor(Mage::getStoreConfig(self::XML_PATH_VERISIGN_VENDOR))
            ->setPartner(Mage::getStoreConfig(self::XML_PATH_VERISIGN_PARTNER))
            ->setPwd(Mage::getStoreConfig(self::XML_PATH_VERISIGN_PWD))
            ->setVerbosity(Mage::getStoreConfig(self::XML_PATH_VERISIGN_VERBOSITY))
            ->setTender(self::TENDER_CC)
            ->setRequestId($this->_generateRequestId());
        return $request;
    }
	
	
    /**
     * Runs request to API.
     * @param string $method
     * @throws Indies_Recurringandrentalpayments_Exception
     * @return array
     */
    protected function _postRequest(Varien_Object $request)
    {
        $debugData = array('request' => $request->getData());		

        $client = new Varien_Http_Client();
        $result = new Varien_Object();

        $_config = array(
            'maxredirects' => 5,
            'timeout'    => 30,
            'verifypeer' => Mage::getStoreConfig(self::XML_PATH_VERISIGN_VERIFY_PEER)
        );

        $_isProxy =  Mage::getStoreConfig(self::XML_PATH_VERISIGN_USE_PROXY, false);
        if ($_isProxy) {
            $_config['proxy'] = Mage::getStoreConfig(self::XML_PATH_VERISIGN_PROXY_HOST)
                . ':'
                . Mage::getStoreConfig(self::XML_PATH_VERISIGN_PROXY_PORT);//http://proxy.shr.secureserver.net:3128',
            $_config['httpproxytunnel'] = true;
            $_config['proxytype'] = CURLPROXY_HTTP;
        }

        $client->setUri($this->_getTransactionUrl())
            ->setConfig($_config)
            ->setMethod(Zend_Http_Client::POST)
            ->setParameterPost($request->getData())
            ->setHeaders('X-VPS-VIT-CLIENT-CERTIFICATION-ID: 33baf5893fc2123d8b191d2d011b7fdc')
            ->setHeaders('X-VPS-Request-ID: ' . $request->getRequestId());
          //  ->setHeaders('X-VPS-CLIENT-TIMEOUT: ' . $this->_clientTimeout);

        try {
           /**
            * we are sending request to payflow pro without url encoding
            * so we set up _urlEncodeBody flag to false
            */
            $response = $client->setUrlEncodeBody(false)->request();
        }
        catch (Exception $e) {
            $result->setResponseCode(-1)
                ->setResponseReasonCode($e->getCode())
                ->setResponseReasonText($e->getMessage());

            $debugData['result'] = $result->getData();
            $this->_debug($debugData);
            throw $e;
        }



        $response = strstr($response->getBody(), 'RESULT');
        $valArray = explode('&', $response);

        foreach($valArray as $val) {
                $valArray2 = explode('=', $val);
                $result->setData(strtolower($valArray2[0]), $valArray2[1]);
        }

        $result->setResultCode($result->getResult())
                ->setRespmsg($result->getRespmsg());

        $debugData['result'] = $result->getData();
        $this->_debug($debugData);

        return $result;
    }
	 /**
     * Getter for URL to perform Payflow requests, based on test mode by default
     *
     * @param bool $testMode Ability to specify test mode using
     * @return string
     */
    protected function _getTransactionUrl($testMode = null)
    {
        $testMode = is_null($testMode) ?  Mage::getStoreConfig(self::XML_PATH_VERISIGN_SANDBOX_FLAG) : (bool)$testMode;
        if ($testMode) {			
            return self::TRANSACTION_URL_TEST_MODE;
        }
        return self::TRANSACTION_URL;
    }
	 /**
      * Return unique value for request
      *
      * @return string
      */
    protected function _generateRequestId()
    {
        return Mage::helper('core')->uniqHash();
    }

     /**
      * If response is failed throw exception
      *
      * @throws Mage_Core_Exception
      */
    protected function _processErrors(Varien_Object $response)
    {
        if ($response->getResultCode() == self::RESPONSE_CODE_VOID_ERROR) {
            throw new Mage_Paypal_Exception(Mage::helper('paypal')->__('You cannot void a verification transaction'));
        } elseif ($response->getResultCode() != self::RESPONSE_CODE_APPROVED
            && $response->getResultCode() != self::RESPONSE_CODE_FRAUDSERVICE_FILTER) {
            Mage::throwException($response->getRespmsg());
        }
    }

    /**
     * Adopt specified address object to be compatible with Paypal
     * Puerto Rico should be as state of USA and not as a country
     *
     * @param Varien_Object $address
     */
    protected function _applyCountryWorkarounds(Varien_Object $address)
    {
        if ($address->getCountry() == 'PR') {
            $address->setCountry('US');
            $address->setRegionCode('PR');
        }
    }
    /**
     * Wrapper for ->_runRequest
     * @param object $methosd
     * @return array
     */
    public function postRequest($request)
    {
        return $this->_postRequest($request);
    }
	public function buildBasicRequest()
    {
        return $this->_buildBasicRequest();
    }
	public function processErrors($response)
    {
        return $this->_processErrors($response);
    }
	/**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    protected function _debug($debugData)
    {
        if ($this->getDebugFlag()) {
            Mage::getModel('core/log_adapter', 'payment_verisign.log')
               ->setFilterDataKeys($this->_debugReplacePrivateDataKeys)
               ->log($debugData);
        }
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return Mage::getStoreConfig(self::XML_PATH_VERISIGN_DEBUG) ;
    }
	

}