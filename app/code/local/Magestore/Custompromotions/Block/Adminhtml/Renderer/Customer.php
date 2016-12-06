<?php

class Magestore_Custompromotions_Block_Adminhtml_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		$customer = Mage::getModel('customer/customer')->load($row['customer_id']);
		if($customer->getId()) {
			$url = Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit",array('id'=>$row['customer_id']));
			return '<a href="' . $url . '" target="_blank">' . $customer->getName() . '</a>';
		}
		else
			return '';
	}
}