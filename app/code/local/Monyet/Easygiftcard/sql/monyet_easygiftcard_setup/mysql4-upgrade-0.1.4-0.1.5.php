<?php
/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$setup = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$setup->addAttribute('order_item', 'pdf_report', array('type' => 'text', 'default' => '', 'visible' => false));

$installer->endSetup();