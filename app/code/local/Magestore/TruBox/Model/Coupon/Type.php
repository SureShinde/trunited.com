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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_TruBox_Model_Coupon_Type
 */
class Magestore_TruBox_Model_Coupon_Type extends Varien_Object {

    const FIXED_AMOUNT = 0;
    const PERCENT_AMOUNT = 1;

    /**
     * @return array
     */
    static public function getOptionArray(){
        return array(
            self::FIXED_AMOUNT	=> Mage::helper('trubox')->__('Fixed points'),
            self::PERCENT_AMOUNT   => Mage::helper('trubox')->__('Percent of product points')
        );
    }


    public function toOptionArray() {
        $options = array();
        foreach (self::getOptionArray() as $value => $label)
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        return $options;
    }

}