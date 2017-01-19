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

DROP TABLE IF EXISTS {$this->getTable('trubox/order')};

CREATE TABLE {$this->getTable('trubox/order')} (
  `trubox_order_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) NOT NULL,
  `order_id` int(10) NOT NULL,
  `updated_time` datetime NULL,
  `created_time` datetime NULL,
  PRIMARY KEY (`trubox_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 ALTER TABLE {$this->getTable('trubox/item')} ADD `order_id` INT;
 ALTER TABLE {$this->getTable('trubox/item')} ADD `price` FLOAT;

");

$installer->endSetup();
