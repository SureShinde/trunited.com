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

class  Indies_Recurringandrentalpayments_Block_Product_View_Type_Subscription extends Varien_Object
{
    const TEMPLATE_RADIOS = 'recurringandrentalpayments/product/view/subscription/radio_selector.phtml'; // Radios selector
    const TEMPLATE_OPTIONS = 'recurringandrentalpayments/product/view/subscription/selector.phtml'; // Normal HTML selector

    const DATE_FIELD_NAME = "indies_recurringandrentalpayments_subscription_start";

    protected $_productBlock;

    public function __construct($productBlock)
    {
        $this->_productBlock = $productBlock;
        parent::__construct();
    }

    protected function getProductBlock()
    {
        return $this->_productBlock;
    }

    /**
     * Returns all available subscription types
     * @return array
     */
    public function getSubscriptionTypes()
    {
        if (!$this->getData('subscription_types')) {
			$plans = Mage::getModel('recurringandrentalpayments/plans_product')->load($this->getProduct()->getId(),'product_id');
			
            $data = Mage::getModel('recurringandrentalpayments/source_subscription_periods');
            $data->getCollection1()->addFieldToFilter('plan_id',$plans->getPlanId());
            $this->hasSubscriptionOptions();
            $this->setData('subscription_types', $data->getAllOptions());
        }
        return $this->getData('subscription_types');
    }

    /**
     * Returns default period
     * @return Indies_Recurringandrentalpayments_Model_Terms
     */
    public function getDefaultPeriod()
    {
	
        if (!$this->getData('default_period')) {
            $this->setData('default_period', Mage::getModel('recurringandrentalpayments/terms')->load($this->getDefaultPeriodId()));
        }
        return $this->getData('default_period');
    }

    /**
     * Returns defult period id
     * @return int
     */
    public function getDefaultPeriodId()
    {
        foreach ($this->getSubscriptionTypes() as $k => $v) {
            return $v['value'];
        }
    }

    /**
     * Returns current product
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Returns ready-to-use calendar field HTML
     * @return string
     */
    public function getCalendarHtml()
    {
        $zDate = new Zend_Date($this->getProductBlock()->formatDate($this->getDefaultPeriod()->getNearestAvailableDay(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), null, Mage::app()->getLocale()->getLocaleCode());
        $date = $zDate->toString(preg_replace(array('/M+/', '/d+/'), array('MM', 'dd'), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)));
        $calendar = $this->getProductBlock()
                ->getLayout()
                ->createBlock('recurringandrentalpayments/html_date')
                ->setId(self::DATE_FIELD_NAME)
                ->setName(self::DATE_FIELD_NAME)
                ->setPeriod($this->getDefaultPeriod())
                ->setClass('product-custom-option datetime-picker input-text')
                ->setImage(Mage::getDesign()->getSkinUrl('recurringandrentalpayments/images/grid-cal.gif'))
                ->setValue($date)
                ->setFormat(Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        return $calendar->getHtml();
    }

    /**
     * Returns true if product has subscriptions options
     * @return bool
     */
    public function hasSubscriptionOptions()
    {
		$plans = Mage::getModel('recurringandrentalpayments/plans_product')->load($this->getProduct()->getId(),'product_id');
//		$plans = Mage::getModel('recurringandrentalpayments/plans')->load($this->getProduct()->getId())->getData();

		if($plans->getProductId())
		{		   
			
			return true;
		}
		return false;
    }

    public function requiresSubscription()
    {
        return true;
    }

    /**
     * Return current template
     * @return string
     */
    public function getTemplate()
    {
        if (Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_DISPLAY_TYPE) == 2) {
            return self::TEMPLATE_RADIOS;
        } else {
            return self::TEMPLATE_OPTIONS;
        }
    }
}

?>