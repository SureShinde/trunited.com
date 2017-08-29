<?php

class Magestore_Affiliateplusdirectlink_Block_Adminhtml_Renderer_Domain extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        return sprintf('<a href="%s" title="%s">%s</a>', 'http://'.$row->getDomain(), 'http://'.$row->getDomain(), $row->getDomain());
    }

}