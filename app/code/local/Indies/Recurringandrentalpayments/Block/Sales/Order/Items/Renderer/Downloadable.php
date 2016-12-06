<?php
class Indies_Recurringandrentalpayments_Block_Sales_Order_Items_Renderer_Downloadable extends Mage_Downloadable_Block_Sales_Order_Item_Renderer_Downloadable
{
    public function getItemOptions()
    {
        $result = array();
		
        if ($options = $this->getItem()->getProductOptions()) {
			
            $startDateLabel = $this->getItem()->getIsVirtual() ? $this->__("Subscription start:")
                    : $this->__("First delivery:");
            if (isset($options['info_buyRequest'])) {
				
                $periodTypeId = @$options['info_buyRequest']['indies_recurringandrentalpayments_subscription_type'];
                $periodStartDate = @$options['info_buyRequest']['indies_recurringandrentalpayments_subscription_start'];
                if ($periodTypeId && $periodStartDate && $periodTypeId > 0) {
					
                    $result[] = array(
                        'label' => $this->__('Subscription type:'),
                        'value' => Mage::getModel('recurringandrentalpayments/terms')->load($periodTypeId)->getLabel()
                    );

                    $result[] = array(
                        'label' => $startDateLabel,
						'value' => Mage::helper('recurringandrentalpayments')->__(date("M d, Y" ,strtotime($periodStartDate)))
                        
                    );
                }
            }
		}	
        return $result;
    }
}