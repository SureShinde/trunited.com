<?php
/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS `{$this->getTable('easygiftcard_reportcard')}`;
	CREATE TABLE `{$this->getTable('easygiftcard_reportcard')}` (
	  `report_id` int(11) unsigned NOT NULL auto_increment,
	  `customer_id` INT(10) UNSIGNED NOT NULL,
	  `order_id` INT(10) UNSIGNED NOT NULL,
	  `product_id` INT(10) UNSIGNED NOT NULL,
	  `file_name` varchar(255) NOT NULL,
	  `report_reason` varchar(255) NOT NULL,
	   PRIMARY KEY (`report_id`)
	) ENGINE=InnoDB;

");

$installer->endSetup(); 