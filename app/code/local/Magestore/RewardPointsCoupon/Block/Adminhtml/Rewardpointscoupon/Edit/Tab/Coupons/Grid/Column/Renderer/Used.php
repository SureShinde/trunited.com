<?php

class Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Edit_Tab_Coupons_Grid_Column_Renderer_Used
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $value = (int)$row->getData($this->getColumn()->getIndex());
        return empty($value) ? Mage::helper('adminhtml')->__('No') : Mage::helper('adminhtml')->__('Yes');
    }
}
