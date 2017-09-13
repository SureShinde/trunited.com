<?php

class Magestore_Interest_Block_Interest extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

	public function getCollectionInterest()
    {
        $collection = Mage::getModel('interest/interest')
            ->getCollection()
            ->addFieldToFilter('status', Magestore_Interest_Model_Status::STATUS_ENABLED)
            ->setOrder('sort_order', 'desc')
            ;

        return $collection;
    }

    public function getActionUrl()
    {
        return $this->getUrl('*/*/saveInterest');
    }

    public function getCurrentCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getCurrentInterset()
    {
        $collection = Mage::getModel('interest/customer')
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getCurrentCustomer()->getId())
            ->setOrder('interest_customer_id', 'desc')
            ;

        return $collection;
    }

    public function isInCurrent($item_id)
    {
        $collection = $this->getCurrentInterset();
        if(sizeof($collection) > 0)
        {
            $interest_ids = $collection->getColumnValues('interest_id');

            return in_array($item_id, $interest_ids);
        } else
            return false;
    }
}
