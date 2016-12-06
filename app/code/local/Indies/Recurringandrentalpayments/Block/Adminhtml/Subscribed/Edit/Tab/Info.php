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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Subscribed_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        
        $this->setForm($form);
        
        $this->setTemplate('recurringandrentalpayments/info.phtml');
        
        return parent::_prepareForm();
    }
	public function getAddressEditLink($address, $label='')
    {
        if (empty($label)) {
            $label = $this->__('Edit');
        }
        $url = $this->getUrl('*/sales_order/address', array('address_id'=>$address->getId()));
        return '<a href="'.$url.'">' . $label . '</a>';
    }
	 public function getCustomerGroupName()
    {
        if ($this->getOrder()) {
            return Mage::getModel('customer/group')->load((int)$this->getOrder()->getCustomerGroupId())->getCode();
        }
        return null;
    }

    public function getCustomerViewUrl()
    {
        if ($this->getOrder()->getCustomerIsGuest() || !$this->getOrder()->getCustomerId()) {
            return false;
        }
        return $this->getUrl('*/customer/edit', array('id' => $this->getOrder()->getCustomerId()));
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$orderId));
    }
	public function getOrder()
	{
		$id=$this->getRequest()->getParam('id');
		$orderid=Mage::getModel('recurringandrentalpayments/subscription_item')->load($id,'subscription_id');
		$order_collection = Mage::getModel('sales/order')->load($orderid->getPrimaryOrderId());
		return $order_collection;
		
	}
 public function getCustomerAccountData()
    {
        $accountData = array();

        /* @var $config Mage_Eav_Model_Config */
        $config     = Mage::getSingleton('eav/config');
        $entityType = 'customer';
        $customer   = Mage::getModel('customer/customer');
        foreach ($config->getEntityAttributeCodes($entityType) as $attributeCode) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            $attribute = $config->getAttribute($entityType, $attributeCode);
            if (!$attribute->getIsVisible() || $attribute->getIsSystem()) {
                continue;
            }
            $orderKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getOrder()->getData($orderKey);
            if ($orderValue != '') {
                $customer->setData($attribute->getAttributeCode(), $orderValue);
                $dataModel  = Mage_Customer_Model_Attribute_Data::factory($attribute, $customer);
                $value      = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML);
                $sortOrder  = $attribute->getSortOrder() + $attribute->getIsUserDefined() ? 200 : 0;
                $sortOrder  = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = array(
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, array('br'))
                );
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }
	public function getPlans()
	{
		$termid=Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'))->getTermType();
		$plan=Mage::getModel('recurringandrentalpayments/terms')->load($termid)->getPlanId();
		
		return Mage::getModel('recurringandrentalpayments/plans')->load($plan);
	}
	public function getSubscriptions()
	{
		return Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));	
	}
	public function getTerms()
	{
		$termid=Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'))->getTermType();
		return Mage::getModel('recurringandrentalpayments/terms')->load($termid);
	}
	public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }
}