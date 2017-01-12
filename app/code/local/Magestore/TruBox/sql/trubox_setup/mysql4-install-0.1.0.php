<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/** @var $installer Magestore_trubox_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create trubox table and fields
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('trubox/trubox')};
DROP TABLE IF EXISTS {$this->getTable('trubox/address')};
DROP TABLE IF EXISTS {$this->getTable('trubox/payment')};

CREATE TABLE {$this->getTable('trubox/trubox')} (
  `trubox_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) NOT NULL,
  `status` text NULL,
  PRIMARY KEY (`trubox_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('trubox/address')} (
  `address_id` int(10) unsigned NOT NULL auto_increment,
  `trubox_id` int(10) NULL,
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
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('trubox/payment')} (
  `payment_id` int(10) unsigned NOT NULL auto_increment,
  `trubox_id` int(10) NULL,
  `card_type` varchar(255) NOT NULL,
  `name_on_card` varchar(255) NOT NULL,
  `cvv` int(10) NOT NULL,
  `card_number` varchar(63) NOT NULL,
  `month_expire` varchar(63) NOT NULL,
  `year_expire` varchar(63) NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('trubox/item')} (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `trubox_id` int(10) NULL,
  `product_id` varchar(255) NOT NULL,
   `qty` int(10) NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
