<?php

$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('interest/interest')};
CREATE TABLE {$this->getTable('interest/interest')} (
  `interest_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `status` smallint(6) NULL default 1,
  `sort_order` INT NULL default 1,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`interest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('interest/customer')};
CREATE TABLE {$this->getTable('interest/customer')} (
  `interest_customer_id` int(11) unsigned NOT NULL auto_increment,
  `interest_id` int(11) unsigned NOT NULL,
  `customer_id` int(11) unsigned NOT NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`interest_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 