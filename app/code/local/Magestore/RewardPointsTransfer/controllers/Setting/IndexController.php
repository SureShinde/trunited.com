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
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsTransfer Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
require_once 'Magestore/RewardPoints/controllers/IndexController.php';
class Magestore_RewardPointsTransfer_Setting_IndexController extends Magestore_RewardPoints_IndexController
{
    /**
     * index action
     */
    public function settingsPostAction()
    {
        if(Mage::helper('rewardpointstransfer')->isEnable()){
            if ($this->getRequest()->isPost()
                && Mage::getSingleton('customer/session')->isLoggedIn()
            ) {
                $customerId     = Mage::getSingleton('customer/session')->getCustomerId();
                $rewardAccount  = Mage::getModel('rewardpoints/customer')->load($customerId, 'customer_id');
                if (!$rewardAccount->getId()) {
                    $rewardAccount->setCustomerId($customerId)
                        ->setData('point_balance', 0)
                        ->setData('holding_balance', 0)
                        ->setData('spent_balance', 0);
                }
                $rewardAccount->setIsNotification((boolean)$this->getRequest()->getPost('is_notification'))
                    ->setExpireNotification((boolean)$this->getRequest()->getPost('expire_notification'))
                    ->setTransferNotification((boolean)$this->getRequest()->getPost('transfer_notification'));
                try {
                    $rewardAccount->save();
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('rewardpoints')->__('Your settings has been updated successfully.'));
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
            }
            $this->_redirect('rewardpoints/index/settings');
        }else parent::settingsPostAction ();
    }
}