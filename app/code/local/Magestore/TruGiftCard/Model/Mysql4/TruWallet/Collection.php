<?php

class Magestore_TruGiftCard_Model_Mysql4_TruGiftCard_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('trugiftcard/trugiftcard');
	}
}