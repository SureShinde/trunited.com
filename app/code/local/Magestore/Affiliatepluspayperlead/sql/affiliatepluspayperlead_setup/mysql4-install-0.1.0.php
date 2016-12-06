<?php

$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('affiliateplus_lead')};
CREATE TABLE {$this->getTable('affiliateplus_lead')} (
 `lead_id` int(10) unsigned NOT NULL auto_increment,
  `account_id` int(10) unsigned NOT NULL,
  `account_name` varchar(255) NOT NULL default '',
  `account_email` varchar(255)  NOT NULL,
  `customer_id` int(10) unsigned  NULL,
  `customer_email` varchar(255)  NOT NULL,
  `action`	tinyint NOT NULL default '1',
  `commission` decimal(12,4) NOT NULL default '0',
  `created_time` datetime NULL,
  `status` tinyint(1) NOT NULL default '1',
  `store_id` smallint(5) unsigned  NOT NULL,
  INDEX(`account_id`),
  INDEX(`store_id`),
  FOREIGN KEY (`account_id`) REFERENCES {$this->getTable('affiliateplus_account')} (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 