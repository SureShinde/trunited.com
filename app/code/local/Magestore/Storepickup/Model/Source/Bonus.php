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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_Source_Payment
 */
class Magestore_Storepickup_Model_Source_Bonus
{
	const BONUS_TYPE_PERCENT = 1;
	const BONUS_TYPE_FIXED = 2;
    /**
     * @return array|void
     */
    public function toOptionArray()
	{
		return self::getOptionHash();
	}

	static public function getOptionArray()
	{
		return array(
			self::BONUS_TYPE_PERCENT    => Mage::helper('storepickup')->__('Percent (%)'),
			self::BONUS_TYPE_FIXED   => Mage::helper('storepickup')->__('Fixed Amount')
		);
	}

	static public function getOptionHash(){
		$options = array();
		$options[] = '';
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}