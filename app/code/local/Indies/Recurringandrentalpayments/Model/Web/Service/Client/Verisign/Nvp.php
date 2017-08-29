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

ini_set('memory_limit', '128M');

class Indies_Recurringandrentalpayments_Model_Web_Service_Client_Verisign_NVP extends Mage_Paypal_Model_Payflowpro
{

    /** Live NVP URI */
    const NVP_URI = "https://payflowpro.paypal.com";
    /** Test NVP URI */
    const NVP_SANDBOX_URI = "https://pilot-payflowpro.paypal.com";
    /** PayPal User config path */
    const XML_PATH_USER = 'paypal/verisign/user';
    /** PayPal Vendor config path */
    const XML_PATH_VENDOR = 'paypal/verisign/vendor';
    /** PayPal Partner config path */
    const XML_PATH_PARTNER = 'paypal/verisign/partner';
    /** PayPal password config path */
    const XML_PATH_PASSWORD = 'paypal/verisign/pwd';
    /** Is PayPal in sandbox mode */
    const XML_PATH_IS_SANDBOX = 'paypal/verisign/sandbox_flag';
    /** PayPal URI. Actual for magento 1.3 */
    const XML_PATH_URI = 'paypal/verisign/url';


    /**
     * Return authentication data as array ready to use for NVP request
     * @return array
     */
    public function getAuthData()
    {
        return array(
            'USER' => Mage::getStoreConfig(self::XML_PATH_USER),
            'VENDOR' => Mage::getStoreConfig(self::XML_PATH_VENDOR),
            'PARTNER' => Mage::getStoreConfig(self::XML_PATH_PARTNER),
            'PWD' => Mage::getStoreConfig(self::XML_PATH_PASSWORD)
        );
    }

    /**
     * Attach common data to request
     * @return Indies_Recurringandrentalpayments_Model_Web_Service_Client_PaypalUK_NVP
     */
    protected function _beforeRunRequest()
    {
        $this->getRequest()
                ->attachData($this->getAuthData()) // Auth data
        ;
    }

    /**
     * Return access URI
     * @return string
     */
    public function getURI()
    {
        if ($url = Mage::getStoreConfig(self::XML_PATH_URI)) {
            if (!Mage::getStoreConfig(self::XML_PATH_IS_SANDBOX)) {
                // Mageno 1.3
                return $url;
            }
        }
        if (Mage::getStoreConfig(self::XML_PATH_IS_SANDBOX)) {
            return (self::NVP_SANDBOX_URI);
        } else {
            return (self::NVP_URI);
        }
    }

    /**
     * Runs "Add Recurring Profile" transaction
     * @return Varien_Object
     */
    public function addAction()
    {
        $this->getRequest()
                ->attachData(array(
                                  'TRXTYPE' => 'A',
                                  'TENDER' => 'C', // Currently Credit Card only
                                  'ACTION' => 'A' // Add action
                             ));
        return $this->runRequest();
    }

    /**
     * Runs Account Verification transaction
     * @return Varien_Object
     */
    public function verifyAccountAction()
    {
        $this->getRequest()
                ->attachData(array(
                                  'TRXTYPE' => 'A',
                                  'TENDER' => 'C', // Currently Credit Card only
                                  'ACTION' => 'A' // Add action
                             ));
        return $this->runRequest();
    }

    /**
     * Runs Reference transaction
     * This type of transactions are required by Indies_Recurringandrentalpayments PayPalUK module and can be enabled at PayFlow manager
     * Also all transactions of that type can only be performed for one year.
     * @return Varien_Object
     */
    public function referenceCaptureAction()
    {
        $this->getRequest()
                ->attachData(array(
                                  'TRXTYPE' => 'D',
                                  'TENDER' => 'C', // Currently Credit Card only
                             ));
        return $this->runRequest();
    }

    /**
     * Runs Reference transaction
     * This type of transactions are required by Indies_Recurringandrentalpayments PayPalUK module and can be enabled at PayFlow manager
     * Also all transactions of that type can only be performed for one year.
     * @return Varien_Object
     */
    public function referenceAuthAction()
    {
        $this->getRequest()
                ->attachData(array(
                                  'TRXTYPE' => 'S',
                                  'TENDER' => 'C', // Currently Credit Card only
                             ));
        return $this->runRequest();
    }
}