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
 * TruBox Helper
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 */
class Magestore_TruBox_Helper_Order extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EMAIL_OUT_OF_STOCK = 'trubox/email/out_of_stock';
    const XML_PATH_EMAIL_SENDER = 'trubox/email/sender';

    protected $_shippingMethod = 'freeshipping_freeshipping';
    protected $_paymentMethod = 'authorizenet';
    protected $_freePaymentMethod = 'free';

    protected $_customer;

    protected $_subTotal = 0;
    protected $_order;
    protected $_storeId;

    public function setShippingMethod($methodName)
    {
        $this->_shippingMethod = $methodName;
    }

    public function setPaymentMethod($methodName)
    {
        $this->_paymentMethod = $methodName;
    }

    public function setCustomer($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer){
            $this->_customer = $customer;
        }
        if (is_numeric($customer)){
            $this->_customer = Mage::getModel('customer/customer')->load($customer);
        }
    }

    public function prepareOrder($data)
    {
        $flag = array();
        if (sizeof($data) > 0) {
            foreach ($data as $trubox_id => $itms) {
                $trubox = Mage::getModel('trubox/trubox')->load($trubox_id);
                if ($trubox->getId()) {
                    if ($rs = $this->createOrder($trubox->getCustomerId(), $itms))
                    {
                        if(sizeof($rs) > 0)
                            $flag[] = $rs;

                        sleep(1);
                    } else {

                    }

                }

            }
        }

        return $flag;
    }

    public function getAddressByTruBoxId($customer_id, $type = Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
    {
        $truBox_id = Mage::helper('trubox')->getCurrentTruBoxId($customer_id);
        if($truBox_id == null)
            return null;

        $address = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBox_id)
            ->addFieldToFilter('address_type', $type)
            ->getFirstItem()
            ;

        if($address->getId())
            return $address;
        else
            return null;
    }

    public function checkConditionCustomer($customer_id)
    {
        $truBox_id = Mage::helper('trubox')->getCurrentTruBoxId($customer_id);
        if($truBox_id == null)
            throw new Exception(
                Mage::helper('trubox')->__('TruBox does not exits !')
            );

        $address = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToSelect('address_id')
            ->addFieldToSelect('address_type')
            ->addFieldToFilter('trubox_id', $truBox_id)
            ;

        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if(sizeof($address) > 0)
        {
            $flag = 0;
            foreach ($address as $addr)
            {
                if($addr->getAddressType() == Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
                    $flag++;
                else if($addr->getAddressType() == Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING)
                    $flag++;
            }


            if($flag != 2)
                throw new Exception(
                    Mage::helper('trubox')->__('%s don\'t have address information !', $customer->getName())
                );

        } else {
            throw new Exception(
                Mage::helper('trubox')->__('%s don\'t have address information !', $customer->getName())
            );
        }

//        $payment = Mage::getModel('trubox/payment')->getCollection()
//                ->addFieldToFilter('trubox_id', $truBox_id)
//                ->getFirstItem()
//            ;
//
//        if(!$payment->getId())
//            throw new Exception(
//                Mage::helper('trubox')->__('%s don\'t have payment information !', $customer->getName())
//            );
    }

    public function getProductParams($customer_id, $data_items)
    {
        $truBox_id = Mage::helper('trubox')->getCurrentTruBoxId($customer_id);
        if($truBox_id == null)
            throw new Exception(
                Mage::helper('trubox')->__('TruBox does not exits !')
            );

        $data = array();
        $items = Mage::getModel('trubox/item')->getCollection()
            -> addFieldToFilter('trubox_id', $truBox_id)
            ->addFieldToFilter('item_id', array('in' => $data_items))
            ;


        if(sizeof($items) > 0)
        {
            foreach ($items as $item)
            {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());

                if ($product->getIsInStock() && $product->isSaleable() === true) {
                    if($item->getOptionParams() != null){
                        $option_params = json_decode($item->getOptionParams(), true);
                        if($product->getTypeId() == 'configurable')
                        {
                            $main_child_product = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($option_params, $product)->getId();
                            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
                            $flag = false;
                            foreach ($childProducts as $childProduct) {
                                $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                                if ($childProduct->getId() == $main_child_product) {

                                    if($qty <= 0)
                                    {
                                        $name = $product->getName().'<br />';
                                        $flag = true;
                                        $_options = Mage::helper('trubox')->getConfigurableOptionProduct($product);
                                        if ($_options && sizeof($option_params) > 0){
                                            foreach ($_options as $_option){
                                                $_attribute_value = 0;
                                                foreach ($option_params as $k => $v) {
                                                    if ($k == $_option['attribute_id']) {
                                                        $_attribute_value = $v;
                                                        break;
                                                    }
                                                }
                                                if ($_attribute_value > 0) {
                                                    $name .= '<small><b>'.$_option['label'].'</b></small><br />';
                                                    foreach ($_option['values'] as $val) {
                                                        if ($val['value_index'] == $_attribute_value) {
                                                            $name .= '<small><i>'.$val['default_label'].'</i></small>';
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        $data['email'][] = array(
                                            'product_name' => $name,
                                            'qty' => $item->getQty(),
                                            'price' => $item->getPrice(),
                                        );
                                    }
                                    break;
                                }
                            }

                            if(!$flag)
                            {
                                $data['product'][$item->getId()] = array(
                                    $item->getProductId() => array(
                                        'qty' => $item->getQty(),
                                        'super_attribute' => $option_params,
                                        '_processing_params' => array(),
                                    )
                                );
                            }
                        } else {
                            $data['product'][$item->getId()] = array(
                                $item->getProductId() => array(
                                    'qty' => $item->getQty(),
                                    'options' => $option_params,
                                    '_processing_params' => array(),
                                )
                            );
                        }
                    } else {
                        $data['product'][$item->getId()] = array(
                            $item->getProductId() => array(
                                'qty' => $item->getQty(),
                                '_processing_params' => array(),
                            )
                        );
                    }
                } else {
                    if($item->getOptionParams() != null){
                        $name = $product->getName().'<br />';
                        $option_params = json_decode($item->getOptionParams(), true);
                        if ($product->getTypeId() == 'configurable') {
                            $_options = Mage::helper('trubox')->getConfigurableOptionProduct($product);
                            if ($_options && sizeof($option_params) > 0){
                                foreach ($_options as $_option){
                                    $_attribute_value = 0;
                                    foreach ($option_params as $k => $v) {
                                        if ($k == $_option['attribute_id']) {
                                            $_attribute_value = $v;
                                            break;
                                        }
                                    }
                                    if ($_attribute_value > 0) {
                                        $name .= '<small><b>'.$_option['label'].'</b></small><br />';
                                        foreach ($_option['values'] as $val) {
                                            if ($val['value_index'] == $_attribute_value) {
                                                $name .= '<small><i>'.$val['default_label'].'</i></small>';
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($product->getOptions() as $o) {
                                $values = $o->getValues();
                                $_attribute_value = 0;

                                foreach ($option_params as $k => $v) {
                                    if ($k == $o->getOptionId()) {
                                        $_attribute_value = $v;
                                        break;
                                    }
                                }
                                if ($_attribute_value > 0) {
                                    $name .= '<small><b>'.$o->getTitle().'</b></small><br />';
                                    foreach ($values as $val) {
                                        if (is_array($_attribute_value)) {
                                            if (in_array($val->getOptionTypeId(), $_attribute_value)) {
                                                $name .= '<small><i>'.$val->getTitle().'</i></small>';
                                            }
                                        } else if ($val->getOptionTypeId() == $_attribute_value) {
                                            $name .= '<small><i>'.$val->getTitle().'</i></small>';
                                        }
                                    }
                                }
                            }
                        }
                        $data['email'][] = array(
                            'product_name' => $name,
                            'qty' => $item->getQty(),
                            'price' => $item->getPrice(),
                        );
                    } else {
                        $data['email'][] = array(
                            'product_name' => $product->getName(),
                            'qty' => $item->getQty(),
                            'price' => $item->getPrice(),
                        );
                    }
                }
            }
        }

        return $data;
    }

    public function getPaymentInformation($customer_id, $is_no_need_payment)
    {
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $truBox_id = Mage::helper('trubox')->getCurrentTruBoxId($customer_id);
        if($truBox_id == null)
            throw new Exception(
                Mage::helper('trubox')->__('TruBox does not exits !')
            );

        $payment = Mage::getModel('trubox/payment')->getCollection()
            ->addFieldToFilter('trubox_id', $truBox_id)
            ->getFirstItem()
        ;

        if(!$payment->getId() && !$is_no_need_payment)
            throw new Exception(
                Mage::helper('trubox')->__('%s don\'t have payment information !', $customer->getName())
            );

        return $payment;
    }

    public function createOrder($customer_id, $data_items)
    {

        Mage::helper('catalog/product')->setSkipSaleableCheck(true);
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $admin_session = Mage::getSingleton('adminhtml/session');
        $result = array();
        try{

            $admin_session->setIsOrderBackend(true);
            $admin_session->setOrderCustomerId($customer->getId());

            /* Check customer */
            $truBox_id = Mage::helper('trubox')->getCurrentTruBoxId($customer->getId());
            if($truBox_id == null)
                throw new Exception(
                    Mage::helper('trubox')->__('TruBox does not exits !')
                );
            /* End check customer */


            /* Check conditions include: billing, shipping and payment information before creating order */
            $this->checkConditionCustomer($customer_id);
            /* END Check conditions include: billing, shipping and payment information before creating order */

            $billing_trubox = $this->getAddressByTruBoxId($customer_id);

            $shipping_trubox = $this->getAddressByTruBoxId($customer_id, Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING);

            $prepare_data = $this->getProductParams($customer_id, $data_items);
            $products = $prepare_data['product'];
            if (sizeof($products) == 0)
                throw new Exception(
                    Mage::helper('trubox')->__('%s - No Items found!', $customer->getName())
                );


            $billingAddress = array(
                'prefix' => '',
                'firstname' => $billing_trubox->getFirstname(),
                'middlename' => '',
                'lastname' => $billing_trubox->getLastname(),
                'suffix' => '',
                'company' => '',
                'street' => $shipping_trubox->getStreet(),
                'city' => $billing_trubox->getCity(),
                'country_id' => $billing_trubox->getCountry(),
                'region' => $billing_trubox->getRegion(),
                'region_id' => $billing_trubox->getRegionId(),
                'postcode' => $billing_trubox->getZipcode(),
                'telephone' => $billing_trubox->getTelephone(),
                'fax' => '',
                'vat_id' => '',
                'save_in_address_book' => '0',
                'use_for_shipping' => '1',
            );

            $shippingAddress = array(
                'prefix' => '',
                'firstname' => $shipping_trubox->getFirstname(),
                'middlename' => '',
                'lastname' => $shipping_trubox->getLastname(),
                'suffix' => '',
                'company' => '',
                'street' => $shipping_trubox->getStreet(),
                'city' => $shipping_trubox->getCity(),
                'country_id' => $shipping_trubox->getCountry(),
                'region' => $shipping_trubox->getRegion(),
                'region_id' => $shipping_trubox->getRegionId(),
                'postcode' => $shipping_trubox->getZipcode(),
                'telephone' => $shipping_trubox->getTelephone(),
                'fax' => '',
                'vat_id' => '',
            );

            $quote = Mage::getModel('sales/quote')->setStoreId(1);

            /*Load Product and add to cart*/
            $before_grandTotal = 0;
            foreach ($products as $itemid => $pro){
                $item_price = Mage::helper('trubox/item')->getItemPrice(Mage::getModel('trubox/item')->load($itemid));
                $before_grandTotal += $item_price;
                foreach ($pro as $k => $v){
                    $product    = Mage::getModel('catalog/product')->load($k);
                    $quote->addProduct($product, new Varien_Object($v));
                }
            }

            $admin_session->setGrandTotalOrder($before_grandTotal);

            /*Add Billing Address*/
            $quote->getBillingAddress()
                ->addData($billingAddress);

            $_shipping = Mage::helper('trubox')->getShippingMethod();
            if($_shipping != null)
                $this->_shippingMethod = $_shipping;

            /*Add Shipping Address and set shipping method*/
            $quote->getShippingAddress()
                ->addData($shippingAddress)
                ->setCollectShippingRates(true)
                ->setShippingMethod($this->_shippingMethod)
                ->setPaymentMethod($this->_paymentMethod)
                ->collectTotals();

            /*Set Customer group As Guest*/
            $quote->setCustomer($customer);

            if ($quote->isVirtual()) {
                $quote->getBillingAddress()->setPaymentMethod($this->_paymentMethod);
            }

            $tax_amount = $quote->getShippingAddress()->getData('tax_amount');
            $is_no_need_payment = $this->checkApplyBalanceToPayment($customer, $before_grandTotal + $tax_amount);
            $payment_information = $this->getPaymentInformation($customer_id, $is_no_need_payment);

            $paymentData = array(
                'truwallet' => 'on',
                'method' => $this->_paymentMethod,
                'cc_type' => $payment_information->getCardType(),
                /*'cc_owner' => $payment_information->getNameOnCard(),
                'cc_number_enc' => Mage::getSingleton('payment/info')->encrypt($payment_information->getCardNumber()),*/
                'cc_number' => $payment_information->getCardNumber(),
                'cc_exp_month' => $payment_information->getMonthExpire(),
                'cc_exp_year' => $payment_information->getYearExpire(),
                'cc_cid' => $payment_information->getCvv(),
            );


            if($is_no_need_payment)
            {
                $paymentData = array(
                    'method' => $this->_freePaymentMethod,
                );

                $this->_paymentMethod = $this->_freePaymentMethod;
            }

            $quote->getPayment()->importData($paymentData);
            $quote->collectTotals();

            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            /* Fix bug remove items in carts after creating orders */
            $quote->setIsActive(false)->save();
            /* END Fix bug remove items in carts after creating orders */

            $increment_id = $service->getOrder()->getIncrementId();

            Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "1");


            $order_mail = new Mage_Sales_Model_Order();
            $order_mail->loadByIncrementId($increment_id);
            $order_mail->setCreatedBy(Magestore_TruBox_Model_Status::ORDER_CREATED_BY_ADMIN_YES)->save();
            $order_mail->sendNewOrderEmail();

            /* update table trubox order */
            $truBox_order = Mage::getModel('trubox/order');
            $truBox_order->setData('trubox_id', $truBox_id);
            $truBox_order->setData('order_id', $increment_id);
            $truBox_order->setData('created_time', now());
            $truBox_order->setData('updated_time', now());
            $truBox_order->save();
            /* END update table trubox order */

            $admin_session->unsIsOrderBackend();
            $admin_session->unsOrderCustomerId();
            $admin_session->unsGrandTotalOrder();

            $result[] = array(
                'ID' => $customer_id,
                'Email' => $customer->getEmail(),
                'Order_id' => $increment_id
            );

            if(sizeof($prepare_data['email']) > 0 && $order_mail->getId())
            {
                $this->sendEmailOutOfStock(Mage::getModel('customer/customer')->load($customer_id), $prepare_data['email']);
            }


        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(
                'Customer: '.$customer->getId().' - '.$customer->getEmail().' - '.Mage::helper('trubox')->__($ex->getMessage())
            );

            /*$result[] = array(
                'customer' => $customer_id,
                'order_increment_id' => $increment_id,
                'error' => 'Email: '.$customer->getEmail().' - '.Mage::helper('trubox')->__($ex->getMessage())
            );*/
        }

        return $result;
    }

    public function checkApplyBalanceToPayment($customer, $grandTotal)
    {

        $account = Mage::helper('truwallet/account')->loadByCustomerId($customer->getId());

        if($account->getId())
        {
            $total_discount = floatval($account->getTruwalletCredit());
            if(Mage::helper('trugiftcard')->isAppliedTGCToOrder($customer->getId()))
            {
                $account = Mage::helper('trugiftcard/account')->loadByCustomerId($customer->getId());
                $total_discount += $account->getTrugiftcardCredit();
            }

            if($total_discount >= floatval($grandTotal))
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

    public function getNextIncrementId(){
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $entityStoreTable = $resource->getTableName('eav_entity_store');
        $entityTypeTable = $resource->getTableName('eav_entity_type');

        $selectEntity = $readConnection->select()->from($entityTypeTable, "*")
            ->where("entity_type_code = 'order'");

        $entityTypeRow = $readConnection->fetchRow($selectEntity);

        if(isset($entityTypeRow['entity_type_id']) && $entityTypeRow['entity_type_id'] > 0){
            $orderEntityTypeId = $entityTypeRow['entity_type_id'];
            $entityStoreSelect = $readConnection->select()->from($entityStoreTable, "*")
                ->where("store_id = ? AND entity_type_id = $orderEntityTypeId", 1);

            $row = $readConnection->fetchRow($entityStoreSelect);

            $lastIncrementId = 0;
            if(isset($row['increment_last_id'])){
                $lastIncrementId = $row['increment_last_id'] + 1;
            }
            return $lastIncrementId;
        }

        return 0;
    }

    public function checkRegionId($country, $region_name, $region_id = 0)
    {
        if($region_id > 0  && !filter_var($region_id, FILTER_VALIDATE_INT) === false)
        {
            return $region_id;
        } else {
            $region = Mage::getModel('directory/region')->getCollection()
                ->addFieldToSelect('region_id')
                ->addFieldToFilter('country_id', $country)
                ->addFieldToFilter('default_name', $region_name)
                ->getFirstItem()
            ;

            if($region->getId())
                return $region->getId();
            else
                return null;
        }
    }

    /**
     * @param $customer
     * @param $products
     * @return $this
     */
    public function sendEmailOutOfStock($customer, $products)
    {
        $store = Mage::app()->getStore();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        if (!$customer->getId())
            return $this;

        $email_path =  Mage::getStoreConfig(self::XML_PATH_EMAIL_OUT_OF_STOCK, $store);

        $data = array(
            'store' => $store,
            'customer_name' => $customer->getName(),
            'items' => $products
        );

        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => Mage::app()->getStore()->getId()
            ))->sendTransactional(
                $email_path,
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store->getId()),
                $customer->getEmail(),
                $customer->getName(),
                $data
            );

        $translate->setTranslateInline(true);
        return $this;
    }

}
