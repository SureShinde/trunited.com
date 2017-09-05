<?php

class Magestore_SpecialOccasion_Block_Payment extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    public function getCurrentCustomer()
    {
        return Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
    }

    public function getPaymentOccasion() {
        $cards = Mage::getModel('tokenbase/card')->getCollection()
            ->addFieldToFilter( 'active', 1 )
            ->addFieldToFilter( 'customer_id', $this->getCurrentCustomer()->getId())
            ->addFieldToFilter( 'method', 'authnetcim')
            ->setOrder('use_in_occasion', 'desc')
            ->setOrder('id', 'desc')
        ;

        return $cards;
    }

    public function getCardUrl()
    {
        return $this->getUrl('customer/paymentinfo/');
    }

    public function getBackAction()
    {
        return $this->getUrl('*/');
    }

    public function savePaymentUrl()
    {
        return $this->getUrl('*/*/paymentPost');
    }
}
