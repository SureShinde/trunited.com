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
 * @module     TruBox
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruBox Index Controller
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @author      Magestore Developer
 */
class Magestore_TruBox_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * check customer is logged in
     */
    public function preDispatch() {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        $action = $this->getRequest()->getActionName();
        if ($action != 'policy' && $action != 'redirectLogin') {
            // Check customer authentication
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                Mage::getSingleton('customer/session')->setAfterAuthUrl(
                    Mage::getUrl($this->getFullActionName('/'))
                );
                $this->_redirect('customer/account/login');
                $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            }
        }
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('trubox')->__('My TruBox'));
        $this->renderLayout();
    }

    public function saveAddressAction() {
        $address = $this->getRequest()->getPost();
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $address['trubox_id'] = $truBoxId;
        $truBoxAddress = Mage::getModel('trubox/address');
        $truBoxFilter = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)->getFirstItem();
        $truBoxAddressId = $truBoxFilter->getAddressId();
        if ($truBoxAddressId) {
            $truBoxAddress->load($truBoxAddressId)->addData($address);
            $truBoxAddress->setId($truBoxAddressId)->save();
        } else {
            $truBoxAddress->setData($address)->save();
        }
        $this->_redirect('mytrubox/index/index');
    }

    public function savePaymentAction() {
        $address = $this->getRequest()->getPost();
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $address['trubox_id'] = $truBoxId;
        $truBoxAddress = Mage::getModel('trubox/payment');
        $truBoxFilter = Mage::getModel('trubox/payment')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)->getFirstItem();
        $truBoxPaymentId = $truBoxFilter->getPaymentId();
        if ($truBoxPaymentId) {
            $truBoxAddress->load($truBoxPaymentId)->addData($address);
            $truBoxAddress->setId($truBoxPaymentId)->save();
        } else {
            $truBoxAddress->setData($address)->save();
        }
        $this->_redirect('mytrubox/index/index');
    }

    public function deleteItemsAction() {
        $productId = $this->getRequest()->getParam('id');
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $truBoxFilter = Mage::getModel('trubox/item')->getCollection()->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('product_id', $productId)->getFirstItem();
        $itemId = $truBoxFilter->getItemId();
        Mage::getModel('trubox/item')->setId($itemId)->delete();
        $this->_redirect('mytrubox/index/index');
    }

    public function saveItemsAction() {
        $itemData = $this->getRequest()->getPost();
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        foreach ($itemData as $k=>$v) {
            $truBoxFilter = Mage::getModel('trubox/item')->getCollection()->addFieldToFilter('trubox_id', $truBoxId)
                ->addFieldToFilter('product_id', $k)->getFirstItem();
            $truBoxFilter->setQty($v)->save();
        }
        $this->_redirect('mytrubox/index/index');
    }

    public function getRegionHtmlAction() {
        $countryCode = $this->getRequest()->getPost('code');
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('state')
            ->setId('region')
            ->setTitle(Mage::helper('checkout')->__('State/Province'))
            ->setClass('required-entry validate-state')
            ->setValue($countryCode)
            ->setOptions($this->getRegionCollection($countryCode)->toOptionArray());
        $this->getResponse()->setBody($select->getHtml());
    }

    public function getRegionCollection($countryCode)
    {
        $regionCollection = Mage::getModel('directory/region')->getResourceCollection()
            ->addCountryFilter($countryCode)
            ->load();
        return $regionCollection;
    }

    public function getRegionCollectionTruBox($countryCode)
    {
        $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
        return $regionCollection;
    }

    public function addTruBoxAction() {
        $productId = $this->getRequest()->getParam('id');
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $truBox = Mage::getModel('trubox/trubox');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customer->getId();
        $truBoxData = array('customer_id' => $customerId, 'status' => 'open');
        if (!$truBoxId) {
            $truBoxId = $truBox->setData($truBoxData)->save()->getTruboxId();
        }
        $truBoxItems = Mage::getModel('trubox/item');
        $checkItem = $truBoxItems->getCollection()->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('product_id', $productId)->getFirstItem();
        if (!$checkItem->getItemId()) {
            $itemData = array('trubox_id' => $truBoxId, 'product_id' => $productId, 'qty' => 1);
            $truBoxItems->setData($itemData)->save();
        } else {
            $qtyCheckItem = $checkItem->getQty();
            $checkItem->setQty($qtyCheckItem + 1)->save();
        }
        $this->_redirect('mytrubox/index/index');
    }

}
