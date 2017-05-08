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

        $transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('transaction_id')
            ->addFieldToSelect('status')
            ->addFieldToSelect('created_time')
            ->addFieldToFilter('status', Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED)
            ->addFieldToFilter('created_time', array('from' => $dateStart, 'to' => $dateEnd));
        ;

        $transactions ->getSelect()
            ->columns('SUM(point_amount) as total')
        ;

        $firstItem = $transactions->getFirstItem();
        if($firstItem->getId())
            return Mage::helper('core')->currency($firstItem->getTotal(), true, false);
        else
            return Mage::helper('core')->currency(0, true, false);
    }

    public function mtd()
    {
        $dateStart = date('Y-m-01 00:00:00');
        $dateEnd = date('Y-m-d 23:59:59', time());

        $transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('transaction_id')
            ->addFieldToSelect('status')
            ->addFieldToSelect('created_time')
            ->addFieldToFilter('status', Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED)
            ->addFieldToFilter('created_time', array('from' => $dateStart, 'to' => $dateEnd));
        ;

        $transactions ->getSelect()
            ->columns('SUM(point_amount) as total')
        ;

        $firstItem = $transactions->getFirstItem();
        if($firstItem->getId())
            return Mage::helper('core')->currency($firstItem->getTotal(), true, false);
        else
            return Mage::helper('core')->currency(0, true, false);
    }
}