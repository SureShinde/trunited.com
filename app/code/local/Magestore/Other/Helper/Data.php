<?php

class Magestore_Other_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEnableOther()
    {
        return Mage::getStoreConfig('other/general/enable', Mage::app()->getStore());
    }

    public function enableDropShip()
    {
        return Mage::getStoreConfig('other/drop_ship/enable', Mage::app()->getStore());
    }

    public function skuDropShip()
    {
        return Mage::getStoreConfig('other/drop_ship/sku', Mage::app()->getStore());
    }

    public function enableRewardPointHeader()
    {
        return Mage::getStoreConfig('other/rewardpoint_header/enable', Mage::app()->getStore());
    }

    public function getRewardFontSizeHeader()
    {
        return Mage::getStoreConfig('other/rewardpoint_header/font_size', Mage::app()->getStore());
    }

    public function getRewardColorHeader()
    {
        return Mage::getStoreConfig('other/rewardpoint_header/text_color', Mage::app()->getStore());
    }

    public function getPrefixCustomer()
    {
        return Mage::getStoreConfig('other/customer_active/prefix', Mage::app()->getStore());
    }

    public function getErrorMessageCustomer()
    {
        return Mage::getStoreConfig('other/customer_active/error_message_login', Mage::app()->getStore());
    }

    public function getListDropShipSku()
    {
        $list = $this->skuDropShip();
        $result = array();
        if ($list != null) {
            $data = explode(',', $list);
            foreach ($data as $sku) {
                $result[] = trim(strtolower($sku));
            }
        }

        return $result;

    }

    public function isInDropShipList($product)
    {

        $product_exclusion = $this->getListDropShipSku();

        if (sizeof($product_exclusion) == 0)
            return false;
        else {
            if (in_array(strtolower(trim($product->getSku())), $product_exclusion))
                return true;
            else
                return false;
        }
    }

    public function dropShipInCart()
    {
        $cart = Mage::getModel('checkout/session')->getQuote();
        $dropShips = $this->getListDropShipSku();
        if($dropShips == null)
            return false;

        $items = $cart->getAllItems();
        if(sizeof($items) > 0){
            $is_normal = 0;
            $is_drop_ship = false;
            foreach ($cart->getAllItems() as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                if(in_array(strtolower($product->getSku()), $dropShips))
                {
                    $is_drop_ship = true;
                } else if(strcasecmp($item->getProduct()->getTypeId(),Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL) != 0)
                    $is_normal++;
            }

            return ($is_drop_ship && $is_normal == 0 && $this->enableDropShip());
        } else {
            return false;
        }
    }

    public function isLogged()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function ytd()
    {
        $dateStart = date('Y').'-01-01 00:00:00';
        $dateEnd = date('Y-m-d 23:59:59', strtotime("last day of -1 month"));

        /*$order_collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('status')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('rewardpoints_earn')
            ->addFieldToFilter('status', array(
                    'in' => array(
                                Mage_Sales_Model_Order::STATE_COMPLETE,
                                Mage_Sales_Model_Order::STATE_PROCESSING
                            ))
            )
            ->addFieldToFilter('created_at', array('from' => $dateStart, 'to' => $dateEnd))
        ;

        $order_collection ->getSelect()
            ->columns('SUM(rewardpoints_earn) as total')
        ;*/

        $transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('transaction_id')
            ->addFieldToSelect('status')
            ->addFieldToSelect('created_time')
            ->addFieldToFilter('status', Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED)
            ->addFieldToFilter('action_type', array('neq'=> Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_RESET_POINTS_BY_ADMIN))
            ->addFieldToFilter('action', array('neq'=> 'reset_point'))
            ->addFieldToFilter('created_time', array('from' => $dateStart, 'to' => $dateEnd));
        ;

        $transactions ->getSelect()
            ->columns('SUM(point_amount) as total')
        ;

        $firstItem = $transactions->getFirstItem();
        if($firstItem->getId())
            return $this->displayNumberFormat($firstItem->getTotal() > 0 ? $firstItem->getTotal() : 0);
        else
            return $this->displayNumberFormat(0);
    }

    public function mtd()
    {
        $dateStart = date('Y-m-01 00:00:00');
        $dateEnd = date('Y-m-d 23:59:59', time());

        /*$order_collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('status')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('rewardpoints_earn')
            ->addFieldToFilter('status', array(
                    'in' => array(
                        Mage_Sales_Model_Order::STATE_COMPLETE,
                        Mage_Sales_Model_Order::STATE_PROCESSING
                    ))
            )
            ->addFieldToFilter('created_at', array('from' => $dateStart, 'to' => $dateEnd))
        ;

        $order_collection ->getSelect()
            ->columns('SUM(rewardpoints_earn) as total')
        ;*/

        $transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('transaction_id')
            ->addFieldToSelect('status')
            ->addFieldToSelect('created_time')
            ->addFieldToFilter('status', Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED)
            ->addFieldToFilter('action_type', array('neq'=> Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_RESET_POINTS_BY_ADMIN))
            ->addFieldToFilter('action', array('neq'=> 'reset_point'))
            ->addFieldToFilter('created_time', array('from' => $dateStart, 'to' => $dateEnd));
        ;

        $transactions ->getSelect()
            ->columns('SUM(point_amount) as total')
        ;

        $firstItem = $transactions->getFirstItem();
        if($firstItem->getId())
            return $this->displayNumberFormat($firstItem->getTotal() > 0 ? $firstItem->getTotal() : 0);
        else
            return $this->displayNumberFormat(0);
    }

    public function displayNumberFormat($val)
    {
        if($val > 0)
        {
            $currency = Mage::helper('core')->currency($val, true, false);
            $dt = explode('.', $currency);
            return $dt[0];
        } else
            return 0;
    }
}