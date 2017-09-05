<?php
/**
 */
 
class Monyet_Easygiftcard_Model_Mysql4_Reportcard_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('monyet_easygiftcard/reportcard');
    }
	

}