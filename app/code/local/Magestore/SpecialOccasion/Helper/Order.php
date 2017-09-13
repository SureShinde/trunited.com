<?php

class Magestore_SpecialOccasion_Helper_Order extends Mage_Core_Helper_Abstract
{
    protected $_shippingMethod = 'freeshipping_freeshipping';
    protected $_paymentMethod = 'authnetcim';
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
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $this->_customer = $customer;
        }
        if (is_numeric($customer)) {
            $this->_customer = Mage::getModel('customer/customer')->load($customer);
        }
    }

    public function prepareOrder($data)
    {
        $flag = array();
        if (sizeof($data) > 0) {
            foreach ($data as $occasion_id => $itms) {
                $occasion = Mage::getModel('specialoccasion/specialoccasion')->load($occasion_id);
                if ($occasion != null && $occasion->getId()) {
                    if (is_array($itms) && sizeof($itms) > 0) {
                        foreach ($itms as $item_id) {
                            if ($rs = $this->createOrder($occasion->getCustomerId(), $item_id)) {
                                if (sizeof($rs) > 0)
                                    $flag[] = $rs;

                                sleep(Mage::helper('trubox')->getDelaySecond());
                            }
                        }
                    } else if (filter_var($itms, FILTER_VALIDATE_INT)) {
                        if ($rs = $this->createOrder($occasion->getCustomerId(), $itms)) {
                            if (sizeof($rs) > 0)
                                $flag[] = $rs;

                            sleep(Mage::helper('trubox')->getDelaySecond());
                        }
                    }
                }

            }
        }

        return $flag;
    }

    public function getAddressByOccasionId($customer_id, $item_id, $is_billing = false)
    {
        $specialoccasion_id = Mage::helper('specialoccasion')->getCurrentOccasionId($customer_id);
        if ($specialoccasion_id == null)
            return null;

        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if ($customer == null)
            return null;

        if ($is_billing)
            return $customer->getPrimaryBillingAddress();

        $address = Mage::getModel('specialoccasion/address')->getCollection()
            ->addFieldToFilter('specialoccasion_id', $specialoccasion_id)
            ->addFieldToFilter('item_id', $item_id)
            ->getFirstItem();

        if ($address->getId())
            return $address;
        else
            return null;
    }

    public function checkConditionCustomer($customer_id, $item_id)
    {
        $occasion_id = Mage::helper('specialoccasion')->getCurrentOccasionId($customer_id);
        if ($occasion_id == null)
            throw new Exception(
                Mage::helper('specialoccasion')->__('The Special Occasion does not exits !')
            );

        $address = Mage::getModel('specialoccasion/address')->getCollection()
            ->addFieldToSelect('address_id')
            ->addFieldToFilter('specialoccasion_id', $occasion_id)
            ->addFieldToFilter('item_id', $item_id);

        $customer = Mage::getModel('customer/customer')->load($customer_id);

        if ($customer->getPrimaryBillingAddress() === false) {
            throw new Exception(
                Mage::helper('specialoccasion')->__('%s don\'t have billing address information !', $customer->getName())
            );
        }

        if (sizeof($address) == 0 || $address == null) {
            throw new Exception(
                Mage::helper('specialoccasion')->__('%s don\'t have address information !', $customer->getName())
            );

        }
    }

    public function getProductParams($customer_id, $item_id)
    {
        $occasion_id = Mage::helper('specialoccasion')->getCurrentOccasionId($customer_id);
        if ($occasion_id == null)
            throw new Exception(
                Mage::helper('specialoccasion')->__('The Special Occasion does not exits !')
            );

        $data = array();
        $item = Mage::getModel('specialoccasion/item')->load($item_id);

        if ($item != null && $item->getId()) {

            $product = Mage::getModel('catalog/product')->load($item->getProductId());

            if ($product->getIsInStock() && $product->isSaleable() === true) {
                if ($item->getOptionParams() != null) {
                    $option_params = json_decode($item->getOptionParams(), true);
                    $data['product'][$item->getId()] = array(
                        $item->getProductId() => array(
                            'qty' => $item->getQty(),
                            'options' => $option_params,
                            '_processing_params' => array(),
                        )
                    );
                } else {
                    $data['product'][$item->getId()] = array(
                        $item->getProductId() => array(
                            'qty' => $item->getQty(),
                            '_processing_params' => array(),
                        )
                    );
                }
            }

        }

        return $data;
    }

    public function getPaymentInformation($customer_id, $is_no_need_payment)
    {
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $occasion_id = Mage::helper('specialoccasion')->getCurrentOccasionId($customer_id);
        if ($occasion_id == null)
            throw new Exception(
                Mage::helper('specialoccasion')->__('The Special Occasion does not exits !')
            );

        $card_collection = Mage::getModel('tokenbase/card')->getCollection()
            ->addFieldToFilter('active', 1)
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('method', 'authnetcim')
            ->addFieldToFilter('use_in_occasion', 1)
            ->setOrder('id', 'desc');

        if (sizeof($card_collection) == 0) {
            $card_collection = Mage::getModel('tokenbase/card')->getCollection()
                ->addFieldToFilter('active', 1)
                ->addFieldToFilter('customer_id', $customer_id)
                ->addFieldToFilter('method', 'authnetcim')
                ->setOrder('id', 'desc');
        }

        if (sizeof($card_collection) > 0)
            $card = $card_collection->getFirstItem();
        else
            $card = null;

        if (($card == null || !$card->getId()) && !$is_no_need_payment)
            throw new Exception(
                Mage::helper('specialoccasion')->__('%s don\'t have payment information !', $customer->getName())
            );

        if ($card != null && $card->getId() && $card->getData('use_in_occasion') == 0) {
            $card->setData('use_in_occasion', 1);
            $card->setData('updated_at', now());
            $card->save();
        }
        return $card;
    }

    public function createOrder($customer_id, $item_id)
    {

        Mage::helper('catalog/product')->setSkipSaleableCheck(true);
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $admin_session = Mage::getSingleton('adminhtml/session');
        $result = array();
        try {

            $admin_session->setIsOrderBackend(true);
            $admin_session->setOrderCustomerId($customer->getId());

            /* Check customer */
            $occasion_id = Mage::helper('specialoccasion')->getCurrentOccasionId($customer->getId());
            if ($occasion_id == null)
                throw new Exception(
                    Mage::helper('specialoccasion')->__('The Occasion does not exits!')
                );
            /* End check customer */

            $item = Mage::getModel('specialoccasion/item')->load($item_id);
            if($item == null)
                throw new Exception(
                    Mage::helper('specialoccasion')->__('The Occasion item does not exits!')
                );

            /* Check conditions include: billing, shipping and payment information before creating order */
            $this->checkConditionCustomer($customer_id, $item_id);
            /* END Check conditions include: billing, shipping and payment information before creating order */

            $billing_specialoccasion = $this->getAddressByOccasionId($customer_id, $item_id, true);

            $shipping_specialoccasion = $this->getAddressByOccasionId($customer_id, $item_id);

            $prepare_data = $this->getProductParams($customer_id, $item_id);

            $products = $prepare_data['product'];

            if (sizeof($products) == 0)
                throw new Exception(
                    Mage::helper('specialoccasion')->__('%s - No Items found!', $customer->getName())
                );


            $billingAddress = array(
                'prefix' => '',
                'firstname' => $billing_specialoccasion->getFirstname(),
                'middlename' => '',
                'lastname' => $billing_specialoccasion->getLastname(),
                'suffix' => '',
                'company' => '',
                'street' => $shipping_specialoccasion->getStreet(),
                'city' => $billing_specialoccasion->getCity(),
                'country_id' => $billing_specialoccasion->getCountry(),
                'region' => $billing_specialoccasion->getRegion(),
                'region_id' => $billing_specialoccasion->getRegionId(),
                'postcode' => $billing_specialoccasion->getPostcode(),
                'telephone' => $billing_specialoccasion->getTelephone(),
                'fax' => '',
                'vat_id' => '',
                'save_in_address_book' => '0',
                'use_for_shipping' => '1',
            );


            $shippingAddress = array(
                'prefix' => '',
                'firstname' => $shipping_specialoccasion->getFirstname(),
                'middlename' => '',
                'lastname' => $shipping_specialoccasion->getLastname(),
                'suffix' => '',
                'company' => '',
                'street' => $shipping_specialoccasion->getStreet(),
                'city' => $shipping_specialoccasion->getCity(),
                'country_id' => $shipping_specialoccasion->getCountry(),
                'region' => $shipping_specialoccasion->getRegion(),
                'region_id' => $shipping_specialoccasion->getRegionId(),
                'postcode' => $shipping_specialoccasion->getZipcode(),
                'telephone' => $shipping_specialoccasion->getTelephone(),
                'fax' => '',
                'vat_id' => '',
            );

            $quote = Mage::getModel('sales/quote')->setStoreId(1);

            /*Load Product and add to cart*/
            $before_grandTotal = 0;
            foreach ($products as $itemid => $pro) {
                $item_price = Mage::helper('specialoccasion')->getItemPrice(Mage::getModel('specialoccasion/item')->load($itemid));
                $before_grandTotal += $item_price;
                foreach ($pro as $k => $v) {
                    $product = Mage::getModel('catalog/product')->load($k);
                    $quote->addProduct($product, new Varien_Object($v));
                }
            }

            $admin_session->setGrandTotalOrder($before_grandTotal);

            /*Add Billing Address*/
            $quote->getBillingAddress()
                ->addData($billingAddress);

            $_shipping = Mage::helper('trubox')->getShippingMethod();
            if ($_shipping != null)
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
                'card_id' => $payment_information->getHash(),
                'cc_type' => '',
                'cc_number' => '',
                'cc_exp_month' => '',
                'cc_exp_year' => '',
                'cc_cid' => '',
                'save' => 0,
            );

            if ($is_no_need_payment) {
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

            $order_mail->addStatusHistoryComment(
                Mage::helper('specialoccasion')->__('The order was created automatically from the Special Occasion event. The event information as below: <br /><i>Occasion:</i> <b>%s</b><br /><i>Ship Date:</i> <b>%s</b><br/><i>Message:</i> <b>%s</b>', $item->getOccasion(), date('F m',strtotime($item->getShipDate())), $item->getMessage())
            );
            $order_mail->save();

            /** update table specialoccasion order & history **/
            $item->setStatus(Magestore_SpecialOccasion_Model_Status::STATUS_ITEM_COMPLETE);
            $item->setUpdatedAt(now());
            $item->save();

            $product_save = array();
            foreach ($products as $item_id => $p) {
                if($item != null && $item->getId()){
                    foreach ($p as $product_id => $_dt) {
                        $product = Mage::getModel('catalog/product')->load($product_id);
                        if($product != null && $product->getId()){
                            $product_save[] = array(
                                'product_id' => $product->getId(),
                                'product_name'  => $product->getName(),
                                'qty'   => $_dt['qty'],
                                'price'   => Mage::helper('core')->currency($product->getPrice(),true,false),
                            );
                        }
                    }
                }
            }

            $occasion_history = Mage::getModel('specialoccasion/history');
            $history_data = array(
                'customer_id' => $customer_id,
                'customer_name' => $customer->getName(),
                'customer_email' => $customer->getEmail(),
                'order_id' => $order_mail->getEntityId(),
                'order_increment_id' => $increment_id,
                'updated_at' => now(),
                'created_at' => now(),
                'points' => $order_mail->getRewardpointsEarn(),
                'cost' => $order_mail->getGrandTotal(),
                'products' => json_encode($product_save),
            );
            $occasion_history->setData($history_data);
            $occasion_history->save();
            /** END update table specialoccasion order **/

            $admin_session->unsIsOrderBackend();
            $admin_session->unsOrderCustomerId();
            $admin_session->unsGrandTotalOrder();

            $result[] = array(
                'ID' => $customer_id,
                'Email' => $customer->getEmail(),
                'Order_id' => $increment_id,
                'cost' => $order_mail->getGrandTotal(),
            );

            if (sizeof($prepare_data['email']) > 0 && $order_mail->getId()) {
                $this->sendEmailOutOfStock(Mage::getModel('customer/customer')->load($customer_id), $prepare_data['email']);
            }


        } catch (Exception $ex) {
            zend_debug::dump('Customer: ' . $customer->getId() . ' - ' . $customer->getEmail() . ' - ' . Mage::helper('specialoccasion')->__($ex->getMessage()));
            Mage::getSingleton('adminhtml/session')->addError(
                'Customer: ' . $customer->getId() . ' - ' . $customer->getEmail() . ' - ' . Mage::helper('specialoccasion')->__($ex->getMessage())
            );
        }

        return $result;
    }

    public function checkApplyBalanceToPayment($customer, $grandTotal)
    {

        $account = Mage::helper('truwallet/account')->loadByCustomerId($customer->getId());

        if ($account->getId()) {
            $total_discount = floatval($account->getTruwalletCredit());
            if (Mage::helper('trugiftcard')->isAppliedTGCToOrder($customer->getId())) {
                $account = Mage::helper('trugiftcard/account')->loadByCustomerId($customer->getId());
                $total_discount += $account->getTrugiftcardCredit();
            }

            if ($total_discount >= floatval($grandTotal))
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

    public function getNextIncrementId()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $entityStoreTable = $resource->getTableName('eav_entity_store');
        $entityTypeTable = $resource->getTableName('eav_entity_type');

        $selectEntity = $readConnection->select()->from($entityTypeTable, "*")
            ->where("entity_type_code = 'order'");

        $entityTypeRow = $readConnection->fetchRow($selectEntity);

        if (isset($entityTypeRow['entity_type_id']) && $entityTypeRow['entity_type_id'] > 0) {
            $orderEntityTypeId = $entityTypeRow['entity_type_id'];
            $entityStoreSelect = $readConnection->select()->from($entityStoreTable, "*")
                ->where("store_id = ? AND entity_type_id = $orderEntityTypeId", 1);

            $row = $readConnection->fetchRow($entityStoreSelect);

            $lastIncrementId = 0;
            if (isset($row['increment_last_id'])) {
                $lastIncrementId = $row['increment_last_id'] + 1;
            }
            return $lastIncrementId;
        }

        return 0;
    }

    public function checkRegionId($country, $region_name, $region_id = 0)
    {
        if ($region_id > 0 && !filter_var($region_id, FILTER_VALIDATE_INT) === false) {
            return $region_id;
        } else {
            $region = Mage::getModel('directory/region')->getCollection()
                ->addFieldToSelect('region_id')
                ->addFieldToFilter('country_id', $country)
                ->addFieldToFilter('default_name', $region_name)
                ->getFirstItem();

            if ($region->getId())
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

        $email_path = Mage::getStoreConfig(self::XML_PATH_EMAIL_OUT_OF_STOCK, $store);

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
