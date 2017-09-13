<?php

class Magestore_Nationpassport_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getNationPassportLabel()
    {
        return $this->__('Trunited Nation Passport');
    }

    public function getAffiliateRefer($customer_id)
    {
        $tracking = Mage::getModel('affiliateplus/tracking')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->setOrder('tracking_id','desc')
            ->getFirstItem()
        ;

        if($tracking->getId()){
            $affiliate = Mage::getModel('affiliateplus/account')->load($tracking->getAccountId());
            if($affiliate->getId())
                return $affiliate;
            else
                return null;
        } else {
            return null;
        }
    }

    public function getTruGiftCardProduct()
    {
        $sku = Mage::helper('trugiftcard')->getTruGiftCardSku();
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);

        if($product != null && $product->getId())
            return $product;
        else
            return null;
    }

    public function getAffiliatesFromCustomer($customer_id)
    {
        $collection = Mage::getModel('affiliateplus/tracking')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->setOrder('tracking_id','desc')
        ;

        if(sizeof($collection) > 0)
            return $collection;
        else
            return null;
    }

}
