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
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruBox Core Block Template Block
 * You should write block extended from this block when you write plugin
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @author      Magestore Developer
 */
class Magestore_TruBox_Block_Search extends Mage_Core_Block_Template {

    //construct function
    public function __construct() {
        parent::__construct();
    }

    //prepare layout
    public function _prepareLayout() {
        parent::_prepareLayout();
        return $this;
    }

    public function getProductCollection()
    {
        return $this->getProducts();
    }

    public function addItemsToTruBox()
    {
        return $this->getUrl('*/*/addProducts');
    }

    public function getTruBoxCollection()
    {
        return Mage::helper('trubox')->getCurrentTruBoxCollection();
    }

    public function getProductsFromTruBox()
    {
        $collection = $this->getTruBoxCollection();
        $rs = array();

        if(sizeof($collection) > 0) {
            foreach ($collection as $trubox) {
                $rs[$trubox->getProductId()] = array(
                    'qty' => $trubox->getQty(),
                    'type_item' => $trubox->getTypeItem()
                );
            }
        }

        return $rs;
    }

    public function displayName($name)
    {
        if(strlen($name) > 60)
            return substr($name, 0, 60).'...';
        else
            return $name;
    }
}
