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
class Magestore_TruBox_Block_Category extends Mage_Core_Block_Template {

    //construct function
    public function __construct() {
        parent::__construct();
    }

    //prepare layout
    public function _prepareLayout() {
        parent::_prepareLayout();
        return $this;
    }

    public function getCategoryId() {
        return $this->getRequest()->getParam('id');
    }

    public function getCategory() {
        $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
        if($category != null && $category->getId())
            return $category;
        else
            return null;
    }

    public function getProductCollection()
    {
        $products = Mage::getModel('catalog/category')->load($this->getCategoryId())
            ->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', 4)
            ->setOrder('price', 'ASC');

        return $products;
    }

    public function addItemsToTruBox()
    {
        return $this->getUrl('*/*/addProducts');
    }
}
