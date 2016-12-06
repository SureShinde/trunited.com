<?php
class Indies_Recurringandrentalpayments_Model_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    /**
     * Check product representation in item
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  bool
     */
    public function representProduct($product)
    {
		$postdata = Mage::app()->getRequest()->getPost();

		/* Change for bundle product by Milople */ 
		if(isset($postdata['indies_recurringandrentalpayments_subscription_type']) &&
		   $postdata['indies_recurringandrentalpayments_subscription_type'] && 
		   $postdata['indies_recurringandrentalpayments_subscription_type'] >=0 && 
		   $postdata['bundle_option']  &&
		   Mage::app()->getRequest()->getActionName() == 'updateItemOptions') 
		{
				return false;
		}
			
        $itemProduct = $this->getProduct();
        if (!$product || $itemProduct->getId() != $product->getId()) {
            return false;
        }

        /**
         * Check maybe product is planned to be a child of some quote item - in this case we limit search
         * only within same parent item
         */
        $stickWithinParent = $product->getStickWithinParent();
        if ($stickWithinParent) {
            if ($this->getParentItem() !== $stickWithinParent) {
                return false;
            }
        }
        // Check options
        $itemOptions = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();

        if (!$this->compareOptions($itemOptions, $productOptions)) {
            return false;
        }
        if (!$this->compareOptions($productOptions, $itemOptions)) {
            return false;
        }
        return true;
    }
}
