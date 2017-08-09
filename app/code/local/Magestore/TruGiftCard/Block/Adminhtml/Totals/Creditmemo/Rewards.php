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
class Magestore_TruGiftCard_Block_Adminhtml_Totals_Creditmemo_Rewards extends Mage_Adminhtml_Block_Template
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
    public function canRefundTruGiftCard()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        if ($this->getMaxTruGiftCardRefund()) {
            return true;
        }
        return false;
    }

    /**
     * max point that admin can refund to customer
     *
     * @return int
     */
    public function getMaxTruGiftCardRefund()
    {
        if ($this->hasData('truGiftCard_refund')) {
            return $this->getData('truGiftCard_refund');
        }
        $maxPointRefund = 0;
        if ($creditmemo = $this->getCreditmemo()) {
            $order = $creditmemo->getOrder();

            $memos = $this->getAllCreditMemoByOrder($order->getEntityId());

            $truGiftCard_earned = 0;
            if(sizeof($memos) > 0)
            {
                foreach ($memos as $me)
                {
                    $truGiftCard_earned += $me->getTrugiftcardEarn();
                }
            }

            $return_balances = $order->getData('trugiftcard_discount') - $truGiftCard_earned;
            $maxPointRefund = $return_balances > 0 ? $return_balances : $order->getData('trugiftcard_discount');

            $this->setData('current_truGiftCard', $maxPointRefund);
        }

        $this->setData('truGiftCard_refund', $maxPointRefund);
        return $this->getData('truGiftCard_refund');
    }

    /**
     * get current refund points for this credit memo
     *
     * @return int
     */
    public function getCurrentTruGiftCard()
    {
        if (!$this->hasData('truGiftCard_refund')) {
            $this->getMaxTruGiftCardRefund();
        }
        return $this->getData('current_truGiftCard');
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
