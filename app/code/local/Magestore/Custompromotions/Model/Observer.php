<?php
require_once   Mage::getBaseDir('lib').'/Twilio/autoload.php';
use Twilio\Rest\Client;

class Magestore_Custompromotions_Model_Observer
{
    public function customerRegisterSuccess($observer)
    {
        $customer_reg = $observer->getCustomer();
        $core_session = Mage::getSingleton('core/session');
        $verify_helper = Mage::helper('custompromotions/verify');
        /* Save customer phone number */
        $is_verified = $core_session->getVerify();
        $code = $core_session->getCodeActive();

        if($is_verified != null && $is_verified){
            $phone = $core_session->getPhoneActive();
            $_phone = $verify_helper->formatPhoneToDatabase($phone);
            $customer_reg->setPhoneNumber($_phone);
            $customer_reg->save();

            $mobile = $verify_helper->getVerifyByPhoneCode($_phone, $code);
            if($mobile != null && $customer_reg->getId() > 0)
            {
                $mobile->setCustomerId($customer_reg->getId());
                $mobile->setUpdatedTime(now());
                $mobile->save();

                /** Send sms to affiliate mobile  **/
                $data = Mage::app()->getRequest()->getParams();
                if($data['affiliate_id'] != null)
                {
                    $affiliate = Mage::getModel('affiliateplus/account')->load($data['affiliate_id']);
                    if($affiliate->getId())
                    {
                        $affiliate_customer = Mage::getModel('customer/customer')->load($affiliate->getCustomerId());
                        if($affiliate_customer->getId())
                        {
                            $sid = $verify_helper->getAccountSID();
                            $token = $verify_helper->getAuthToken();
                            $from = $verify_helper->getSenderNumber();
                            $mobile_prefix = $verify_helper->getMobileCode();
                            $phone = $verify_helper->getPhoneNumberFormat($mobile_prefix, $affiliate_customer->getPhoneNumber());
                            $message = Mage::helper('custompromotions')->__('Congratulations! %s %s just completed registration as your new connection on Trunited.com.', $customer_reg->getFirstname(), $customer_reg->getLastname());

                            try{
                                $client = new Client($sid, $token);
                                $client->messages->create(
                                    $phone,
                                    array(
                                        /*'from' => $from,*/
                                        'messagingServiceSid' => "MGb9626abfac0e54ccc6b424dcd3dc325d",
                                        'body' => $message
                                    )
                                );
                            } catch (Exception $ex){

                            }

                        }

                    }
                }
                /** END send sms to affiliate mobile  **/
            }
            $core_session->unsPhoneActive();
            $core_session->unsCodeActive();
            $core_session->unsVerify();
        }
        /* End save customer phone number */

        if (!Mage::helper('custompromotions')->isRunPromotions())
            return $this;

        $customer = Mage::getModel('customer/customer')->load($customer_reg->getId());
        if (!$customer->getId())
            return $this;

        Mage::helper('custompromotions')->addReward($customer);

        return $this;
    }

    public function salesOrderPlaceAfter($observer)
    {
        $order = $observer->getOrder();

        if (Mage::helper('custompromotions')->isRunPromotions()) {
            $customer_id = $order->getCustomerId();
            $affiliate = Mage::helper('custompromotions')->getAffiliateFromCustomer($customer_id);
            if ($affiliate != null && $affiliate->getId()) {
                Mage::helper('custompromotions')->addRewardAffiliate($affiliate, $customer_id, $order->getIncrementId());
            }
        }

        $quoteId = $order->getQuoteId();
        $quote = Mage::getModel('sales/quote')->load($quoteId);
        if($quote->getData('checkout_method') == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER){
            $no_refers_me = Mage::getSingleton('core/session')->getWRMNo();
            $affiliateId = Mage::getSingleton('core/session')->getWRMId();
            $affiliateName = Mage::getSingleton('core/session')->getWRMName();
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

            if(isset($no_refers_me) && $no_refers_me == 0){
                if (!$affiliateId) {
                    $affiliateId = Mage::getModel('affiliateplus/account')->getCollection()
                        ->addFieldToFilter('name', $affiliateName)
                        ->getFirstItem()->getAccountId();
                }

                if (isset($affiliateId) && $affiliateId) {
                    $collectionTracking = Mage::getModel('affiliateplus/tracking')->getCollection()
                        ->addFieldToFilter('customer_id', $customer->getId())
                        ->getFirstItem();
                    if (!$collectionTracking->getId()) {
                        $collectionTracking = Mage::getModel('affiliateplus/tracking');
                        $collectionTracking->setAccountId($affiliateId);
                        $collectionTracking->setCustomerId($customer->getId());
                        $collectionTracking->setCustomerEmail($customer->getEmail());
                        $collectionTracking->setCreatedTime(now());
                        try {
                            $collectionTracking->save();
                        } catch (Exception $e) {
                            Mage::log($e->getMessage(), null, 'affiliate.log');
                        }
                    }
                }
            }

            $customer->setData('referred_affiliate_name',$affiliateName);
            $customer->setData('no_refers_me',$no_refers_me);
            $customer->save();
            Mage::getSingleton('core/session')->unsWRMNo();
            Mage::getSingleton('core/session')->unsWRMId();
            Mage::getSingleton('core/session')->unsWRMName();
        }
    }

