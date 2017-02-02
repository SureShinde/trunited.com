<?php
class Magestore_Onestepcheckout_Model_Freeshipping extends Mage_SalesRule_Model_Quote_Freeshipping
{

    /**
     * Collect information about free shipping for all address items
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Freeshipping
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
		parent::collect($address);
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());

        $address->setFreeShipping(0);
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }
		/* Fix Shopping Cart Rule issue */
		// if($quote->getCustomerGroupId() == 0)
			// $quote->setCustomerGroupId(4);
		/* Fix Shopping Cart Rule issue */
        
		$this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());

        $isAllFree = true;
        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $isAllFree = false;
                $item->setFreeShipping(false);
            } else {
                /**
                 * Child item discount we calculate for parent
                 */
                if ($item->getParentItemId()) {
                    continue;
                }
                $this->_calculator->processFreeShipping($item);
                $isItemFree = (bool)$item->getFreeShipping();
                $isAllFree = $isAllFree && $isItemFree;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
					foreach ($item->getChildren() as $child) {
                        $this->_calculator->processFreeShipping($child);
                        /**
                         * Parent free shipping we apply to all children
                         */
                        if ($isItemFree) {
                            $child->setFreeShipping($isItemFree);
                        }

                    }
                }
            }
        }
        if ($isAllFree && !$address->getFreeShipping()) {
			$address->setFreeShipping(true);
        }
		/* Free shipping for Trubox this order option */
		if(Mage::getStoreConfig('onestepcheckout/giftwrap/enable_freeshipping', Mage::app()->getStore()->getId())){
			$session = Mage::getSingleton('checkout/session');
			$deliveryType = $session->getData('delivery_type');
			if($deliveryType == 1){
				$address->setFreeShipping(true);
			}
		}
		/* Free shipping for Trubox this order option */
		
        return $this;
    }

}