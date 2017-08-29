<?php

class Magestore_Affiliateplus_Block_Payment_Transfer extends Mage_Core_Block_Template {

    /**
     * get Helper
     *
     * @return Magestore_Affiliateplus_Helper_Config
     */
    public function _getHelper() {
        return Mage::helper('affiliateplus/config');
    }

    protected function _construct() {
        parent::_construct();
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $object_transfer = Mage::helper('affiliateplus/config')->getTransferConfig();
        if($object_transfer == 1)
            $collection = Mage::getModel('truwallet/transaction')->getCollection()
                    ->addFieldToFilter('customer_id', $account->getCustomerId())
                    ->addFieldToFilter('action_type', Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_TRANSFER)
                    ->setOrder('transaction_id', 'DESC');
        else if($object_transfer == 2)
            $collection = Mage::getModel('trugiftcard/transaction')->getCollection()
                ->addFieldToFilter('customer_id', $account->getCustomerId())
                ->addFieldToFilter('action_type', Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_TRANSFER)
                ->setOrder('transaction_id', 'DESC');
        else
            $collection = null;

        $this->setCollection($collection);
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'payments_pager')
                ->setTemplate('affiliateplus/html/pager.phtml')
                ->setCollection($this->getCollection());
        $this->setChild('payments_transfer_pager', $pager);

        $grid = $this->getLayout()->createBlock('affiliateplus/transferGrid', 'payments_transfer_grid');
//        $grid->setCollection($this->getCollection());

        // prepare column
        $grid->addColumn('id', array(
            'header' => $this->__('No.'),
            'align' => 'left',
            'render' => 'getNoNumber',
        ));

        $grid->addColumn('title', array(
            'header' => $this->__('Title'),
            'index' => 'title',
            'type' => 'text',
            'format' => 'medium',
            'align' => 'left',
            'searchable' => true,
        ));

        $grid->addColumn('changed_credit', array(
            'header' => $this->__('Amount'),
            'align' => 'left',
            'type' => 'baseprice',
            'index' => 'changed_credit',
            'searchable' => true,
            'width' => '100px',
        ));

        $grid->addColumn('created_time', array(
            'header' => $this->__('Created Time'),
            'index' => 'created_time',
            'type' => 'date',
            'format' => 'medium',
            'align' => 'left',
            'width' => '150px',
            'searchable' => true,
        ));

        $grid->addColumn('status', array(
            'header' => $this->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Magestore_TruWallet_Model_Status::getTransactionOptionArray(),
            'width' => '95px',
            'searchable' => true,
        ));


        $this->setChild('payments_transfer_grid', $grid);
        return $this;
    }

    public function getNoNumber($row) {
        return sprintf('#%d', $row->getId());
    }

    public function getFeeRow($row) {
        if ($row->getStatus() == 1)
            return $this->__('N/A');
        $fee = $row->getFee();
        if ($row->getIsPayerFee())
            $fee = 0;
        return Mage::helper('core')->currency($fee);
    }

    public function getPagerHtml() {
        return $this->getChildHtml('payments_transfer_pager');
    }

    public function getGridHtml() {
        return $this->getChildHtml('payments_transfer_grid');
    }

    protected function _toHtml() {
        $this->getChild('payments_transfer_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

}
