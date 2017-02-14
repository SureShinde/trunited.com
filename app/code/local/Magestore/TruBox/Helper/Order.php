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
        $flag = 0;
        if (sizeof($data) > 0) {
            foreach ($data as $trubox_id => $itms) {
                $trubox = Mage::getModel('trubox/trubox')->load($trubox_id);
                if ($trubox->getId()) {
                    if ($this->createOrder($trubox->getCustomerId(), $itms))
                        $flag++;
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

        $payment = Mage::getModel('trubox/payment')->getCollection()
                ->addFieldToFilter('trubox_id', $truBox_id)
                ->getFirstItem()
            ;

        if(!$payment->getId())
            throw new Exception(
                Mage::helper('trubox')->__('%s don\'t have payment information !', $customer->getName())
            );
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
                $inStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getIsInStock();

                if ($product->getIsInStock() && $product->isSaleable() === true) {

                    if($item->getOptionParams() != null){
                        $option_params = json_decode($item->getOptionParams(), true);
                        if($product->getTypeId() == 'configurable')
                        {
                            $data[$item->getId()] = array(
                                $item->getProductId() => array(
                                    'qty' => $item->getQty(),
                                    'super_attribute' => $option_params,
                                    '_processing_params' => array(),
                                )
                            );
                        } else {
                            $data[$item->getId()] = array(
                                $item->getProductId() => array(
                                    'qty' => $item->getQty(),
                                    'options' => $option_params,
                                    '_processing_params' => array(),
                                )
                            );
                        }
                    } else {
                        $data[$item->getId()] = array(
                            $item->getProductId() => array(
                                'qty' => $item->getQty(),
                                '_processing_params' => array(),
                            )
                        );
                    }
                }
            }
        }

        return $data;
    }

    public function getPaymentInformation($customer_id)
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

        if(!$payment->getId())
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

            $products = $this->getProductParams($customer_id, $data_items);
            if (sizeof($products) == 0)
                throw new Exception(
                    Mage::helper('trubox')->__('%s - No Items found!', $customer->getName())
                );

            $payment_information = $this->getPaymentInformation($customer_id);

            $paymentData = array(
                'truwallet' => 'on',
                'method' => $this->_paymentMethod,
                'cc_type' => $payment_information->getCardType(),
//                'cc_owner' => $payment_information->getNameOnCard(),
//                'cc_number_enc' => Mage::getSingleton('payment/info')->encrypt($payment_information->getCardNumber()),
                'cc_number' => $payment_information->getCardNumber(),
                'cc_exp_month' => $payment_information->getMonthExpire(),
                'cc_exp_year' => $payment_information->getYearExpire(),
                'cc_cid' => $payment_information->getCvv(),
//                'checks' => 179
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

            //Load Product and add to cart
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

            $is_no_need_payment = $this->checkApplyBalanceToPayment($customer, $before_grandTotal);
            if($is_no_need_payment)
            {
                $paymentData = array(
                    'method' => $this->_freePaymentMethod,
                );

                $this->_paymentMethod = $this->_freePaymentMethod;
            }


            // Add Billing Address
            $quote->getBillingAddress()
                ->addData($billingAddress);

            //Add Shipping Address and set shipping method
            $quote->getShippingAddress()
                ->addData($shippingAddress)
                ->setCollectShippingRates(true)
                ->setShippingMethod($this->_shippingMethod)
                ->setPaymentMethod($this->_paymentMethod)
                ->collectTotals();

            //Set Customer group As Guest
            $quote->setCustomer($customer);

            if ($quote->isVirtual()) {
                $quote->getBillingAddress()->setPaymentMethod($this->_paymentMethod);
            }

            $quote->getPayment()->importData($paymentData);
            $quote->collectTotals();
//            $quote->save();


            //Save Order With All details
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();

            $increment_id = $service->getOrder()->getIncrementId();

            Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "1");

            //Send Order Mail

            $order_mail = new Mage_Sales_Model_Order();
            $order_mail->loadByIncrementId($increment_id);
//            $order_mail->sendNewOrderEmail();

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

            return true;
        } catch (Exception $ex) {
//            Mage::log($ex->getMessage(), null, '1.log');
            Mage::getSingleton('adminhtml/session')->addError(
                'Email: '.$customer->getEmail().' - '.Mage::helper('trubox')->__($ex->getMessage())
            );

            return false;
        }
    }

    public function checkApplyBalanceToPayment($customer, $grandTotal)
    {

        $account = Mage::helper('truwallet/account')->loadByCustomerId($customer->getId());

        if($account->getId())
        {
            if(floatval($account->getTruwalletCredit()) >= floatval($grandTotal))
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

}