    public function salesOrderSaveAfter($observer)
    {
        $order = $observer['order'];
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        // Add earning point for customer
        /*$truWallet_order_status = Mage::getStoreConfig('custompromotions/product/order_status', Mage::app()->getStore()->getId());

        if ($order->getState() == $truWallet_order_status || $order->getStatus() == $truWallet_order_status) {
            Mage::helper('custompromotions')->addTruWalletFromProduct($order);
        }*/

        /* Process partially shipment status when order has created shipment */
        $shipstation_enable = Mage::helper('custompromotions/configuration')->getShipStationEnable();
        if($shipstation_enable){
            $is_partially = Mage::getSingleton('core/session')->getOrderShipmentStatus();
            $total_qty_ordered = floor($order->getData('total_qty_ordered'));
            if(isset($is_partially) && $is_partially != null)
            {
                if($order->hasShipments()){
                    $shipmentCollection = $order->getShipmentsCollection();
                    $shipment_qty = 0;
                    foreach ($shipmentCollection as $ship) {
                        $shipment_qty += $ship->getTotalQty();
                    }

                    $new_status = Mage::helper('custompromotions/configuration')->getShipStationOrderStatus();
                    if($new_status == '')
                        $new_status = Mage_Sales_Model_Order::STATE_PROCESSING;

                    if($total_qty_ordered > $shipment_qty){
                        $order->setStatus($new_status);
                    } else if($total_qty_ordered == $shipment_qty){
                        $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
                    }

                    Mage::getSingleton('core/session')->unsOrderShipmentStatus();
                    $order->save();
                }
            }
        }/* END Process partially shipment status when order has created shipment */

    }

    public function salesOrderShipmentSaveAfter($observer)
    {
        if (Mage::registry('salesOrderShipmentSaveAfterTriggered')) {
            return $this;
        }

        /* @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = Mage::getModel('sales/order')->load($shipment->getOrderId());

        if ($shipment) {
            if($order->hasInvoices()){
                $total_qty_ordered = floor($order->getData('total_qty_ordered'));
                $invoiceCollection = $order->getInvoiceCollection();
                $invoice_qty = 0;
                foreach ($invoiceCollection as $inv) {
                    $invoice_qty += $inv->getTotalQty();
                }

                if($total_qty_ordered > $invoice_qty){
                    Mage::getSingleton('core/session')->setOrderShipmentStatus(1);
                } else {
                    Mage::getSingleton('core/session')->unsOrderShipmentStatus();
                }
            } else {
                Mage::getSingleton('core/session')->setOrderShipmentStatus(1);
            }
        }
        return $this;
    }

    public function customerPrepareSave($observer)
    {
        $customer = $observer->getCustomer();
        $request = $observer->getRequest();
        $data = $request->getParams();
        if($data['account']['phone_number'] != '')
            $data['account']['phone_number'] = Mage::helper('custompromotions/verify')->formatPhoneToDatabase($data['account']['phone_number']);

        if($data['account']['alternate_number'] != '')
            $data['account']['alternate_number'] = Mage::helper('custompromotions/verify')->formatPhoneToDatabase($data['account']['alternate_number']);

        if($data['account']['phone_number'][0] == 1 || $data['account']['alternate_number'][0] == 1 ||
            sizeof($data['account']['phone_number']) > 10 ||  sizeof($data['account']['alternate_number']) > 10)
        {
            Mage::throwException(
                Mage::helper('custompromotions')->__('The mobile number only has 10 digits and don\'t allow begin with 1')
            );
            return;
        }

        $_customer = Mage::getModel('customer/customer')->load($customer->getId());
        $old_phone_number = $_customer->getPhoneNumber();

        if(strcasecmp($data['account']['phone_number'], $old_phone_number) != 0){
            Mage::getSingleton('adminhtml/session')->setIsUpdatePhone(1);
        }

        $customer->setData('phone_number', $data['account']['phone_number']);
        $customer->setData('alternate_number', $data['account']['alternate_number']);

    }

    public function customerSaveAfter($observer)
    {
        $is_update = Mage::getSingleton('adminhtml/session')->getIsUpdatePhone();
        $customer = $observer->getCustomer();
        if($is_update != null){
            $verify_mobile = Mage::getModel('custompromotions/verifymobile')->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId())
                ->getFirstItem()
            ;

            if($verify_mobile != null && $verify_mobile->getId()){
                $verify_mobile->setData('phone', $customer->getPhoneNumber());
                $verify_mobile->setData('updated_time', now());
                try{
                    $verify_mobile->save();
                } catch (Exception $ex) {

                }
                Mage::getSingleton('core/session')->unsIsUpdatePhone();
            }
        }
    }

}
