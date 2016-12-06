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

class Indies_Recurringandrentalpayments_Block_Product_View_Type_Virtual extends Mage_Catalog_Block_Product_View_Type_Virtual
{
    protected $_subscriptionInstance;

    public function __construct()
    {
        $this->_subscriptionInstance = new Indies_Recurringandrentalpayments_Block_Product_View_Type_Subscription($this);
        return parent::__construct();
    }

    public function getSubscription()
    {
        return $this->_subscriptionInstance;
    }

    /**
     * Return current template
     * @return string
     */
    public function getTemplate()
    {
        if (parent::getTemplate()) return parent::getTemplate();
        return $this->getSubscription()->getTemplate();
    }

    /**
     * Get JSON encripted configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();
        /*if (!$this->hasOptions()) {
            return Mage::helper('core')->jsonEncode($config);
        }*/

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $this->getProduct()->getPrice();
        $_finalPrice = $this->getProduct()->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($this->getProduct(), $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($this->getProduct(), $_finalPrice);

        $config = array(
            'productId' => $this->getProduct()->getId(),
            'priceFormat' => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax' => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax' => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices' => Mage::helper('tax')->displayBothPrices(),
            'productPrice' => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice' => Mage::helper('core')->currency($_regularPrice, false, false),
            'skipCalculate' => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax' => $defaultTax,
            'currentTax' => $currentTax,
            'idSuffix' => '_clone',
            'oldPlusDisposition' => 0,
            'plusDisposition' => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition' => 0,
        );

        $responseObject = new Varien_Object();
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return Zend_Json::encode($config);
    }
	public function getDisplayCalendar($id)
	{
		$isavailable = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_GENERAL_ANONYMOUS_SUBSCRIPTIONS);	
		$plans_product = Mage::getModel('recurringandrentalpayments/plans_product')->load($id,'product_id');
		$plan = Mage::getModel('recurringandrentalpayments/plans')->load($plans_product->getPlanId(),'plan_id');
		$customer_group = explode(',',Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_CUSTOMER_GROUP));
		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		if(($isavailable == 1 ) || ($isavailable == 2 ) || (($isavailable == 3) && in_array($groupId,$customer_group)))
		{
			if(($plan->getPlanStatus() == 1))
			{
				return $plan->getStartDate();
			}
		}
		return 0;
	}
}

?>