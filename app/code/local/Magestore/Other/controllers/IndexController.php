<?php

class Magestore_Other_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		zend_debug::dump(Mage::helper('other')->ytd());
		zend_debug::dump( Mage::helper('other')->mtd());
		$this->loadLayout();
		$this->renderLayout();
	}

	public function customerActiveAction()
	{
		$setup = new Mage_Eav_Model_Entity_Setup();
		$installer = $setup;
		$installer->startSetup();
		$installer->run("");

		/*$setup->removeAttribute('customer','status_active');*/
		$setup->removeAttribute('customer','is_active');

		$entityTypeId = $setup->getEntityTypeId('customer');
		$attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
		$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);


		$installer->addAttribute("customer", "is_active",  array(
			"type"     => "int",
			"backend"  => "",
			"label"    => "Is Active",
			"input"    => "select",
			"source"   => "eav/entity_attribute_source_boolean",
			"visible"  => true,
			"required" => true,
			"default"   => 1,
			"frontend" => "",
			"unique"     => true,
			"note"       => "Allow admin to active or inactive the customer. When the customer is changed to Inactive status, their email will be added 'inactive-' prefix automatically.",
		));

		$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "is_active");
		$setup->addAttributeToGroup(
			$entityTypeId,
			$attributeSetId,
			$attributeGroupId,
			'is_active',
			'102'
		);

		$used_in_forms=array();
		$used_in_forms[]="adminhtml_customer";
		$used_in_forms[]="checkout_register";
		$used_in_forms[]="customer_account_create";
		$used_in_forms[]="customer_account_edit";
		$used_in_forms[]="adminhtml_checkout";

		$attribute->setData("used_in_forms", $used_in_forms)
			->setData("is_used_for_customer_segment", true)
			->setData("is_system", 0)
			->setData("is_user_defined", 1)
			->setData("is_visible", 1)
			->setData("sort_order", 102)
		;
		$attribute->save();

		$customers = Mage::getModel('customer/customer')->getCollection();
		Mage::getSingleton('core/resource_iterator')->walk($customers->getSelect(), array('callback'));

		$installer->endSetup();
		echo "success";
	}

	function callback($args)
	{
		$customer = Mage::getModel('customer/customer');
		$customer->setData($args['row']);
		$customer->setStatusActive(1);
		$customer->getResource()->saveAttribute($customer, 'status_active');
	}
}