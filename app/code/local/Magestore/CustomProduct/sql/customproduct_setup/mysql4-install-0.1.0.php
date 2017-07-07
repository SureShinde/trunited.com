<?php

$installer = $this;
$installer->startSetup();

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'shop_now', array(
    'group'            => 'General',
    'input'            => 'text',
    'type'             => 'text',
    'sort_order'       => 4,
    'label'            => 'Shop Now',
    'backend'          => '',
    'visible'          => true,
    'required'         => true,
    'wysiwyg_enabled'  => false,
    'visible_on_front' => true,
    'used_in_product_listing' => true,
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'note'             => Mage::helper('customproduct')->__('You are able to insert {{customer_id}} variable to Url. It will be displayed the identity of the customer who logged.'),
));

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'shop_now', 'apply_to', Magestore_CustomProduct_Model_Type::PRODUCT_TYPE_SHOP_NOW);


$installer->endSetup(); 