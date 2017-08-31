<?php

class Magestore_SpecialOccasion_Model_Mysql4_Specialoccasion extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('specialoccasion/specialoccasion', 'specialoccasion_id');
	}
}
