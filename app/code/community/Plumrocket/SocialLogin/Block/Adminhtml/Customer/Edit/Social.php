
<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SocialLogin
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_SocialLogin_Block_Adminhtml_Customer_Edit_Social extends Mage_Adminhtml_Block_Template
{

	public function getSocialAccounts()
	{
		$customer = Mage::registry('current_customer');
		$collection = Mage::getModel('pslogin/account')->getCollection()
			->addFieldToFilter('customer_id', $customer->getId());

		$accounts = array();
	    foreach ($collection as $account) {
            $accounts[] = Mage::getModel('pslogin/'.$account->getType())
                ->setData($account->getData())
                ->setPhoto(Mage::helper('pslogin')->getPhotoPath(false, $customer->getId(), $account->getType()));
        }

		return $accounts;
	}

}
