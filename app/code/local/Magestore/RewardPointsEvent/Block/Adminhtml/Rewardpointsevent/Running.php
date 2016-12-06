<?php

class Magestore_RewardpointsEvent_Block_Adminhtml_Rewardpointsevent_Running extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $actionName = $this->getRequest()->getActionName();
        $applied = $row->getData($this->getColumn()->getIndex());
        if (strpos($actionName, 'export') === 0) {
            if ($applied)
                return Mage::helper('rewardpointsevent')->__('Yes');
            else {
                return Mage::helper('rewardpointsevent')->__('No');
            }
        }
        if ($applied)
            return sprintf('<span class="grid-severity-minor"><span>%s</span></span>', Mage::helper('rewardpointsevent')->__('Yes'));
        else {
            return sprintf('<span class="grid-severity-minor"><span>%s</span></span>', Mage::helper('rewardpointsevent')->__('No'));
        }
    }

}