<?php
/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$this->getConnection()->dropColumn($this->getTable('sales_flat_order'), 'pdf_report', "");


$installer->endSetup();