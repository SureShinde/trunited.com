<?php

class Magestore_Affiliateplusdirectlink_Block_Adminhtml_Renderer_Banner extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
        return sprintf('<a href="%s" title="%s">%s</a>', $this->getUrl('adminhtml/affiliateplus_banner/edit/', array('id' => $row->getBannerId())), Mage::helper('affiliateplus')->__('View Banner Detail'), $row->getTitle()
        );
    }

}