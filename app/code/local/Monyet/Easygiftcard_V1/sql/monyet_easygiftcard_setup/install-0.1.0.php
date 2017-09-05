<?php
/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Add attributes to the eav/attribute table
 */
$installer->addAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'pdf_path',
    array(
        'type'                    => 'text',
        'backend'                 => '',
        'frontend'                => '',
        'label'                   => 'PDF Path',
        'input'                   => 'text',
        'class'                   => '',
        'source'                  => '',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => false,
        'default'                 => '',
        'searchable'              => false,
        'filterable'              => false,
        'comparable'              => false,
        'visible_on_front'        => false,
        'unique'                  => false,
        'is_configurable'         => false,
        'used_in_product_listing' => false,
		'group'                   => 'General',
		'apply_to'                => Mage_Catalog_Model_Product_Type::TYPE_GROUPED
    )
);
$installer->addAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'rw_point_percent',
    array(
        'type'                    => 'text',
        'backend'                 => '',
        'frontend'                => '',
        'label'                   => 'Rw Point Percent',
        'input'                   => 'text',
        'class'                   => '',
        'source'                  => '',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => false,
        'user_defined'            => false,
        'default'                 => '',
        'searchable'              => false,
        'filterable'              => false,
        'comparable'              => false,
        'visible_on_front'        => false,
        'unique'                  => false,
        'is_configurable'         => false,
        'used_in_product_listing' => false,
		'group'                   => 'General',
		'apply_to'                => Mage_Catalog_Model_Product_Type::TYPE_GROUPED
    )
);


$installer->endSetup();