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


class Plumrocket_SocialLogin_Block_Link_Button extends Mage_Core_Block_Template
{
	/**
	 * Show full buttons
	 * @var boolean
	 */
	protected $_showFull = true;

	/**
	 * Retrieve link buttons
	 * @return Array
	 */
	public function getLinkButtons($part = null)
	{
		$buttons = Mage::helper('pslogin')->getPreparedButtons($part);

		$activeAccounts = $this->helper('pslogin')->getCustomerAccounts();
		foreach ($buttons as $key => $button) {
			if (isset($activeAccounts[$button['type']])) {
				unset($buttons[$key]);
			}
		}

		return $buttons;
	}

	/**
	 * Show full button
	 * @return boolean
	 */
	public function showFull()
	{
		return $this->_showFull;
	}

	/**
	 * Set show full
	 * @param string $showFull
	 */
	public function setShowFull($showFull)
	{
		$this->_showFull = $showFull;
		return $this;
	}
}
