<?php

class Magestore_Affiliateplus_Model_System_Config_Source_Transfer
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
			array('value' => '1', 'label'=>Mage::helper('affiliateplus')->__('TruWallet')),
            array('value' => '2', 'label'=>Mage::helper('affiliateplus')->__('Trunited Gift Card')),
        );
    }

}