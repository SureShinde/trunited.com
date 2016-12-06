<?php
class Indies_Recurringandrentalpayments_Block_Sales_Order_Total extends Mage_Core_Block_Template
{
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize reward points totals
     *
     * @return Enterprise_Reward_Block_Sales_Order_Total
     */
    public function initTotals()
    {
       $source = $this->getSource();
       $value  = $source->getRecurringDiscountAmount();
       
	    if ($value != 0) {

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'recurring_discount',
                'strong' => true,
                'label'  => 'Recurring Discount',
                'value'  => $value,
				'area' => 'footer'
            )), 'recurring_discount');
        }

        return $this;
    }
}
