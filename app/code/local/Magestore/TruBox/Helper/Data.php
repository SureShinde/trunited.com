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
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruBox Helper
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 */
class Magestore_TruBox_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'rewardpoints/general/enable';

    /**
     *
     * @return string
     */
    public function getTruboxLabel()
    {
        return $this->__('My TruBox');
    }

    public function getCurrentTruBoxId() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $id = $customer->getId();
        $truBox = Mage::getModel('trubox/trubox')->getCollection()->addFieldToFilter('status', 'open')
            ->addFieldToFilter('customer_id', $id)->getFirstItem();
        $truBoxId = $truBox->getTruboxId();
        return $truBoxId;
    }

    public function firstCheckAddress()
    {
        $truBoxId = $this->getCurrentTruBoxId();
        $truBox_billing = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
            ->getFirstItem()
        ;

        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
        if($truBox_billing->getId() == null)
        {
            $customer_billing = $customer->getDefaultBillingAddress();
            if( $customer_billing != null && $customer_billing->getId())
            {
                $_billing = Mage::getModel('trubox/address');
                $_billing->setData('address_type',Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING);
                $_billing->setData('trubox_id',$truBoxId);
                $_billing->setData('firstname',$customer_billing->getFirstname());
                $_billing->setData('lastname',$customer_billing->getLastname());
                $_billing->setData('company','');
                $_billing->setData('telephone',$customer_billing->getTelephone());
                $_billing->setData('fax','');
                $_billing->setData('street',$customer_billing['street']);
                $_billing->setData('region_id',$customer_billing->getRegionId());
                $_billing->setData('region',$customer_billing->getRegion());
                $_billing->setData('city',$customer_billing->getCity());
                $_billing->setData('zipcode',$customer_billing->getPostcode());
                $_billing->setData('country',$customer_billing->getCountryId());
                $_billing->save();
            }
        }

        $truBox_shipping = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING)
            ->getFirstItem()
        ;

        if($truBox_shipping->getId() == null)
        {
            $customer_shipping = $customer->getDefaultShippingAddress();

            if($customer_shipping != null && $customer_shipping->getId())
            {
                $_shipping = Mage::getModel('trubox/address');
                $_shipping->setData('address_type',Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING);
                $_shipping->setData('trubox_id',$truBoxId);
                $_shipping->setData('firstname',$customer_shipping->getFirstname());
                $_shipping->setData('lastname',$customer_shipping->getLastname());
                $_shipping->setData('company','');
                $_shipping->setData('telephone',$customer_shipping->getTelephone());
                $_shipping->setData('fax','');
                $_shipping->setData('street',$customer_shipping['street']);
                $_shipping->setData('region_id',$customer_shipping->getRegionId());
                $_shipping->setData('region',$customer_shipping->getRegion());
                $_shipping->setData('city',$customer_shipping->getCity());
                $_shipping->setData('zipcode',$customer_shipping->getPostcode());
                $_shipping->setData('country',$customer_shipping->getCountryId());
                $_shipping->save();
            }
        }


    }

}
