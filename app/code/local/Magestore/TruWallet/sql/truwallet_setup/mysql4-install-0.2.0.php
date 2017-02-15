<?php

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('sales/order'), 'created_by', 'tinyint NULL default 2');

$installer->endSetup(); 