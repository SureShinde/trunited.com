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

class Indies_Recurringandrentalpayments_Model_Payment_Method_Ewayrapid_Response extends Eway_Rapid31_Model_Response
{
    
    /**
     * Decode response returned by eWAY API call
     *
     * @param $response
     * @return Eway_Rapid31_Model_Response
     */
    public function decodeJSON($response)
    {
        $json = json_decode($response, true);
        $this->addData($json);
        if(!empty($json['Customer']) && is_array($json['Customer'])) {
            $this->_setIfNotEmpty($json['Customer'], 'TokenCustomerID');
            if(!empty($json['Customer']['CardDetails']) && !empty($json['Customer']['CardDetails']['Number'])) {
                $this->setData('CcLast4', substr($json['Customer']['CardDetails']['Number'], -4));
            }
        }

        if(!empty($json['Errors'])) {
            $this->setErrors(explode(',', $json['Errors']));
        }

        if(isset($json['TransactionStatus'])) {
            // Use TransactionStatus if it's presented in response
            $this->isSuccess((bool)$this->getTransactionStatus());

            // Check response message has fraud code
            if (isset($json['ResponseMessage']) && $this->isSuccess()) {
				
				if(isset($json['Customer']['TokenCustomerID']))
				{
					Mage::getSingleton('core/session')->setcustToken($json['Customer']['TokenCustomerID']);
				}
				$codeMessage = str_replace(' ', '', $json['ResponseMessage']);
                $codeMessage = explode(',', $codeMessage);
                //$codeMessage = array_flip($codeMessage);

                //$result = array_intersect_key($this->_messageCode, $codeMessage);
                $result = preg_grep("/^F.*/", $codeMessage);
                if (!empty($result)) {
                    $codes = array_flip($result);
                    $resultMatched = array_intersect_key($this->_messageCode, $codes);
                    $resultDefault = array_fill_keys(array_keys($codes), "Unknown fraud rule");
                    $resultMessages = array_merge($resultDefault,$resultMatched);
                    Mage::getSingleton('core/session')->setData('fraud', 1);
                    $fraudMessage = implode(', ', $resultMessages);
                    Mage::getSingleton('core/session')->setData('fraudMessage', $fraudMessage);
                }
            }
        } else {
            // Otherwise base on the Errors (Token transactions)
            $this->isSuccess(!$this->getErrors());

            // Catch empty response
            if(!isset($json['TransactionStatus'])
                    && !isset($json['TransactionID'])
                    && !isset($json['Customer']['TokenCustomerID'])
                    && !isset($json['AccessCode'])
                    && !isset($json['Transactions'])) {
                $this->isSuccess(false);
            }
        }

        return $this;
    }

    /**
     * Sets a value in the object if present
     *
     * @param array $json
     * @param string $key
     */
    private function _setIfNotEmpty($json, $key)
    {
        if(!empty($json[$key])) {
            $this->setData($key, $json[$key]);
        }
    }
}