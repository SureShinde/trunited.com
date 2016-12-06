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

class Indies_Recurringandrentalpayments_Model_Web_Service_Client_Request_Simple extends Varien_Object
{

    const POST_SPACER = "&";

    protected $_fields;
    protected $_defaultData = null;


    public function setOnceFields(array $fields)
    {
        $this->_fields = $fields;
    }

    public function reset()
    {
        $this->setData(array());
        return $this;
    }

    /**
     * Encode request as POST data
     * @return array
     */
    public function encodeRawPost()
    {
        $out = array();
        foreach ($this->getData() as $key => $value) {
            $out[] = urlencode($key) . "=" . urlencode($value);
        }
        return implode(self::POST_SPACER, $out);
    }

    /**
     * Attach data w/o rewriting whole data array
     * @param <type> $v1
     * @param <type> $v2
     * @return Indies_Recurringandrentalpayments_Model_Web_Service_Client_Request_Simple
     */
    public function attachData($v1, $v2 = null)
    {
        if (is_array($v1)) {
            foreach ($v1 as $k => $v) {
                $this->setData($k, $v);
            }
        } else {
            $this->setData($v1, $v2);
        }
        return $this;
    }

    /**
     * Adds bulk empty fields to the request(required by some web services)
     * @param array $data
     */
    public function setDefaultData(array $data)
    {
        $this->_defaultData = $data;
        return $this;
    }

    public function getDefaultData()
    {
        return $this->_defaultData;
    }

    /**
     * Return request data with merged default data
     * @return array
     */
    public function getRequestData()
    {
        if (is_array($this->getDefaultData())) {
            $data = array_merge($this->getDefaultData(), $this->getData());
        } else {
            $data = $this->getData();
        }
        return $data;
    }
}
