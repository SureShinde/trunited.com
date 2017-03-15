<?php

class Magestore_Custompromotions_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigHelper()
    {
        return Mage::helper('custompromotions/configuration');
    }

    public function isRunPromotions()
    {
        if(!$this->getConfigHelper()->isEnable())
            return false;
        else {
            $start_date = strtotime($this->getConfigHelper()->getStartPromotion());
            $end_date = strtotime($this->getConfigHelper()->getEndPromotion());
            $now_time = Mage::getModel('core/date')->timestamp(time());
            if($start_date > $end_date)
                return false;
            else{
                if($now_time < $start_date || $now_time > $end_date)
                    return false;
                else
                    return true;
            }
        }
    }

    public function isEnableTruWalletProduct()
    {
        return $this->getConfigHelper()->isEnableTruWalletProduct();
    }

    public function compareTime($start_time, $end_time)
    {
        $diff = abs($end_time - $start_time);

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

        if ($years > 0 || $months > 0) {
            return false;
        } else {
            return $days;
        }
    }

    public function getAffiliateFromCustomer($customer_id)
    {
        $collection = Mage::getModel('affiliateplus/tracking')->getCollection()
                ->addFieldToFilter('customer_id',$customer_id)
                ->setOrder('tracking_id','desc')
                ->getFirstItem()
            ;

        if($collection->getId())
            return $collection;
        else
            return null;
    }

    public function addReward($customer)
    {
        try{
            $receiveObject = new Varien_Object();
            $receiveObject->setData('product_credit', abs($this->getConfigHelper()->getRewardsAmount()));
            $receiveObject->setData('point_amount', 0);
            $receiveObject->setData('customer_exist', true);
            Mage::helper('rewardpoints/action')
                ->addTransaction(
                    'promotion_register', $customer, $receiveObject
                );

            $model = Mage::getModel('custompromotions/custompromotions');
            $model->setData('customer_id',$customer->getId());
            $model->setData('register_amount',abs($this->getConfigHelper()->getRewardsAmount()));
            $model->setData('title',Magestore_Custompromotions_Model_Status::TITLE_PROMOTION_REGISTER);
            $model->setData('type',Magestore_Custompromotions_Model_Status::TYPE_PROMOTION_REGISTER);
            $model->setData('created_time',now());
            $model->setData('updated_time',now());
            $model->save();
        } catch (Exception $ex) {

        }
    }

    public function addRewardAffiliate($affiliate, $customer_id, $order_id)
    {
        $promo = Mage::getModel('custompromotions/custompromotions')->getCollection()
                ->addFieldToFilter('customer_id',$customer_id)
                ->setOrder('custompromotions_id','desc')
                ->getFirstItem()
            ;
		
        if($promo->getId() && $promo->getAffiliateId() == null)
        {
            try {
                // check current step of affiliate
                $dateStart = date('Y-m-d H:i:s', strtotime($this->getConfigHelper()->getStartPromotion()));
                $dateEnd = date('Y-m-d H:i:s', strtotime($this->getConfigHelper()->getEndPromotion()));
                $_collection = Mage::getModel('custompromotions/custompromotions')->getCollection()
                    ->addFieldToFilter('affiliate_id',$affiliate->getAccountId())
                    ->addFieldToFilter('created_time', array(
                        'from' => $dateStart,
                        'to' => $dateEnd,
                        'date' => true
                    ))
                    ;
                if(sizeof($_collection) < $this->getConfigHelper()->getMaxCustomers()){
                    $affiliate_account = Mage::getModel('affiliateplus/account')->load($affiliate->getAccountId());
                    $customer = Mage::getModel('customer/customer')->load($affiliate_account->getCustomerId());

                    $receiveObject = new Varien_Object();
                    $receiveObject->setData('product_credit', abs($this->getConfigHelper()->getRewardsAmount()));
                    $receiveObject->setData('point_amount', 0);
                    $receiveObject->setData('customer_exist', true);
                    Mage::helper('rewardpoints/action')
                        ->addTransaction(
                            'promotion_order', $customer, $receiveObject
                        );

                    $promo->setData('affiliate_id',$affiliate->getAccountId());
                    $promo->setData('referred_amount',abs($this->getConfigHelper()->getRewardsAmount()));
                    $promo->setData('current_step',$promo->getCurrentStep() + 1);
                    $promo->setData('order_id',$order_id);
                    $promo->setData('updated_time',now());
                    $promo->save();
                }
            } catch (Exception $ex) {

            }

        }
    }

    public function addTruWalletFromProduct($order)
    {
        if(!$this->isEnableTruWalletProduct())
            return $this;

        $order_status_configure = $this->getConfigHelper()->getTruWalletOrderStatus();
        $product_configure = $this->getConfigHelper()->getTruWalletSku();
        $value_configure = $this->getConfigHelper()->getTruWalletValue();

        if($order_status_configure == '')
            return $this;

        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $flag = $this->checkAddedTransaction($order->getIncrementId(), $customer->getId());

        if(strcasecmp($order->getStatus(),$order_status_configure) == 0 && !$flag){
            $items = $order->getAllItems();
            try{
                foreach($items as $orderItem) {
                    if(strcasecmp($orderItem->getSku(),$product_configure) == 0)
                    {
                        $credit = $value_configure * (int)$orderItem->getQtyOrdered();
                        $receiveObject = new Varien_Object();
                        $receiveObject->setData('product_credit', abs($credit));
                        $receiveObject->setData('point_amount', 0);
                        $receiveObject->setData('customer_exist', true);
                        $receiveObject->setData('order_id', $order->getIncrementId());

                        Mage::helper('rewardpoints/action')
                            ->addTransaction(
                                'purchase_truWallet_giftcard', $customer, $receiveObject
                            );
                    }
                }
            } catch (Exception $ex) {}
        }
    }

    public function checkAddedTransaction($order_id, $customer_id)
    {
        $collection = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->addFieldToFilter('action','purchase_truWallet_giftcard')
            ->addFieldToFilter('title','Purchased truWallet Gift Card on order '.$order_id)
            ->setOrder('transaction_id','desc')
            ->getFirstItem()
        ;

        if($collection->getId())
            return true;
        else
            return false;
    }

    public function truWalletInCart()
    {
        $cart = Mage::getModel('checkout/session')->getQuote();
        $items = $cart->getAllItems();
        if(sizeof($items) > 0){
            $sku_truWallet = $this->getConfigHelper()->getTruWalletSku();
            $flag = false;
            foreach ($cart->getAllItems() as $item) {
                $sku = $item->getProduct()->getSku();
                if(strcasecmp($sku_truWallet, $sku) == 0){
                    $flag = true;
                    break;
                }
            }

            return $flag;
        } else {
            return false;
        }
    }
}