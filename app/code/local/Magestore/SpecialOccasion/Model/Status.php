<?php

class Magestore_SpecialOccasion_Model_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	const STATUS_ITEM_PENDING = 1;
	const STATUS_ITEM_PROCESSING = 2;
	const STATUS_ITEM_COMPLETE = 3;

	const STATE_ACTIVE = 1;
	const STATE_INACTIVE = 2;

	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED	=> Mage::helper('specialoccasion')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('specialoccasion')->__('Disabled')
		);
	}
	
	static public function getOptionHash(){
		$options = array();
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}

    static public function getOptionItemArray(){
        return array(
            self::STATUS_ITEM_PENDING	=> Mage::helper('specialoccasion')->__('Pending'),
            self::STATUS_ITEM_PROCESSING   => Mage::helper('specialoccasion')->__('Processing'),
            self::STATUS_ITEM_COMPLETE   => Mage::helper('specialoccasion')->__('Complete'),
        );
    }

    static public function getOptionItemHash(){
        $options = array();
        foreach (self::getOptionItemArray() as $value => $label)
            $options[] = array(
                'value'	=> $value,
                'label'	=> $label
            );
        return $options;
    }

    static public function getOptionStateArray(){
        return array(
            self::STATE_ACTIVE	=> Mage::helper('specialoccasion')->__('Active'),
            self::STATE_INACTIVE   => Mage::helper('specialoccasion')->__('Inactive'),
        );
    }

    static public function getOptionStateHash(){
        $options = array();
        foreach (self::getOptionStateArray() as $value => $label)
            $options[] = array(
                'value'	=> $value,
                'label'	=> $label
            );
        return $options;
    }
}
