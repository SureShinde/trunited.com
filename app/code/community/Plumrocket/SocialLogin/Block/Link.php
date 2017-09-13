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

class Plumrocket_SocialLogin_Block_Link extends Mage_Core_Block_Template
{

	protected $_oneDay = 86400;

	/**
	 * Show popup
	 * @return boolean
	 */
	public function showPopup()
	{
		if (!Mage::helper('pslogin')->moduleEnabled() || !Mage::getStoreConfigFlag('pslogin/link/enable_popup') || !Mage::getStoreConfigFlag('pslogin/link/enable')) {
			return false;
		}

		$customer = $this->_session()->getCustomer();

		/* Is a guest */
		if (!$this->_session()->isLoggedIn() || !$customer || !$customer->getId() ) {
			return false;
		}

		/* Created less than a day */
		$currentTimestamp = Mage::getSingleton('core/date')->timestamp();
		if (($customer->getCreatedAtTimestamp() + $this->_oneDay ) > $currentTimestamp ) {
			return false;
		}

		$lastShown = $customer->getData('pslogin_link_popup_date');

		if ($lastShown !== null) {
			$configTime = (int)Mage::getStoreConfig('pslogin/link/timeout');
			$lastShown = strtotime($lastShown);

			if (!$configTime) {
				return false;
			}

			$mustShow = $configTime * $this->_oneDay + $lastShown;

			if ($mustShow > $currentTimestamp) {
				return false;
			}
		}

		$customer->setData('pslogin_link_popup_date', date('Y-d-m H:i:s', $currentTimestamp))
			->save();

		/* Subscribed to all networks */
		$buttons = $this->helper('pslogin')->getButtons();
		$accounts = Mage::getModel('pslogin/account')->getCollection()
			->addFieldToFilter('customer_id', $customer->getId());

		if ($accounts->count() == count($buttons)) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieve descripton for popup
	 * @return string
	 */
	public function getDescription()
	{
		return $this->helper('pslogin')->getLinkingDescription();
	}

	/**
	 * Retrieve customer session
	 * @return Mage_Customer_Model_Session
	 */
	protected function _session()
	{
		return Mage::getSingleton('customer/session');
	}
}