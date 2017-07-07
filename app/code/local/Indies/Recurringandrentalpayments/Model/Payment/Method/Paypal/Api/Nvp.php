<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * NVP API wrappers model
 * @TODO: move some parts to abstract, don't hesitate to throw exceptions on api calls
 */
class Indies_Recurringandrentalpayments_Model_Payment_Method_Paypal_Api_Nvp extends Mage_Paypal_Model_Api_Nvp//Mage_Paypal_Model_Api_Abstract
{
      /**
     * DoExpressCheckout call
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
     */
    public function callDoExpressCheckoutPayment()
    {
        $this->_prepareExpressCheckoutCallRequest($this->_doExpressCheckoutPaymentRequest);
        $request = $this->_exportToRequest($this->_doExpressCheckoutPaymentRequest);
        $this->_exportLineItems($request);
	
        $response = $this->call(self::DO_EXPRESS_CHECKOUT_PAYMENT, $request);
/*  Date : 1 feb 2016 : set billing agreement id in session */	
	if (isset($response['BILLINGAGREEMENTID']))
	{
		$billing_agreement_id = $response['BILLINGAGREEMENTID'];
		Mage::getSingleton('core/session')->setBillingAgreementId($response['BILLINGAGREEMENTID']); 
	}
	else 
	{
	    $customerId = Mage::getSingleton('customer/session')->getCustomerId(); 
	    $active_agrrement = Mage::getModel('sales/billing_agreement')->getCollection()
			 	->addFieldToFilter('customer_id',$customerId)
				->addFieldToFilter('status','active')
				->getFirstItem();
	    if(count($active_agrrement->getData()) > 0)
	    {
	    	$billing_agreement_id = $active_agrrement->getReferenceId();		
	    }
	    else
	    {
		$billing_agreement_id = null;
	    }
	    Mage::getSingleton('core/session')->setBillingAgreementId($billing_agreement_id); 
		
	}
        $this->_importFromResponse($this->_paymentInformationResponse, $response);
        $this->_importFromResponse($this->_doExpressCheckoutPaymentResponse, $response);
        $this->_importFromResponse($this->_createBillingAgreementResponse, $response);
    }
}
