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

class  Indies_Recurringandrentalpayments_Block_Adminhtml_Subscribed_Edit_Tab_Payments extends Mage_Adminhtml_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('recurringandrentalpayments/subscription/payments/history.phtml');
    }

	public function getSubscriptions()
	{
		return Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));	
	}
	public function getOrder()
	{
		$id=$this->getRequest()->getParam('id');
		$orderid=Mage::getModel('recurringandrentalpayments/subscription_item')->load($id,'subscription_id');
		$order_collection = Mage::getModel('sales/order')->load($orderid->getPrimaryOrderId());
		return $order_collection;
		
	}
    /**
     * Returns payments collection
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function getPaidCollection()
    {
		
        if (!$this->getData('paid_collection')) {
            $this->setPaidCollection(Mage::getModel('recurringandrentalpayments/sequence')
                                             ->getCollection()
                                             ->addFieldToFilter('subscription_id',$this->getRequest()->getParam('id'))
                                    /* ->addStatusFilter(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PAYED)
                                             ->setOrder('date', 'desc')*/
            );

        }
        return $this->getData('paid_collection');
    }


    /**
     * Returns pending payments collection
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function getPendingCollection()
    {
        if (!$this->getData('pending_collection')) {
            $this->setPendingCollection(Mage::getModel('recurringandrentalpayments/sequence')
                                             ->getCollection()
                                             ->addFieldToFilter('subscription_id',$this->getRequest()->getParam('id'))
                                 	 ->addStatusFilter(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PENDING)
                                              ->setOrder('date', 'asc')
            );

        }
        return $this->getData('pending_collection');
    }

    public function getConvertedPrice($value, $code)
    {

        $currency = Mage::getModel('directory/currency')->load($code);
        return $currency->format($value, array(), false);
        ;

    }
	public function getCollection()
    {
        if (!$this->getData('pending_collection')) {
            $this->setPendingCollection(Mage::getModel('recurringandrentalpayments/sequence')
                                             ->getCollection()
                                             ->addFieldToFilter('subscription_id',$this->getRequest()->getParam('id'))
                                             ->setOrder('date', 'asc')
            );

        }
        return $this->getData('pending_collection');
    }
}