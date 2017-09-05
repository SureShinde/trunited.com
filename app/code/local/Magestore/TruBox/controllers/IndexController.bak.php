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
class Magestore_TruBox_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * check customer is logged in
     */
    public function preDispatch()
    {
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
    public function indexAction()
    {
        /* Check default address when trubox address is null */
        Mage::helper('trubox')->firstCheckAddress();
        /* End check default address when trubox address is null */
        $this->loadLayout();
        $this->_title(Mage::helper('trubox')->__('My TruBox'));
        $this->renderLayout();
    }

    public function saveAddressAction()
    {
        $billing = $this->getRequest()->getParam('billing');
        $shipping = $this->getRequest()->getParam('shipping');

        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $address['trubox_id'] = $truBoxId;

        $billing['trubox_id'] = $truBoxId;
        $shipping['trubox_id'] = $truBoxId;
        $billing['address_type'] = Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING;
        $shipping['address_type'] = Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING;

        try {
            /* Check Region ID of billing and shipping address first */
            $billing['region_id'] = Mage::helper('trubox/order')->checkRegionId($billing['country'], $billing['region'], $billing['region_id']);
            if ($billing['region_id'] == null)
                throw new Exception(
                    Mage::helper('trubox')->__('Please enter the State in Billing Address.')
                );

            $shipping['region_id'] = Mage::helper('trubox/order')->checkRegionId($shipping['country'], $shipping['region'], $shipping['region_id']);
            if ($shipping['region_id'] == null)
                throw new Exception(
                    Mage::helper('trubox')->__('Please enter the State in Shipping Address.')
                );


            /* END Check Region ID of billing and shipping address first */

            /* save data to billing address */
            $billing_model = Mage::getModel('trubox/address')->getCollection()
                ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
                ->addFieldToFilter('trubox_id', $truBoxId)
                ->getFirstItem();

            if ($billing_model == null) {
                $billing_model = Mage::getModel('trubox/address');
                $billing['created_at'] = now();
            }

            $billing['updated_at'] = now();
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
                $shipping['created_at'] = now();
            }
            $shipping['updated_at'] = now();
            $shipping_model->addData($shipping);
            $shipping_model->save();
            /* end save data to shipping address */

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have updated TruBox address successfully!')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/address'));
    }

    public function savePaymentAction()
    {
        $address = $this->getRequest()->getPost();
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $address['trubox_id'] = $truBoxId;
        $current_credit_card = $this->getRequest()->getParam('current_credit_card');

        $use_trugiftcard = $this->getRequest()->getParam('use_trugiftcard');
        $truBox = Mage::helper('trubox')->getCurrentTruBox();

        try {
            if ($current_credit_card > 0) {
                $cards = Mage::getModel('tokenbase/card')->getCollection()
                    ->addFieldToFilter('active', 1)
                    ->addFieldToFilter('customer_id', $current_credit_card)
                    ->addFieldToFilter('method', 'authnetcim');

                if (sizeof($cards) > 0) {
                    foreach ($cards as $_card) {
                        $_card->setData('use_in_trubox', 0);
                        $_card->setData('updated_at', now());
                        $_card->save();
                    }

                }

                $card = Mage::getModel('tokenbase/card')->load($current_credit_card);
                if ($card != null && $card->getId()) {
                    $card->setData('use_in_trubox', 1);
                    $card->setData('updated_at', now());
                    $card->save();
                }
            }

            if ($truBox != null && $truBox->getId()) {
                $truBox->setData('use_trugiftcard', $use_trugiftcard != null && strcasecmp($use_trugiftcard, 'on') == 0 ? 1 : 0);
                $truBox->save();
            }

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have updated Payment Information successfully!')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/payment'));
    }

    public function deleteItemsAction()
    {
        $item_id = $this->getRequest()->getParam('id');

        try {
            $item = Mage::getModel('trubox/item')->load($item_id);
            if (!$item->getId())
                throw new Exception(
                    Mage::helper('trubox')->__('Item does not exist !')
                );

            $item->delete();

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have deleted item successfully!')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/items'));
    }

    public function clearItemsAction()
    {
        try {
            $items = Mage::helper('trubox')->getCurrentTruBoxCollection();
            if (sizeof($items) > 0) {
                foreach ($items as $item)
                    $item->delete();
            }

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have deleted all items successfully!')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/items'));
    }

    public function saveItemsAction()
    {
        $data = $this->getRequest()->getParams();
        try {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $delete_items = array();
            $day_on_time = Mage::getStoreConfig('trubox/general/current_date');
            $current_day = date('d', time());
            foreach ($data as $id => $qty) {
                if (strpos($id, 'ype_') > 0) {
                    $_id = str_replace('type_', '', $id);
                    $item = Mage::getModel('trubox/item')->load($_id);
                    if ($item->getId()) {
                        if ($data[$_id] == 0)
                            $item->delete();
                        else {
                            $item->setQty($data[$_id]);
                            $item->setUpdatedAt(now());
                            $item->setTypeItem((int)$qty);
                            if ($qty == Magestore_TruBox_Model_Type::TYPE_ONE_TIME) {
                                if ($current_day < $day_on_time) {
                                    $item->setOnetimeMonth(now());
                                    $item->setOnetimeMonthText(Mage::helper('trubox')->__('One Time (%s)', date('F', time())));
                                } else {
                                    $item->setOnetimeMonth(date('Y-m-d H:i:s', strtotime('first day of next month')));
                                    $item->setOnetimeMonthText(Mage::helper('trubox')->__('One Time (%s)', date('F', strtotime('first day of next month'))));
                                }
                            } else {
                                $item->setOnetimeMonth(null);
                                $item->setOnetimeMonthText(null);
                            }
                            $transactionSave->addObject($item);
                        }
                    }
                } else {
                    $delete_items[] = $id;
                }
            }

            if (sizeof($delete_items) > 0) {
                foreach ($delete_items as $del) {
                    $item = Mage::getModel('trubox/item')->load($del);
                    if ($item->getId() && (!isset($data['type_' . $del]) || $data['type_' . $del] == null)) {
                        $item->delete();
                    }
                }

            }

            $transactionSave->save();

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('You have updated item(s) successfully!')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/items'));
    }

    public function getRegionHtmlAction()
    {
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

    public function addTruBoxAction()
    {
        $productId = $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->load($productId);

        $super_attributes = $this->getRequest()->getParam('super_attribute');
        $str_encode = json_encode($super_attributes);

        $options = $this->getRequest()->getParam('options');
        $str_option = json_encode($options);

        $super_group = $this->getRequest()->getParam('super_group');

        try {

            if (!$product->getId()) {
                throw new Exception(
                    Mage::helper('trubox')->__('Product does not exist')
                );
            }

            if (Mage::helper('trubox')->isInExclusionList($product)) {
                throw new Exception(
                    Mage::helper('trubox')->__('You cannot add the <b>%s</b> to TruBox', $product->getName())
                );
            }

            $flag = false;

            if ($str_encode == "null" && $product->getTypeId() == 'configurable') {
                $options = Mage::helper('trubox')->getConfigurableOptionProduct($product);

                foreach ($options as $_option) {
                    $attr = Mage::getModel('catalog/resource_eav_attribute')->load($_option['attribute_id']);
                    if ($attr->getId() || $attr->getIsRequired()) {
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

            if (isset($super_group) && sizeof($super_group) > 0) {
                $check = false;
                foreach ($super_group as $id => $qty) {
                    if ($qty > 0) {
                        $check = true;
                        break;
                    }
                }

                if (!$check) {
                    Mage::getSingleton('core/session')->addError(
                        Mage::helper('trubox')->__('Please specify the quantity of product(s).')
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

            if (isset($super_group) && sizeof($super_group) > 0) {
                $transactionSave = Mage::getModel('core/resource_transaction');
                foreach ($super_group as $pid => $pqty) {
                    if ($pqty > 0) {
                        $truBoxItems = Mage::getModel('trubox/item');
                        $checkItem = $truBoxItems->getCollection()
                            ->addFieldToFilter('trubox_id', $truBoxId)
                            ->addFieldToFilter('product_id', $pid)
                            ->getFirstItem();
                        if (!$checkItem->getItemId()) {
                            $_p = Mage::getModel('catalog/product')->load($pid);
                            $itemData = array(
                                'trubox_id' => $truBoxId,
                                'product_id' => $pid,
                                'qty' => $pqty,
                                'origin_params' => '',
                                'option_params' => '',
                                'order_id' => '',
                                'price' => $_p->getPirce(),
                                'type_item' => Magestore_TruBox_Model_Type::TYPE_EVERY_MONTH,

                            );
                            $checkItem = Mage::getModel('trubox/item');
                            $checkItem->setData($itemData);
                        } else {
                            $qtyCheckItem = $checkItem->getQty();
                            $checkItem->setQty($qtyCheckItem + $pqty);
                        }
                        $transactionSave->addObject($checkItem);
                    }
                }
                $transactionSave->save();
            } else {
                $truBoxItems = Mage::getModel('trubox/item');
                $checkItem = $truBoxItems->getCollection()
                    ->addFieldToFilter('trubox_id', $truBoxId)
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('option_params', $str_encode != "null" ? $str_encode : $str_option)
                    ->getFirstItem();

                $truBox_obj = null;
                if (!$checkItem->getItemId()) {
                    $itemData = array(
                        'trubox_id' => $truBoxId,
                        'product_id' => $productId,
                        'qty' => 1,
                        'origin_params' => $str_encode != "null" ? $str_encode : $str_option,
                        'option_params' => $str_encode != "null" ? $str_encode : $str_option,

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
                            'type_item' => Magestore_TruBox_Model_Type::TYPE_EVERY_MONTH,

                        );

                        $truBoxItems->setData($itemData)->save();
                        $truBox_obj = $truBoxItems;
                    }
                }

                $price = Mage::helper('trubox/item')->getItemPrice($truBox_obj);
                $truBox_obj->setPrice($price);
                $truBox_obj->save();
            }

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('%s was added to your TruBox.', $product->getName())
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
            $this->_redirectUrl($product->getProductUrl());
            return;
        }

        $this->_redirectUrl(Mage::getUrl('*/*/items'));
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

    public function updateDb6Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE {$setup->getTable('trubox/trubox')} ADD `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP;
            ALTER TABLE {$setup->getTable('trubox/trubox')} ADD `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP;

            ALTER TABLE {$setup->getTable('trubox/item')} ADD `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP;
            ALTER TABLE {$setup->getTable('trubox/item')} ADD `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP;

            ALTER TABLE {$setup->getTable('trubox/address')} ADD `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP;
            ALTER TABLE {$setup->getTable('trubox/address')} ADD `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP;

            ALTER TABLE {$setup->getTable('trubox/payment')} ADD `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP;
            ALTER TABLE {$setup->getTable('trubox/payment')} ADD `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP;
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb7Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            DROP TABLE IF EXISTS {$setup->getTable('trubox/coupon')};

            CREATE TABLE {$setup->getTable('trubox/coupon')} (
              `trubox_coupon_id` int(10) unsigned NOT NULL auto_increment,
              `customer_id` int(10) NOT NULL,
              `order_id` int(10) NULL,
              `coupon_code` VARCHAR(255) NOT NULL,
              `type_code` smallint(6) NOT NULL,
              `amount` smallint(6) NOT NULL,
              `status` smallint(6) NOT NULL,
              `updated_time` datetime NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`trubox_coupon_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		");
        $installer->endSetup();
        echo "success";
    }

    public function updatePriceAction()
    {
        Mage::helper('trubox/item')->updatePrice();
    }

    //ALTER TABLE tablename MODIFY columnname INTEGER;
    public function cvvAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
			ALTER TABLE {$setup->getTable('trubox/payment')} MODIFY cvv VARCHAR(10);
        ");
        $installer->endSetup();
        echo "success";
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

    public function updateAddressAction()
    {
        var_dump($_SERVER);
        $collection = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('region_id', 0);

        if (sizeof($collection) > 0) {
            foreach ($collection as $col) {
                $region_id = Mage::helper('trubox/order')->checkRegionId($col->getCountry(), $col->getRegion());
                if ($region_id != null) {
                    $col->setRegionId($region_id)->save();
                }
            }
        }

        echo 'success';
    }

    public function saveCouponCodeAction()
    {
        $coupon_code = $this->getRequest()->getParam('coupon_code');
        $helper = Mage::helper('trubox');
        $enable = $helper->isEnableCouponCode();
        $default_code = $helper->getCouponCode();
        $start_date = $helper->getStartCouponCode();
        $end_date = $helper->getEndCouponCode();

        try {
            if (!$enable)
                throw new Exception(
                    $helper->__('The promotion code has been disabled.')
                );

            if (strtotime($start_date) > time() || strtotime($end_date) < time() || $start_date == null || $end_date == null)
                throw new Exception(
                    $helper->__('The promotion code has not yet started.')
                );

            if ($default_code == null)
                throw new Exception(
                    $helper->__('The promotion code has not been configured.')
                );

            if (!isset($coupon_code) || $coupon_code == null) {
                throw new Exception(
                    $helper->__('The promotion code is not valid. Please enter a new promotion code and try again.')
                );
            }

            if (strcasecmp($coupon_code, $default_code) != 0)
                throw new Exception(
                    $helper->__('The promotion code is not valid. Please enter a new promotion code and try again.')
                );

            $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $check_order = $helper->checkOrderFromTruBox($customer_id);
            if ($check_order)
                throw new Exception(
                    $helper->__('The promotion code entered is for new TruBox customers only. Please enter a different code and try again.')
                );

            $coupon_model = Mage::getModel('trubox/coupon');
            $data = array(
                'customer_id' => $customer_id,
                'coupon_code' => $coupon_code,
                'type_code' => $helper->getTypeCouponCode(),
                'amount' => $helper->getAmountCouponCode(),
                'status' => Magestore_TruBox_Model_Status::COUPON_CODE_STATUS_PENDING,
                'updated_time' => now(),
                'created_time' => now(),
            );
            $coupon_model->setData($data);
            $coupon_model->save();

            Mage::getSingleton('core/session')->addSuccess(
                $helper->__('The promotion code has been saved successfully!')
            );

        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function updateDb8Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE {$setup->getTable('trubox/trubox')} ADD `use_trugiftcard` tinyint(4) NULL DEFAULT 1;
		");
        $installer->endSetup();
        echo "success";
    }

    public function testAction()
    {
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addFieldToFilter('created_at', array('from' => '2017-08-06', 'to' => '2017-08-08'));
        $collection->addFieldToFilter('created_by', Magestore_TruBox_Model_Status::ORDER_CREATED_BY_ADMIN_YES);
        echo $collection->getSelect();
        foreach ($collection as $order) {
            zend_debug::dump($order->debug());
            exit;
        }

    }

    public function synchCIMAction()
    {
//        $cards = Mage::getModel('tokenbase/card')->getCollection()
//            ->addFieldToFilter( 'active', 1 )
//            ->addFieldToFilter( 'customer_id', 18790)
//            ->addFieldToFilter( 'method', 'authnetcim')
//        ;
//
//        zend_debug::dump($cards->getData());
//        zend_debug::dump($cards->getFirstItem()->getLabel());
//        exit;

        Mage::helper('trubox')->synchronizeCIM();
    }

    public function updateDb9Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE {$setup->getTable('tokenbase/card')} ADD `use_in_trubox` tinyint(4) NULL DEFAULT 0;
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb10Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE {$setup->getTable('trubox/item')} ADD `type_item` INT DEFAULT 2;
            ALTER TABLE {$setup->getTable('trubox/item')} ADD `onetime_month` datetime NULL;
            ALTER TABLE {$setup->getTable('trubox/item')} ADD `onetime_month_text` VARCHAR (255) NULL;

            DROP TABLE IF EXISTS {$setup->getTable('trubox/history')};
            CREATE TABLE {$setup->getTable('trubox/history')} (
              `history_id` int(10) unsigned NOT NULL auto_increment,
              `customer_id` int(10) NOT NULL,
              `customer_name` VARCHAR (255) NOT NULL,
              `customer_email` VARCHAR (255) NOT NULL,
              `order_id` int(10) NULL,
              `order_increment_id` int(10) NULL,
              `products` text NULL,
              `points` FLOAT NULL,
              `cost` FLOAT NULL,
              `updated_time` datetime NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`history_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
        $installer->endSetup();
        echo "success";
    }

    public function updatePointCostAction()
    {
        $data = json_decode($this->getRequest()->getParam('data'), true);

        $result = Mage::helper('trubox')->calculatePointsCost($data);

        echo json_encode($result);
    }

    public function itemsAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('trubox')->__('My TruBox Items'));
        $this->renderLayout();
    }

    public function addressAction()
    {
        /* Check default address when trubox address is null */
        Mage::helper('trubox')->firstCheckAddress();
        /* End check default address when trubox address is null */
        $this->loadLayout();
        $this->_title(Mage::helper('trubox')->__('My TruBox Address'));
        $this->renderLayout();
    }

    public function paymentAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('trubox')->__('My TruBox Payment'));
        $this->renderLayout();
    }

    public function categoryAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('trubox')->__('My TruBox Category'));
        $this->renderLayout();
    }

    public function updateDb11Action()
    {
        $setup = new Mage_Eav_Model_Entity_Setup ();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("

		");
        $setup->addAttribute('catalog_category', 'display_trubox', array(
            'group'             => 'TruBox',
            'type'              => 'int',
            'backend'           => '',
            'frontend_input'    => '',
            'frontend'          => '',
            'label'             => 'Display TruBox',
            'input'             => 'select',
            'default'           => array(0),
            'class'             => '',
            'source'            => 'eav/entity_attribute_source_boolean',
            'global'             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible'           => true,
            'frontend_class'     => '',
            'required'          => false,
            'user_defined'      => true,
            'position'            => 100,
        ));

        $setup->addAttribute('catalog_category', 'order_trubox', array(
            'group'             => 'TruBox',
            'type'              => 'int',
            'backend'           => '',
            'frontend_input'    => '',
            'frontend'          => '',
            'label'             => 'Order In TruBox',
            'input'             => 'text',
            'default'           => 1,
            'class'             => '',
            'source'            => '',
            'global'             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible'           => true,
            'frontend_class'     => '',
            'required'          => false,
            'user_defined'      => true,
            'position'            => 102,
        ));
        $installer->endSetup();
        echo "success";
    }

    public function addProductsAction()
    {
        $data = $this->getRequest()->getParams();
        $count = 0;
        if(sizeof($data) > 0) {

            $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
            $truBox = Mage::getModel('trubox/trubox');
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerId = $customer->getId();
            $truBoxData = array('customer_id' => $customerId, 'status' => 'open');
            if (!$truBoxId) {
                $truBoxId = $truBox->setData($truBoxData)->save()->getTruboxId();
            }

            $day_on_time = Mage::getStoreConfig('trubox/general/current_date');
            $current_day = date('d', time());
            foreach ($data as $key => $val) {
                if (strpos($key, 'ype_') > 0) {
                    $productId = str_replace('type_', '', $key);
                    $product = Mage::getModel('catalog/product')->load($productId);

                    try {
                        if (!$product->getId()) {
                            continue;
                        }

                        if (Mage::helper('trubox')->isInExclusionList($product)) {
                            throw new Exception(
                                Mage::helper('trubox')->__('You cannot add the <b><a href="%s">%s</a></b> to TruBox', $product->getProductUrl(), $product->getName())
                            );
                        }

                        $truBoxItems = Mage::getModel('trubox/item');
                        $checkItem = $truBoxItems->getCollection()
                            ->addFieldToFilter('trubox_id', $truBoxId)
                            ->addFieldToFilter('product_id', $productId)
                            ->getFirstItem();

                        $truBox_obj = null;
                        if ($checkItem != null && $checkItem->getId()) {
                            $checkItem->setData('qty', $checkItem->getQty()+ $data[$productId]);
                            $checkItem->setData('type_item', $val);
                            $checkItem->setData('updated_at', now());
                            if ($key == Magestore_TruBox_Model_Type::TYPE_ONE_TIME) {
                                if ($current_day < $day_on_time) {
                                    $checkItem->setOnetimeMonth(now());
                                    $checkItem->setOnetimeMonthText(Mage::helper('trubox')->__('One Time (%s)', date('F', time())));
                                } else {
                                    $checkItem->setOnetimeMonth(date('Y-m-d H:i:s', strtotime('first day of next month')));
                                    $checkItem->setOnetimeMonthText(Mage::helper('trubox')->__('One Time (%s)', date('F', strtotime('first day of next month'))));
                                }
                            } else {
                                $checkItem->setOnetimeMonth(null);
                                $checkItem->setOnetimeMonthText(null);
                            }
                            $checkItem->save();
                            $truBox_obj = $truBoxItems;
                        } else {
                            $truBoxItems = Mage::getModel('trubox/item');
                            $itemData = array(
                                'trubox_id' => $truBoxId,
                                'product_id' => $productId,
                                'qty' => $data[$productId],
                                'origin_params' => '',
                                'option_params' => '',
                                'updated_at' => now(),
                                'type_item' => $val,
                            );

                            if ($key == Magestore_TruBox_Model_Type::TYPE_ONE_TIME) {
                                if ($current_day < $day_on_time) {
                                    $itemData['onetime_month'] = now();
                                    $itemData['onetime_month_text'] = Mage::helper('trubox')->__('One Time (%s)', date('F', time()));
                                } else {
                                    $itemData['onetime_month'] = date('Y-m-d H:i:s', strtotime('first day of next month'));
                                    $itemData['onetime_month_text'] = Mage::helper('trubox')->__('One Time (%s)', date('F', strtotime('first day of next month')));
                                }
                            } else {
                                $itemData['onetime_month'] = '';
                                $itemData['onetime_month_text'] = '';
                            }

                            $truBoxItems->setData($itemData)->save();
                            $truBox_obj = $truBoxItems;
                        }

                        $price = Mage::helper('trubox/item')->getItemPrice($truBox_obj);
                        $truBox_obj->setPrice($price);
                        $truBox_obj->save();
                        $count++;
                    } catch (Exception $ex) {
                        Mage::getSingleton('core/session')->addError(
                            $ex->getMessage()
                        );

                    }
                }
            }
        }

        if($count > 0)
            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trubox')->__('%s were added to your TruBox.', $count)
            );
        $this->_redirectUrl(Mage::getUrl('*/*/items'));
    }

}
