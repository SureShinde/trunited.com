<?php
class Magestore_Onestepcheckout_Block_Sales_Order_Totals_Giftwrap extends Mage_Sales_Block_Order_Totals
{
	public function initTotals()
    {
		if($this->giftwrapAmount() == null)
			return $this;
		
		$total = new Varien_Object();
		$total->setCode('giftwrap');
		$total->setValue($this->giftwrapAmount());
		$total->setBaseValue($this->baseGiftwrapAmout());
		if($this->giftwrapAmount() > 0){
			$total->setLabel('Standard Shipping');
		}else{
			$total->setLabel('Ship With My TruBox');
		}
		$parent = $this->getParentBlock();
		$parent->addTotal($total,'subtotal');
	}
	
	public function giftwrapAmount() {
		$order = $this->getParentBlock()->getOrder();
		$giftwrapAmount = $order->getOnestepcheckoutGiftwrapAmount();
		return $giftwrapAmount;
	}
	
	public function baseGiftwrapAmout(){
		$order = $this->getParentBlock()->getOrder();
		$giftwrapAmount = $order->getOnestepcheckoutGiftwrapAmount();
		$orderCurrencyCode = $order->getOrderCurrency()->getCurrencyCode();
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		if ($baseCurrencyCode != $orderCurrencyCode) {
			$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
			$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
			return $giftwrapAmount/$rates[$orderCurrencyCode];
		}else{
			return $giftwrapAmount;
		}
	}
}