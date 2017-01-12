<?php
class Magestore_Onestepcheckout_Model_Sales_Quote_Address_Total_Freeshipping extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
		$session = Mage::getSingleton('checkout/session');
		$deliveryType = $session->getData('delivery_type');
		if($deliveryType == 1){
			$address->setFreeShipping(true);
        }
    }
}