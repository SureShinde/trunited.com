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
    protected $_paymentMethod = 'ccsave';

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
                    Mage::helper('trubox')->__('Customer don\'t have address information !')
                );

        } else {
            throw new Exception(
                Mage::helper('trubox')->__('Customer don\'t have address information !')
            );
        }

        $payment = Mage::getModel('trubox/payment')->getCollection()
                ->addFieldToFilter('trubox_id', $truBox_id)
                ->getFirstItem()
            ;

        if(!$payment->getId())
            throw new Exception(
                Mage::helper('trubox')->__('Customer don\'t have payment information !')
            );
    }

    public function getProductParams($customer_id)
    {
        $truBox_id = Mage::helper('trubox')->getCurrentTruBoxId($customer_id);
        if($truBox_id == null)
            throw new Exception(
                Mage::helper('trubox')->__('TruBox does not exits !')
            );

        $data = array();
        $items = Mage::getModel('trubox/item')->getCollection()
            -> addFieldToFilter('trubox_id', $truBox_id)
            ;

        if(sizeof($items) > 0)
        {
            foreach ($items as $item)
            {
                if($item->getOptionParams() != null){
                    $option_params = json_decode($item->getOptionParams(), true);
                    $product = Mage::getModel('catalog/product')->load($item->getProductId());
                    if($product->getTypeId() == 'configurable')
                    {
                        $data[] = array(
                            'product' => $item->getProductId(),
                            'qty' => $item->getQty(),
                            'super_attribute' => $option_params
                        );
                    } else {
                        $data[] = array(
                            'product' => $item->getProductId(),
                            'qty' => $item->getQty(),
                            'options' => $option_params
                        );
                    }
                } else {
                    $data[] = array(
                        'product' => $item->getProductId(),
                        'qty' => $item->getQty(),
                    );
                }
            }
        }

        return $data;
    }

    public function getPaymentInformation($customer_id)
    {
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
                Mage::helper('trubox')->__('Customer don\'t have payment information !')
            );

        return $payment;
    }

    public function createOrder($customer_id)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try{
            $connection->beginTransaction();

            /* Check customer */
            $this->setCustomer($customer_id);
            if(!$this->_customer->getId())
                throw new Exception(
                    Mage::helper('trubox')->__('Customer does not exist')
                );

            $truBox_id = Mage::helper('trubox')->getCurrentTruBoxId($customer_id);
            if($truBox_id == null)
                throw new Exception(
                    Mage::helper('trubox')->__('TruBox does not exits !')
                );
            /* End check customer */

            /* Check conditions include: billing, shipping and payment information before creating order */
            $this->checkConditionCustomer($customer_id);
            /* END Check conditions include: billing, shipping and payment information before creating order */

            $products = $this->getProductParams($customer_id);
            if(sizeof($products) == 0)
                throw new Exception(
                    Mage::helper('trubox')->__('No Items found !')
                );

            /* Prepare data for order */
            $transaction = Mage::getModel('core/resource_transaction');

            $this->_storeId = $this->_customer->getStoreId();
            $reservedOrderId = Mage::getSingleton('eav/config')
                ->getEntityType('order')
                ->fetchNewIncrementId($this->_storeId);

            $currencyCode  = Mage::app()->getBaseCurrencyCode();
            $this->_order = Mage::getModel('sales/order')
                ->setIncrementId($reservedOrderId)
                ->setStoreId($this->_storeId)
                ->setQuoteId(0)
                ->setGlobalCurrencyCode($currencyCode)
                ->setBaseCurrencyCode($currencyCode)
                ->setStoreCurrencyCode($currencyCode)
                ->setOrderCurrencyCode($currencyCode)
            ;
            /* End Prepare data for order */

            /* Assign customer to order */
            $this->_order->setCustomerEmail($this->_customer->getEmail())
                ->setCustomerFirstname($this->_customer->getFirstname())
                ->setCustomerLastname($this->_customer->getLastname())
                ->setCustomerGroupId($this->_customer->getGroupId())
                ->setCustomerIsGuest(0)
                ->setCustomer($this->_customer);
            /* End Assign customer to order */

            $billing = $this->_customer->getDefaultBillingAddress();
            $billing_trubox = $this->getAddressByTruBoxId($customer_id);
            $billingAddress = Mage::getModel('sales/order_address')
                ->setStoreId($this->_storeId)
                ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
                ->setCustomerId($this->_customer->getId())
                ->setCustomerAddressId($this->_customer->getDefaultBilling())
                ->setCustomerAddress_id($billing->getEntityId())
                ->setPrefix('')
                ->setFirstname($billing_trubox->getFirstname())
                ->setMiddlename('')
                ->setLastname($billing_trubox->getLastname())
                ->setSuffix(''  )
                ->setCompany('')
                ->setStreet($billing_trubox->getStreet())
                ->setCity($billing_trubox->getCity())
                ->setCountry_id($billing_trubox->getCountry())
                ->setRegion($billing_trubox->getRegion())
                ->setRegion_id($billing_trubox->getRegionId())
                ->setPostcode($billing_trubox->getZipcode())
                ->setTelephone($billing_trubox->getTelephone())
                ->setFax('')
                ->setVatId($billing->getVatId());
            $this->_order->setBillingAddress($billingAddress);

            $shipping = $this->_customer->getDefaultShippingAddress();
            $shipping_trubox = $this->getAddressByTruBoxId($customer_id, Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING);
            $shippingAddress = Mage::getModel('sales/order_address')
                ->setStoreId($this->_storeId)
                ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
                ->setCustomerId($this->_customer->getId())
                ->setCustomerAddressId($this->_customer->getDefaultShipping())
                ->setCustomer_address_id($shipping->getEntityId())
                ->setPrefix('')
                ->setFirstname($shipping_trubox->getFirstname())
                ->setMiddlename('')
                ->setLastname($shipping_trubox->getLastname())
                ->setSuffix('')
                ->setCompany('')
                ->setStreet($shipping_trubox->getStreet())
                ->setCity($shipping_trubox->getCity())
                ->setCountry_id($shipping_trubox->getCountry())
                ->setRegion($shipping_trubox->getRegion())
                ->setRegion_id($shipping_trubox->getRegionId())
                ->setPostcode($shipping_trubox->getZipcode())
                ->setTelephone($shipping_trubox->getTelephone())
                ->setFax('')
                ->setVatId($shipping->getVatId());

            /* Set shipping method to order */
            $this->_order->setShippingAddress($shippingAddress)
                ->setShippingMethod($this->_shippingMethod);
            /* End Set shipping method to order */

            /* Save payment information */
            $payment_information = $this->getPaymentInformation($customer_id);
            $orderPayment = Mage::getModel('sales/order_payment')
                ->setStoreId($this->_storeId)
                ->setCustomerPaymentId(0)
                ->setMethod($this->_paymentMethod)
                ->setCcNumber($payment_information->getCardNumber())
                ->setCcNumberEnc(Mage::getSingleton('payment/info')->encrypt($payment_information->getCardNumber()))
                ->setCcOwner($payment_information->getNameOnCard())
                ->setCcType($payment_information->getCardType())
                ->setCcExpMonth($payment_information->getMonthExpire())
                ->setCcExpYear($payment_information->getYearExpire())
                ->setCcCid($payment_information->getCvv())
            ;

            $this->_order->setPayment($orderPayment);
            /* End Save payment information */

            /* Add products to order */
            $this->_addProducts($products);
            /* End Add products to order */

            /* Save order */
            $this->_order->setSubtotal($this->_subTotal)
                ->setBaseSubtotal($this->_subTotal)
                ->setGrandTotal($this->_subTotal)
                ->setBaseGrandTotal($this->_subTotal)
            ;

            $transaction->addObject($this->_order);
            $transaction->addCommitCallback(array($this->_order, 'place'));
            $transaction->addCommitCallback(array($this->_order, 'save'));
            $transaction->save();
            /* End save order */

            /* update table trubox order */
            $truBox_order = Mage::getModel('trubox/order');
            $truBox_order->setData('trubox_id', $truBox_id);
            $truBox_order->setData('order_id', $this->_order->getId());
            $truBox_order->setData('created_time', now());
            $truBox_order->setData('updated_time', now());
            $truBox_order->save();
            /* END update table trubox order */

            $connection->commit();
        } catch (Exception $ex) {
            $connection->rollback();
        }

        return $this->_order;
    }

    protected function _addProducts($products)
    {
        $this->_subTotal = 0;

        foreach ($products as $productRequest) {
            if ($productRequest['product'] == 'rand') {

                $productsCollection = Mage::getResourceModel('catalog/product_collection');

                $productsCollection->addFieldToFilter('type_id', 'simple');
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productsCollection);

                $productsCollection->getSelect()
                    ->order('RAND()')
                    ->limit(rand($productRequest['min'], $productRequest['max']));

                foreach ($productsCollection as $product){
                    $this->_addProduct(array(
                        'product' => $product->getId(),
                        'qty' => rand(1, 2)
                    ));
                }
            } else {
                $this->_addProduct($productRequest);
            }
        }
    }

    protected function _addProduct($requestData)
    {
        $request = new Varien_Object();
        $request->setData($requestData);

        $product = Mage::getModel('catalog/product')->load($request['product']);

        $cartCandidates = $product->getTypeInstance(true)
            ->prepareForCartAdvanced($request, $product);

        if (is_string($cartCandidates)) {
            throw new Exception($cartCandidates);
        }

        if (!is_array($cartCandidates)) {
            $cartCandidates = array($cartCandidates);
        }

        $parentItem = null;
        $errors = array();
        $items = array();
        foreach ($cartCandidates as $candidate) {
            $item = $this->_productToOrderItem($candidate, $candidate->getCartQty());

            $items[] = $item;

            /**
             * As parent item we should always use the item of first added product
             */
            if (!$parentItem) {
                $parentItem = $item;
            }
            if ($parentItem && $candidate->getParentProductId()) {
                $item->setParentItem($parentItem);
            }
            /**
             * We specify qty after we know about parent (for stock)
             */
            $item->setQty($item->getQty() + $candidate->getCartQty());

            // collect errors instead of throwing first one
            if ($item->getHasError()) {
                $message = $item->getMessage();
                if (!in_array($message, $errors)) { // filter duplicate messages
                    $errors[] = $message;
                }
            }
        }
        if (!empty($errors)) {
            Mage::throwException(implode("\n", $errors));
        }

        foreach ($items as $item){
            $this->_order->addItem($item);
        }

        return $items;
    }

    function _productToOrderItem(Mage_Catalog_Model_Product $product, $qty = 1)
    {
        $rowTotal = $product->getFinalPrice() * $qty;

        $options = $product->getCustomOptions();

        $optionsByCode = array();

        foreach ($options as $option)
        {
            $quoteOption = Mage::getModel('sales/quote_item_option')->setData($option->getData())
                ->setProduct($option->getProduct());

            $optionsByCode[$quoteOption->getCode()] = $quoteOption;
        }

        $product->setCustomOptions($optionsByCode);

        $options = $product->getTypeInstance(true)->getOrderOptions($product);

        $orderItem = Mage::getModel('sales/order_item')
            ->setStoreId($this->_storeId)
            ->setQuoteItemId(0)
            ->setQuoteParentItemId(NULL)
            ->setProductId($product->getId())
            ->setProductType($product->getTypeId())
            ->setQtyBackordered(NULL)
            ->setTotalQtyOrdered($product['rqty'])
            ->setQtyOrdered($product['qty'])
            ->setName($product->getName())
            ->setSku($product->getSku())
            ->setPrice($product->getFinalPrice())
            ->setBasePrice($product->getFinalPrice())
            ->setOriginalPrice($product->getFinalPrice())
            ->setRowTotal($rowTotal)
            ->setBaseRowTotal($rowTotal)

            ->setWeeeTaxApplied(serialize(array()))
            ->setBaseWeeeTaxDisposition(0)
            ->setWeeeTaxDisposition(0)
            ->setBaseWeeeTaxRowDisposition(0)
            ->setWeeeTaxRowDisposition(0)
            ->setBaseWeeeTaxAppliedAmount(0)
            ->setBaseWeeeTaxAppliedRowAmount(0)
            ->setWeeeTaxAppliedAmount(0)
            ->setWeeeTaxAppliedRowAmount(0)

            ->setProductOptions($options);

        $this->_subTotal += $rowTotal;

        return $orderItem;
    }
}
