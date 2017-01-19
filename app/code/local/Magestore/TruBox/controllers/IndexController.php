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
        $item_id = $this->getRequest()->getParam('id');

        try{
            $item = Mage::getModel('trubox/item')->load($item_id);
            if(!$item->getId())
                throw new Exception(
                    Mage::helper('trubox')->__('Item does not exist !')
                );

            $item->delete();

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have deleted item successfully !')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/index'));
    }

    public function saveItemsAction() {
        $data = $this->getRequest()->getParams();
        try{
            $transactionSave = Mage::getModel('core/resource_transaction');
            foreach ($data as $id=>$qty) {
                $item = Mage::getModel('trubox/item')->load($id);
                if($item->getId()){
                    if($qty == 0)
                        $item->delete();
                    else{
                        $item->setQty($qty);
                        $transactionSave->addObject($item);
                    }
                }
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

            if (!$product->getId())
            {
                throw new Exception(
                    Mage::helper('trubox')->__('Product does not exist')
                );
            }

            if(Mage::helper('trubox')->isInExclusionList($product))
            {
                throw new Exception(
                    Mage::helper('trubox')->__('You can not add this product to TruBox')
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

            $truBox_obj = null;
            if (!$checkItem->getItemId()) {
                $itemData = array(
                    'trubox_id' => $truBoxId,
                    'product_id' => $productId,
                    'qty' => 1,
                    'origin_params' => $str_encode != "null" ? $str_encode : $str_option,
                    'option_params' => $str_encode != "null" ? $str_encode : $str_option

                );
                $truBoxItems->setData($itemData)->save();
                $truBox_obj = $truBoxItems;
            } else {

                if ((strcasecmp($checkItem->getOptionParams(), $str_encode) == 0 && $str_encode != "null")
                    || (strcasecmp($checkItem->getOptionParams(), $str_option) == 0 && $str_option != "null")
                ) {
                    $qtyCheckItem = $checkItem->getQty();
                    $checkItem->setQty($qtyCheckItem + 1);
                    $checkItem->save();
                    $truBox_obj = $checkItem;
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
                    $truBox_obj = $truBoxItems;
                }
            }

            $price = Mage::helper('trubox/item')->getItemPrice($truBox_obj);
            $truBox_obj->setPrice($price);
            $truBox_obj->save();

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('%s was added to your TruBox.',$product->getName())
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
            $this->_redirectUrl($product->getProductUrl());
            return;
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

    public function updateDb3Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            DROP TABLE IF EXISTS {$setup->getTable('trubox/order')};

            CREATE TABLE {$setup->getTable('trubox/order')} (
              `trubox_order_id` int(10) unsigned NOT NULL auto_increment,
              `trubox_id` int(10) NOT NULL,
              `order_id` int(10) NOT NULL,
              `updated_time` datetime NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`trubox_order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

             ALTER TABLE {$setup->getTable('trubox/item')} ADD `price` FLOAT ;

		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb4Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("

             ALTER TABLE {$setup->getTable('trubox/order')} DROP COLUMN  `customer_id`;
             ALTER TABLE {$setup->getTable('trubox/order')} ADD `trubox_id` INT ;

		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb5Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("

             ALTER TABLE {$setup->getTable('trubox/item')} DROP COLUMN  `order_id`;

		");
        $installer->endSetup();
        echo "success";
    }

    public function updatePriceAction()
    {
        Mage::helper('trubox/item')->updatePrice();
    }

    public function createOrderAction()
    {
        $order = Mage::helper('trubox/order')->createOrder(
            3719,
            array(
                // Add configurable product
                array(
                    'product' => 168,
                    'super_attribute' => array(
                        92 => 7,
                    ),
                    'qty' => 3
                ),
                // Add products with custom options
                array(
                    'product' => 169,
                    'options' => array(
                        158 => array(
                            282, 283
                        )
                    ),
                    'qty' => 2
                ),
                // Add 1-3 random simple products
                array(
                    'product' => 2,
                    'qty' => 2
                ),
            )
        );

//        zend_debug::dump($order->debug());
    }

}
