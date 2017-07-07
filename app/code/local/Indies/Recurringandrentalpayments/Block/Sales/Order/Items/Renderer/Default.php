<?php
class Indies_Recurringandrentalpayments_Block_Sales_Order_Items_Renderer_Default extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    public function getItemOptions()
    {

        $result = array();
        if ($options = $this->getOrderItem()->getProductOptions()) {
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
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }

        }
        return $result;
    }

}