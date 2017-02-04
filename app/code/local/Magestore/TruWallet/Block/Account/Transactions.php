<?php

class Magestore_TruWallet_Block_Account_Transactions extends Mage_Core_Block_Template {

    protected function _construct() {
        parent::_construct();
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $collection = Mage::getResourceModel('truwallet/transaction_collection')
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('created_time', 'DESC')
                ->setOrder('transaction_id','DESC');
        $this->setCollection($collection);
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'transactions_pager')
                ->setCollection($this->getCollection());
        $this->setChild('transactions_pager', $pager);
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('transactions_pager');
    }

}
