<?php

$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('specialoccasion/specialoccasion')};
CREATE TABLE {$this->getTable('specialoccasion/specialoccasion')} (
  `specialoccasion_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(10) NOT NULL,
  `status` varchar(255) NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  `use_trugiftcard` tinyint(4) NULL DEFAULT 1,
  PRIMARY KEY (`specialoccasion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('specialoccasion/address')};
CREATE TABLE {$this->getTable('specialoccasion/address')} (
  `address_id` int(10) unsigned NOT NULL auto_increment,
  `specialoccasion_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `firstname` text NULL,
  `lastname` text NULL,
  `company` text NULL,
  `telephone` text NULL,
  `fax` text NULL,
  `street` text NULL,
  `state` text NULL,
  `city` text NULL,
  `zipcode` text NULL,
  `country` text NULL,
  `address_type` int(10) DEFAULT 2,
  `region` text DEFAULT NULL,
  `region_id` int(10),
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('specialoccasion/item')};
CREATE TABLE {$this->getTable('specialoccasion/item')} (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `specialoccasion_id` int(10) NULL,
  `product_id` varchar(255) NOT NULL,
  `qty` int(10) NULL,
  `origin_params` text DEFAULT NULL,
  `option_params` text DEFAULT NULL,
  `occasion` VARCHAR(255) DEFAULT NULL,
  `ship_date` datetime NULL,
  `message` VARCHAR(255) NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  `status` tinyint NULL,
  `state` tinyint DEFAULT 1,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('specialoccasion/history')};
CREATE TABLE {$this->getTable('specialoccasion/history')} (
  `history_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) NOT NULL,
  `customer_name` VARCHAR (255) NOT NULL,
  `customer_email` VARCHAR (255) NOT NULL,
  `order_id` int(10) NULL,
  `order_increment_id` int(10) NULL,
  `products` text NULL,
  `points` FLOAT NULL,
  `cost` FLOAT NULL,
  `updated_at` datetime NULL,
  `created_at` datetime NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('tokenbase/card')} ADD `use_in_occasion` tinyint(4) NULL DEFAULT 0;

    ");

$installer->endSetup(); 
