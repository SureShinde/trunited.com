<?php
class Magestore_Onestepcheckout_Model_Sales_Quote_Address_Total_Giftwrap extends Mage_Sales_Model_Quote_Address_Total_Abstract {
	public function collect(Mage_Sales_Model_Quote_Address $address) {

		if($address->getQuote()->isVirtual())
			return $this;
		
		$_helper = Mage::helper('onestepcheckout');
		$active = $_helper->enableGiftWrap();
		if (!$active)
			return;
		
		$items = $address->getAllItems();
		if (!count($items)) {
			return $this;
		}
		$session = Mage::getSingleton('checkout/session');
		$deliveryType = $session->getData('delivery_type');
		if($deliveryType == null)
			return $this;
		
		$giftwrapAmount = $_helper->getGiftwrapAmount();
		if($deliveryType == 2)
			$wrapTotal = $giftwrapAmount;
		else
			$wrapTotal = 0;
		
		$session->setData('onestepcheckout_giftwrap_amount', $wrapTotal);
		
		$address->setOnestepcheckoutGiftwrapAmount($wrapTotal);		
		$address->setGrandTotal($address->getGrandTotal() + $address->getOnestepcheckoutGiftwrapAmount());
		$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getOnestepcheckoutGiftwrapAmount());
		return $this;
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address) 
	{
		if($address->getQuote()->isVirtual())
			return $this;
		
		$_helper = Mage::helper('onestepcheckout');
		$active = $_helper->enableGiftWrap();
		if (!$active)
			return;
		$session = Mage::getSingleton('checkout/session');
		$deliveryType = $session->getData('delivery_type');
		if($deliveryType == null)
			return $this;
		
		$amount = $address->getOnestepcheckoutGiftwrapAmount();
		if($amount)
			$title = Mage::helper('sales')->__('Get It Now');
		else
			$title = Mage::helper('sales')->__('Ship With My TruBox');
		
		$address->addTotal(array(
				'code'=>$this->getCode(),
				'title'=>$title,
				'value'=>$amount
		));
		return $this;
	}
}
