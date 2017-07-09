<?php

class Magestore_Custompromotions_Block_Language extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
        $this->setTemplate('custompromotions/account/language.phtml');
	    return parent::_prepareLayout();
	}

    protected function _toHtml() {
        return parent::_toHtml();
    }

}