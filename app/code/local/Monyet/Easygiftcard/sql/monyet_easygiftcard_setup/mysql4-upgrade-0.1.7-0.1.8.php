<?php
/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
	ALTER TABLE {$this->getTable('easygiftcard_reportcard')}
    ADD `created_at` datetime NOT NULL AFTER `report_reason` ;
    ");
$installer->endSetup(); 