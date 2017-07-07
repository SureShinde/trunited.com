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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Widget_Grid_Column_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render row store views
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $raw = $row->getData($this->getColumn()->getIndex());
        $products = preg_split(Indies_Recurringandrentalpayments_Model_Subscription_Flat::DB_EXPLODE_RE, $raw, -1, PREG_SPLIT_NO_EMPTY);
        return '<ul><li>' . implode("</li><li>", $products) . '</li></ul>';
    }

}
