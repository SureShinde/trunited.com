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
 * RewardPoints Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_CustomerController extends Mage_Core_Controller_Front_Action 
{
    public function registerAction()
    {
		$email = $this->getRequest()->getParam('email');
		Mage::getSingleton('core/session')->setEmailRefer($email);
		$this->_redirectUrl(Mage::getUrl('customer/account/create/'));
		return;
    }

	public function cancelTransactionAction()
	{
		$transaction_id = $this->getRequest()->getParam('id');
		$transaction = Mage::getModel('rewardpoints/transaction')->load($transaction_id);

		try{

			if(!$transaction->getId()){
				throw new Exception(Mage::helper('rewardpoints')->__('Transaction doesn\'t exist'));
			}

			$transaction->setUpdatedTime(now());
			$transaction->setStatus(Magestore_RewardPoints_Model_Transaction::STATUS_CANCELED);
			$transaction->save();

			$rewardAccount = Mage::helper('rewardpoints/customer')->getAccountByCustomerId($transaction->getCustomerId());
			$rewardAccount->setProductCredit($rewardAccount->getProductCredit() + abs($transaction->getProductCredit()));
			$rewardAccount->save();

			Mage::getSingleton('core/session')->addSuccess(
				Mage::helper('rewardpoints')->__('Transaction has been cancelled successfully')
			);

		} catch (Exception $ex) {
			Mage::getSingleton('customer/session')->addError(
				$ex->getMessage()
			);
		}

		$this->_redirectUrl(Mage::getUrl('rewardpoints/index/shareTruWallet'));
	}

}
