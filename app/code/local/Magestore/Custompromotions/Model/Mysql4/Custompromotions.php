<?php

class Magestore_Custompromotions_Model_Mysql4_Custompromotions extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('custompromotions/custompromotions', 'custompromotions_id');
	}
}