<?php

class Magestore_Nationpassport_Block_Search extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    public function getAccountCollection()
    {
        return $this->getAccounts();
    }

}
