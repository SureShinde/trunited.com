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
 * Rewardpoints Total Point Earn/Spend Block
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Block_Adminhtml_Totals_Creditmemo_Rewards extends Mage_Adminhtml_Block_Template
{
    /**
     * get current creditmemo
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * check admin can refund point that customer spent
     *
     * @return boolean
     */
    public function canRefundTruWallet()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        if ($this->getMaxTruWalletRefund()) {
            return true;
        }
        return false;
    }

    /**
     * max point that admin can refund to customer
     *
     * @return int
     */
    public function getMaxTruWalletRefund()
    {
        if ($this->hasData('truWallet_refund')) {
            return $this->getData('truWallet_refund');
        }
        $maxPointRefund = 0;
        if ($creditmemo = $this->getCreditmemo()) {
            $order = $creditmemo->getOrder();

            $memos = $this->getAllCreditMemoByOrder($order->getEntityId());

            $truWallet_earned = 0;
            if(sizeof($memos) > 0)
            {
                foreach ($memos as $me)
                {
                    $truWallet_earned += $me->getTruwalletEarn();
                }
            }

            $return_balances = $order->getData('truwallet_discount') - $truWallet_earned;
            $maxPointRefund = $return_balances > 0 ? $return_balances : $order->getData('truwallet_discount');

            $this->setData('current_truWallet', $maxPointRefund);
        }
        $this->setData('truWallet_refund', $maxPointRefund);
        return $this->getData('truWallet_refund');
    }

    /**
     * get current refund points for this credit memo
     *
     * @return int
     */
    public function getCurrentTruWallet()
    {
        if (!$this->hasData('truWallet_refund')) {
            $this->getMaxTruWalletRefund();
        }
        return (int)$this->getData('current_truWallet');
    }

    public function getAllCreditMemoByOrder($order_id)
    {
        $collection = Mage::getResourceModel('sales/order_creditmemo_collection')
            ->addAttributeToFilter('order_id', array('eq' => $order_id))
            ->addAttributeToSelect('*')
        ;

        return $collection;
    }
}
