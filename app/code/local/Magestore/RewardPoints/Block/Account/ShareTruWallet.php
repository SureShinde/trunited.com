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
class Magestore_RewardPoints_Block_Account_ShareTruWallet extends Magestore_RewardPoints_Block_Template {

    protected function _construct() {
        parent::_construct();
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $collection = Mage::getResourceModel('rewardpoints/transaction_collection')
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('action_type', array('in' => array(
                    Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_SHARE,
                    Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_RECEIVE_FROM_SHARING,
                )))
                ->addFieldToFilter('status',array('in'=>array(
                    Magestore_RewardPoints_Model_Transaction::STATUS_PENDING,
                    Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED,
                    Magestore_RewardPoints_Model_Transaction::STATUS_CANCELED,
                )))
                ->setOrder('updated_time', 'DESC')
                ->setOrder('transaction_id','DESC');
        $this->setCollection($collection);
    }

    public function _prepareLayout() {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'share_pager')
            ->setTemplate('rewardpoints/html/pager.phtml')
            ->setCollection($this->getCollection());

        $this->setChild('share_pager', $pager);

        $grid = $this->getLayout()->createBlock('rewardpoints/shareGrid', 'share_grid');

        // prepare column
        $grid->addColumn('title', array(
            'header'    => Mage::helper('rewardpoints')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
//            'searchable' => true,
        ));

        $grid->addColumn('receiver_email', array(
            'header' => Mage::helper('rewardpoints')->__('Email'),
            'align' => 'left',
            'index' => 'receiver_email',
//            'searchable' => true,
            'width' => '200px',
        ));

        $grid->addColumn('product_credit', array(
            'header' => Mage::helper('rewardpoints')->__('Amount'),
            'align' => 'left',
            'type' => 'baseprice',
            'index' => 'product_credit',
//            'searchable' => true,
            'width' => '150px',
        ));

        $grid->addColumn('updated_time', array(
            'header' => Mage::helper('rewardpoints')->__('Date Sent'),
            'index' => 'updated_time',
            'type' => 'date',
            'format' => 'medium',
            'align' => 'left',
            'width' => '180px',
//            'searchable' => true,
        ));

        $grid->addColumn('status', array(
            'header' => Mage::helper('rewardpoints')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => $this->__('Pending'),
                2 => $this->__('On Hold'),
                3 => $this->__('Complete'),
                4 => $this->__('Canceled'),
                5 => $this->__('Expired'),
            ),
            'width' => '95px',
//            'searchable' => true,
        ));

        $this->setChild('share_grid', $grid);
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('share_pager');
    }

    /**
     * get current balance of customer as text
     *
     * @return string
     */
    public function getBalanceText()
    {
        return Mage::helper('rewardpoints/customer')->getBalanceFormated();
    }

    public function getProductCreditBalanceText()
    {
        return Mage::helper('rewardpoints/customer')->getProductCreditBalanceFormated();
    }

    public function getProductCreditMoney()
    {
        $pointAmount = Mage::helper('rewardpoints/customer')->getProductCredit();
        if ($pointAmount > 0) {
            $rate = Mage::getModel('rewardpoints/rate')->getRate(Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                return Mage::app()->getStore()->convertPrice($baseAmount, true);
            }
        }
        return '';
    }

    /**
     * get holding balance of customer as text
     *
     * @return int
     */
    public function getHoldingBalance()
    {
        $holdingBalance = Mage::helper('rewardpoints/customer')->getAccount()->getHoldingBalance();
        if ($holdingBalance > 0) {
            return Mage::helper('rewardpoints/point')->format($holdingBalance);
        }
        return '';
    }

    public function getDescription()
    {
        return Mage::getStoreConfig('rewardpoints/sharewallet/description', Mage::app()->getStore()->getId());
    }

    public function getActionForm()
    {
        return Mage::getUrl('rewardpoints/index/sendTruWallet');
    }

    public function getAllEmail()
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('email')
            ->setOrder('entity_id','desc')
            ;
		$_customer = Mage::getSingleton('customer/session')->getCustomer();
        
		$emails = '';
        if(sizeof($collection) > 0){
            $is_last = 0;
            $size = sizeof($collection);
            foreach ($collection as $customer) {
				if($customer->getEmail() != $_customer->getEmail()){
					if($is_last == ($size - 1))
						$emails .= '"'.$customer->getEmail().'"';
					else
						$emails .= '"'.$customer->getEmail().'",';
				}
                
                $is_last++;
            }
        }

        return $emails;
    }

    public function getGridHtml() {
        return $this->getChildHtml('share_grid');
    }

    protected function _toHtml() {
        $this->getChild('share_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }


}
