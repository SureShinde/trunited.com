<?php

class Magestore_Affiliateplusdirectlink_Block_Adminhtml_Renderer_Sourcepage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        return sprintf('<a href="%s" title="%s">%s</a>', 'http://'.$row->getSourcePage(), 'http://'.$row->getSourcePage(), $row->getSourcePage());
    }

}