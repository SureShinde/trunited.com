<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 2/15/17
 * Time: 4:26 PM
 */

class Magestore_TruBox_Block_Adminhtml_Renderer_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract  {

    protected $_options = false;

    protected function _getOptions(){
        $this->_options = Magestore_TruBox_Model_Status::getOptionArray();
        return $this->_options;
    }

    public function render(Varien_Object $row){
        $value = $row->getData('created_by');
        $options = $this->_getOptions();

        return isset($options[$value]) ? $options[$value] : $value;
    }
}