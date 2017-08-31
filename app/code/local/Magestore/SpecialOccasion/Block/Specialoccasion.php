<?php

class Magestore_SpecialOccasion_Block_Specialoccasion extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    public function getAddNewAction()
    {
        return $this->getUrl('*/*/add');
    }

    public function getUpdateAction()
    {
        return $this->getUrl('*/*/update');
    }

    public function getOccasionsCollection()
    {
        return Mage::helper('specialoccasion')->getItemCollectionByCustomerId();
    }

    public function getCurrentId()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getId();
    }

    public function getShipName($item_id)
    {
        return Mage::helper('specialoccasion')->getShipName($item_id);
    }
}
