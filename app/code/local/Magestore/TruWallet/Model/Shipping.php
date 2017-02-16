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
 * Class Magestore_TruWallet_Model_Shipping
 */
class Magestore_TruWallet_Model_Shipping extends Varien_Object {

    public function getAllShippingMethods()
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

        $options = array();

        foreach($methods as $_ccode => $_carrier)
        {
            $_methodOptions = array();
            if($_methods = $_carrier->getAllowedMethods())
            {
                foreach($_methods as $_mcode => $_method)
                {
                    $_code = $_ccode . '_' . $_mcode;
                    $_methodOptions[] = array('value' => $_code, 'label' => $_method);
                }

                if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                    $_title = $_ccode;

                $options[] = array('value' => $_methodOptions, 'label' => $_title);
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    static public function getOptionArray() {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

        $options = array();

        foreach($methods as $_ccode => $_carrier)
        {
            if($_methods = $_carrier->getAllowedMethods())
            {
                foreach($_methods as $_mcode => $_method)
                {
                    $_code = $_ccode . '_' . $_mcode;
                    if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                        $_title = $_ccode;
                    else
                        $_title = Mage::getStoreConfig("carriers/$_ccode/title");

                    $options[$_code] = $_title;
                }
            }
        }

        return $options;
    }

    /**
     * @return array
     */
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