<?php
class Magestore_RewardPointsTransfer_Model_Cron
{
    /**
     * cron check pending, holding transfer -> complete, cancel transfer
     * @param type $observer
     */
    public function checkPendingTransferTransaction($observer) {
        $toDay = time();

        $transfers = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->getCollection()
                ->addFieldToFilter('status', array('in' => array(Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING, Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING)));
        foreach ($transfers as $transfer) {
            if ($transfer->getHoldingDay() > 0) $holdDay = $transfer->getHoldingDay();
            else $holdDay = 0;
            if ($transfer->getPendingDay() > 0) $pendDay = $transfer->getPendingDay();
            else $pendDay = 0;

            $holdDay = strtotime($transfer->getCreatedTime()) + $holdDay*86400;
            $pendDay = strtotime($transfer->getCreatedTime()) + $pendDay*86400;
            
            if ($transfer->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING) {
                if ($toDay >= $holdDay) {
                    Mage::helper('rewardpointstransfer')->completeTransfer($transfer);
                }
            } else if ($transfer->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING) {
                if ($toDay >= $pendDay) {
                    $reason = Mage::helper('rewardpointstransfer')->__('Canceled as the pending day is over.');
                    Mage::helper('rewardpointstransfer')->cancelTransfer($transfer, $reason);
                }
            }
        }
    }
}