<?php

$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('custompromotions')};
CREATE TABLE {$this->getTable('custompromotions')} (
  `custompromotions_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL,
  `affiliate_id` int(11) unsigned NULL,
  `register_amount` decimal(10,2),
  `referred_amount` decimal(10,2),
  `current_step` int(11) NULL ,
  `title` varchar(255) NULL default '',
  `type` int(11) NULL,
  `order_id` int(11) unsigned NULL,
  `status` smallint(6) NULL default '0',
  `created_time` datetime NULL,
  `updated_time` datetime NULL,
  PRIMARY KEY (`custompromotions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 