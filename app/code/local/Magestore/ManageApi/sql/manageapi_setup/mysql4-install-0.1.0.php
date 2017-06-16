<?php

$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('manageapi/linkshare')};
CREATE TABLE {$this->getTable('manageapi/linkshare')} (
  `linkshare_id` int(11) unsigned NOT NULL auto_increment,
  `member_id` int(11) unsigned NOT NULL,
  `advertiser_name` varchar(255) NOT NULL,
  `order_id` int(11) unsigned NOT NULL,
  `transaction_date` datetime NULL,
  `sales` FLOAT unsigned,
  `total_commission` FLOAT unsigned,
  `process_date` datetime NULL,
  `created_at` datetime NULL,
  PRIMARY KEY (`linkshare_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 