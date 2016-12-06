<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Model_Source_Subscription_Periods extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrive all attribute options
     *
     * @return array
     */

    public static $options;
    public static $options_by_id;
    protected $_collection = false;

    /**
     * Returns period types as array
     * @return array
     */
    public function getAllOptions()
    {

        if (!self::$options) {
            self::$options_by_id = array();
            $_options = array();
            $periods = $this->getCollection();

            if (false){//(!$this->getCollection()->hasProductFilterApplied() || ($this->getCollection()->hasNoSubscriptionOption())) {
                $periods->addItem(Mage::getModel('recurringandrentalpayments/terms')->setId('-1')->setName(Mage::helper('recurringandrentalpayments')->__('No subscription')));
            }

            foreach ($periods as $Period) {
                $_options[] = array('value' => $Period->getId(), 'label' => $Period->getLabel());
                self::$options_by_id[$Period->getId()] = $Period->getLabel();
            }
            self::$options = $_options;
        }
		
        return self::$options;
    }

    /**
     * Returns array ready for use by grid
     * @return array
     */
    public function getGridOptions()
    {
        $items = $this->getAllOptions();
        $out = array();
        //array_shift($items);
        foreach ($items as $item) {
            $out[$item['value']] = $item['label'];
        }
		return $out;
    }

    /**
     * Returns oeriods collection
     * @return Indies_Recurringandrentalpayments_Model_Source_Subscription_Periods_Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = Mage::getModel('recurringandrentalpayments/terms')->getCollection();
        }
        return $this->_collection;
    }
	public function getCollection1()
    {
		if (!$this->_collection) {
            $this->_collection = Mage::getModel('recurringandrentalpayments/plans')->getCollection();
        }
        return $this->_collection;
    }
    /**
     * @param object $value
     * @return
     */
    public function getOptionText($value)
    {

        $out = array();
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        $this->getAllOptions();
        if (is_array($value)) {
            foreach ($value as $key) {
                $out[] = @self::$options_by_id[$key];
            }
        }
        return implode(',', $out);
    }
	public function hasNoSubscriptionOption()
    {
        if ($Product = $this->getProduct()) {
            $opts = Mage::getModel('recurringandrentalpayments/plans')->load($Product->getId(),'product_id');
            if ($opts->getIsNormal()==1) {
                return true;
            }
        }
        return false;
    }
	public function getProduct()
    {
       return Mage::registry('product');
    }
}