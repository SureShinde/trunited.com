<?php

class Magestore_Custompromotions_Block_Promotion extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getHelperConfiguration()
    {
        return Mage::helper('custompromotions/configuration');
    }

    public function isEnable()
    {
        return $this->getHelperConfiguration()->getDataConfig('ytd_mtd', 'enable');
    }

    public function getLabelForProfit()
    {
        return str_replace('{{current_year}}', date('Y', time()), $this->getHelperConfiguration()->getDataConfig('ytd_mtd', 'profit'));
    }

    public function getLabelForSharing()
    {
        return $this->getHelperConfiguration()->getDataConfig('ytd_mtd', 'shared');
    }
}