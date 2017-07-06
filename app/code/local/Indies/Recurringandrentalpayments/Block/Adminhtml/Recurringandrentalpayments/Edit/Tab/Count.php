<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Block_Adminhtml_Recurringandrentalpayments_Edit_Tab_Count extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 	public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        $collection = Mage::getModel('recurringandrentalpayments/terms')->getCollection();
		$collection->addFieldToFilter('plan_id',$value);
		$string = '';
		$count = $collection->getSize();
		$flag = 0;
		$counting = 0;
		foreach($collection as $col)
		{
			$counting++;
			if($flag==0)
			{
				$string=$col->getLabel();
				$flag=1;
			}
			else
			{
				$string.='<br>'.$col->getLabel();
			}
		}
		return $string;    
    }
}