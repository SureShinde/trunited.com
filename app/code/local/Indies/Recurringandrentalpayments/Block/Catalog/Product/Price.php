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

class Indies_Recurringandrentalpayments_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{
    /* MAPRICE COPYPASTE BEGIN*/

    protected $data;
    protected $product;
    protected $mapCondition;
    protected $listPriceCondition;


    public function isCustomTemplateNeeded()
    {
        $res = false;
        $this->product = $this->getProduct();
		if ('catalog/product/price.phtml' == $this->getTemplate() && in_array($this->product->getTypeId(), array('simple', 'virtual', 'configurable', 'simple')))
		{
			$this->product = Mage::getModel('maprice/maprice')->getProductAttributes($this->product);
			$this->data = $this->product->getData();
			$maPrice = $this->data['minimum_advertised_price'];
			$this->mapCondition = $maPrice && $maPrice > floatval($this->product->getPrice());
			$this->listPriceCondition = (bool)($this->data['list_price']);
			$this->youSaveCondition = (bool)($this->data['you_save_price']);
			$res = ($this->listPriceCondition || $this->mapCondition || $this->youSaveCondition);
		}
        return $res;
    }

    protected function _toHtml()
    {
       return parent::_toHtml();
    }

    /* MAPRICE COPYPASTE END*/

    public function getProduct()
    {
        $product = parent::getProduct();
        $rnrProduct = Mage::getModel('catalog/product')->load($product->getId());
        return $rnrProduct;
    }
}

?>
