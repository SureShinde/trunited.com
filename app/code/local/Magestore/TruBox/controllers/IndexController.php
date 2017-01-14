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
            } else {
                if (!Mage::helper('trubox')->isCurrentCheckingCustomer()) {
                    Mage::getSingleton('core/session')->addNotice(
                        Mage::helper('trubox')->__('You don\'t have permission for this action')
                    );
                    $this->_redirect('customer/account/');
                    $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                }
            }
        }
    }

    /**
     * index action
     */
    public function indexAction() {
        /* Check default address when trubox address is null */
        Mage::helper('trubox')->firstCheckAddress();
        /* End check default address when trubox address is null */
        $this->loadLayout();
        $this->_title(Mage::helper('trubox')->__('My TruBox'));
        $this->renderLayout();
    }

    public function saveAddressAction() {
        $billing = $this->getRequest()->getParam('billing');
        $shipping = $this->getRequest()->getParam('shipping');

        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $address['trubox_id'] = $truBoxId;

        $billing['trubox_id'] = $truBoxId;
        $shipping['trubox_id'] = $truBoxId;
        $billing['address_type'] = Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING;
        $shipping['address_type'] = Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING;

        try {
            /* save data to billing address */
            $billing_model = Mage::getModel('trubox/address')->getCollection()
                ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
                ->addFieldToFilter('trubox_id', $truBoxId)
                ->getFirstItem();

            if ($billing_model == null) {
                $billing_model = Mage::getModel('trubox/address');
            }
            $billing_model->addData($billing);
            $billing_model->save();
            /* end save data to billing address */

            /* save data to shipping address */
            $shipping_model = Mage::getModel('trubox/address')->getCollection()
                ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING)
                ->addFieldToFilter('trubox_id', $truBoxId)
                ->getFirstItem();

            if ($shipping_model == null) {
                $shipping_model = Mage::getModel('trubox/address');
            }
            $shipping_model->addData($shipping);
            $shipping_model->save();
            /* end save data to shipping address */

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have updated TruBox Address successfully !')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function savePaymentAction() {
        $address = $this->getRequest()->getPost();
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $address['trubox_id'] = $truBoxId;

        try {
            $payment = Mage::getModel('trubox/payment')->getCollection()
                ->addFieldToFilter('trubox_id', $truBoxId)
                ->getFirstItem()
            ;

            if($payment == null)
                $payment = Mage::getModel('trubox/payment');

            $payment->addData($address);
            $payment->save();

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have updated Payment Information successfully !')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function deleteItemsAction() {
        $productId = $this->getRequest()->getParam('id');
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();

        $truBoxFilter = Mage::getModel('trubox/item')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('product_id', $productId)
            ->getFirstItem()
        ;

        $itemId = $truBoxFilter->getItemId();

        try{
            Mage::getModel('trubox/item')->setId($itemId)->delete();

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have deleted item successfully !')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function saveItemsAction() {
        $itemData = $this->getRequest()->getPost();
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();

        try{
            $transactionSave = Mage::getModel('core/resource_transaction');
            foreach ($itemData as $k=>$v) {
                $truBoxFilter = Mage::getModel('trubox/item')->getCollection()
                    ->addFieldToFilter('trubox_id', $truBoxId)
                    ->addFieldToFilter('product_id', $k)
                    ->getFirstItem()
                ;
                $truBoxFilter->setQty($v);
                $transactionSave->addObject($truBoxFilter);
            }
            $transactionSave->save();

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have updated item(s) successfully !')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
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
        $product = Mage::getModel('catalog/product')->load($productId);

        $super_attributes = $this->getRequest()->getParam('super_attribute');
        $str_encode = json_encode($super_attributes);

        $options = $this->getRequest()->getParam('options');
        $str_option = json_encode($options);

        try {

            if (!$product->getId()){
                throw new Exception(
                    Mage::helper('trubox')->__('Product does not exist')
                );
            }

            $flag = false;
            if ($str_encode == "null" && $product->getTypeId() == 'configurable')
            {
                $options = Mage::helper('trubox')->getConfigurableOptionProduct($product);
                foreach ($options as $_option) {
                    $attr = Mage::getModel('catalog/resource_eav_attribute')->load($_option['attribute_id']);

                    if ($attr->getId() && $attr->getIsRequired()) {
                        $flag = true;
                        break;
                    }
                }

                if ($flag) {
                    Mage::getSingleton('core/session')->addError(
                        Mage::helper('trubox')->__('Please specify product option(s).')
                    );
                    $this->_redirectUrl($product->getProductUrl());
                    return;
                }
            }

            if ($str_option == "null" && $product->getHasOptions()) {
                $_flag = false;
                if ($product->getHasOptions()) {
                    foreach ($product->getOptions() as $o) {
                        if ($o->getIsRequire()) {
                            $_flag = true;
                            break;
                        }
                    }
                }

                if ($_flag) {
                    Mage::getSingleton('core/session')->addError(
                        Mage::helper('trubox')->__('Please specify product option(s).')
                    );
                    $this->_redirectUrl($product->getProductUrl());
                    return;
                }
            }

            $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
            $truBox = Mage::getModel('trubox/trubox');
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerId = $customer->getId();

            $truBoxData = array('customer_id' => $customerId, 'status' => 'open');
            if (!$truBoxId) {
                $truBoxId = $truBox->setData($truBoxData)->save()->getTruboxId();
            }

            $truBoxItems = Mage::getModel('trubox/item');
            $checkItem = $truBoxItems->getCollection()
                ->addFieldToFilter('trubox_id', $truBoxId)
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('option_params', $str_encode != "null" ? $str_encode : $str_option)
                ->getFirstItem()
            ;

            if (!$checkItem->getItemId()) {
                $itemData = array(
                    'trubox_id' => $truBoxId,
                    'product_id' => $productId,
                    'qty' => 1,
                    'origin_params' => $str_encode != "null" ? $str_encode : $str_option,
                    'option_params' => $str_encode != "null" ? $str_encode : $str_option

                );
                $truBoxItems->setData($itemData)->save();
            } else {

                if ((strcasecmp($checkItem->getOptionParams(), $str_encode) == 0 && $str_encode != "null")
                    || (strcasecmp($checkItem->getOptionParams(), $str_option) == 0 && $str_option != "null")
                ) {
                    $qtyCheckItem = $checkItem->getQty();
                    $checkItem->setQty($qtyCheckItem + 1);
                    $checkItem->save();
                } else {
                    $truBoxItems = Mage::getModel('trubox/item');
                    $itemData = array(
                        'trubox_id' => $truBoxId,
                        'product_id' => $productId,
                        'qty' => 1,
                        'origin_params' => $str_encode != "null" ? $str_encode : $str_option,
                        'option_params' => $str_encode != "null" ? $str_encode : $str_option,

                    );

                    $truBoxItems->setData($itemData)->save();
                }
            }

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('%s was added to your TruBox.',$product->getName())
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function updateDbAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE {$setup->getTable('trubox/address')} ADD `address_type` int(10) DEFAULT 2;
            ALTER TABLE {$setup->getTable('trubox/address')} ADD `region` text DEFAULT NULL ;
            ALTER TABLE {$setup->getTable('trubox/address')} ADD `region_id` int(10);
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb2Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE {$setup->getTable('trubox/item')} ADD `origin_params` text DEFAULT NULL;
            ALTER TABLE {$setup->getTable('trubox/item')} ADD `option_params` text DEFAULT NULL;
		");
        $installer->endSetup();
        echo "success";
    }

}
