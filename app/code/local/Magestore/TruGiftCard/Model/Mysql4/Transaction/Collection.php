<?php

class Magestore_TruGiftCard_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('trugiftcard/transaction');
	}

    public function getFieldTotal($field = 'changed_credit') {
        $this->_renderFilters();

        $sumSelect = clone $this->getSelect();
        $sumSelect->reset(Zend_Db_Select::ORDER);
        $sumSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $sumSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $sumSelect->reset(Zend_Db_Select::COLUMNS);

        $sumSelect->columns("SUM(`$field`)");

        return $this->getConnection()->fetchOne($sumSelect, $this->_bindParams);
    }
}
