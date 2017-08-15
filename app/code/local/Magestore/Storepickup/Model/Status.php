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
 * Class Magestore_Storepickup_Model_Status
 */
class Magestore_Storepickup_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    const DISTANCE_BY_MILES = 1;
    const DISTANCE_BY_FEET = 2;
    const DISTANCE_BY_YARDS = 3;
    const DISTANCE_BY_KILOMETERS = 4;
    const DISTANCE_BY_METERS = 5;

    /**
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('storepickup')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('storepickup')->__('Disabled')
        );
    }

    static public function getOptionDistanceArray()
    {
        return array(
            self::DISTANCE_BY_MILES    => Mage::helper('storepickup')->__('Miles'),
            self::DISTANCE_BY_FEET   => Mage::helper('storepickup')->__('Feet'),
            self::DISTANCE_BY_YARDS   => Mage::helper('storepickup')->__('Yards'),
            self::DISTANCE_BY_KILOMETERS   => Mage::helper('storepickup')->__('Kilometers'),
            self::DISTANCE_BY_METERS   => Mage::helper('storepickup')->__('Meters'),
        );
    }

    static public function getOptionDistanceHash(){
        $options = array();
        $options[] = '';
        foreach (self::getOptionDistanceArray() as $value => $label)
            $options[] = array(
                'value'	=> $value,
                'label'	=> $label
            );
        return $options;
    }
}