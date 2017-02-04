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
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints All Transactions
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Block_Account_ShareTruWallet extends Mage_Core_Block_Template {

    protected function _construct() {
        parent::_construct();
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $collection = Mage::getResourceModel('truwallet/transaction_collection')
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('action_type', array('in' => array(
                    Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_SHARING,
                    Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING,
                )))
                ->addFieldToFilter('status',array('in'=>array(
                    Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING,
                    Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED,
                    Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED,
                )))
                ->setOrder('updated_time', 'DESC')
                ->setOrder('transaction_id','DESC');
        $this->setCollection($collection);
    }

    public function _prepareLayout() {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'share_pager')
            ->setTemplate('truwallet/html/pager.phtml')
            ->setCollection($this->getCollection());

        $this->setChild('share_pager', $pager);

        $grid = $this->getLayout()->createBlock('truwallet/shareGrid', 'share_grid');

        // prepare column
        $grid->addColumn('action_type', array(
            'header'    => Mage::helper('truwallet')->__('Action'),
            'align'     => 'left',
            'index'     => 'action_type',
            'type'      => 'options',
            'options'   => Mage::getSingleton('truwallet/type')->getOptionArray(),
        ));

        $grid->addColumn('receiver_email', array(
            'header' => Mage::helper('truwallet')->__('Email'),
            'align' => 'left',
            'index' => 'receiver_email',
            'width' => '200px',
        ));

        $grid->addColumn('changed_credit', array(
            'header' => Mage::helper('truwallet')->__('Amount'),
            'align' => 'left',
            'type' => 'baseprice',
            'index' => 'changed_credit',
            'width' => '150px',
        ));

        $grid->addColumn('updated_time', array(
            'header' => Mage::helper('truwallet')->__('Date Sent'),
            'index' => 'updated_time',
            'type' => 'date',
            'format' => 'medium',
            'align' => 'left',
            'width' => '180px',
        ));

        $grid->addColumn('status', array(
            'header' => Mage::helper('truwallet')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Magestore_TruWallet_Model_Status::getTransactionOptionArray(),
            'width' => '95px',
        ));

        $grid->addColumn('cancel', array(
            'header' => Mage::helper('truwallet')->__('Action'),
            'index' => 'cancel',
            'align' => 'left',
            'type' => 'action',
            'action' => array(
                            'label' => 'Cancel',
                            'url' 	=> 'truwallet/transaction/cancelTransaction',
                            'name'	=> 'id',
                            'field'	=> 'transaction_id',
      					),
        ));

        $this->setChild('share_grid', $grid);
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('share_pager');
    }

    public function getActionForm()
    {
        return Mage::getUrl('truwallet/transaction/sendTruWallet');
    }

    public function getGridHtml() {
        return $this->getChildHtml('share_grid');
    }

    protected function _toHtml() {
        $this->getChild('share_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

    public function getTruWalletCredit()
    {
        return Mage::helper('truwallet/account')->getTruWalletCredit();
    }


}
