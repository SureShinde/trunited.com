<?php

class Magestore_Custompromotions_Block_Adminhtml_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		$order = Mage::getModel('sales/order')->load($value, 'increment_id');
		$url = Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view",array('order_id'=>$order->getId()));
		return '<a href="'.$url.'" target="_blank">'.$value.'</a>';
	}
}