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

class Plumrocket_SocialLogin_Model_Caching_Container_Link extends Enterprise_PageCache_Model_Container_Abstract
{
	/**
	 * {@inheritdoc}
	 */
	protected function _getCacheId()
	{
	    return 'plumrocket_social_login_placeholder' . $this->_getIdentifier();
	}

	/**
	 * Retrieve indentifier
	 */
	protected function _getIdentifier()
	{
	    return microtime();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _renderBlock()
	{
	    $blockClass = $this->_placeholder->getAttribute('block');
	    $template = $this->_placeholder->getAttribute('template');
	    $block = new $blockClass;
	    $block->setTemplate($template);
	    $layout = Mage::app()->getLayout();
	    $block->setLayout($layout);
	    return $block->toHtml();

	}

	/**
	 * {@inheritdoc}
	 */
	protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
	{
		return false;
	}
}
