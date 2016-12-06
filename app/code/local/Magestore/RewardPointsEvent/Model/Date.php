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
 * @package     Magestore_RewardpointsEvent
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsevent Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardpointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Model_Date extends Varien_Object {

    static public function getMonthArray() {
        $_months = array();
        for ($i = 1; $i <= 12; $i++) {
            $_months[$i] = Mage::app()->getLocale()
                    ->date(mktime(null, null, null, $i))
                    ->get(Zend_Date::MONTH_NAME);
        }
        return $_months;
    }

    static public function getMonthOptionHash() {
        $options = array();
        foreach (self::getMonthArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

    static public function getDayArray() {
        $_days = array();
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0' . $i : $i;
        }
        return $_days;
    }

    static public function getDayOptionHash() {
        $options = array();
        foreach (self::getDayArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

    static public function getWeekArray() {
        $dayofweek = array();
        $dayofweek = array(
            "Sun",
            "Mon",
            "Tue",
            "Wed",
            "Thu",
            "Fri",
            "Sat");
        return $dayofweek;
    }

    static public function getWeekOptionHash() {
        $options = array();
        foreach (self::getWeekArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

}
