<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_Holiday
 */
class Magestore_Storepickup_Model_Shipping extends Mage_Shipping_Model_Shipping
{

    public function collectCarrierRates($carrierCode, $request)
    {
        if (!$this->_checkCarrierAvailability($carrierCode, $request)) {
            return $this;
        }
        return parent::collectCarrierRates($carrierCode, $request);
    }

    protected function _checkCarrierAvailability($carrierCode, $request = null)
    {
        $isLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        $session = Mage::getSingleton('core/session');
        if (!$isLoggedIn) {
            if ($carrierCode == 'storepickup') {
                $session->setIsHideTruBox(0);
                return false;
            }
        } else  {
            $stores = Mage::helper('storepickup')->echoAllStoreCheckoutToJson(Mage::app()->getStore()->getId());
            if(!$stores){
                if ($carrierCode == 'storepickup') {
                    return false;
                }
            }
        }
        return true;
    }

}
