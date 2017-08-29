<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 2/15/17
 * Time: 4:32 PM
 */

class Magestore_TruBox_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select  {

    protected $_options = false;

    protected function _getOptions(){
        $this->_options = Magestore_TruBox_Model_Status::getOptionHash();
        return $this->_options;
    }
}