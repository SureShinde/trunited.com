<?php

class Magestore_Custompromotions_Block_Adminhtml_Renderer_Affiliate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		$account = Mage::getModel('affiliateplus/account')->load($row->getData('affiliate_id'));
		if($account->getId()) {
			$url = Mage::helper("adminhtml")->getUrl("adminhtml/affiliateplus_account/edit",array('id'=>$row->getData('affiliate_id')));
			return '<a href="' . $url . '" target="_blank">' . $row['name'] . '</a>';
		}
		else
			return '';
	}
}