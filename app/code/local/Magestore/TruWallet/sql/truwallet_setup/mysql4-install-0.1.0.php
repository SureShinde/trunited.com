<?php

$installer = $this;
$installer->startSetup();

$installer->run("

  DROP TABLE IF EXISTS {$this->getTable('truwallet/customer')};
  DROP TABLE IF EXISTS {$this->getTable('truwallet/transaction')};

  CREATE TABLE {$this->getTable('truwallet/customer')} (
    `truwallet_id` int(11) unsigned NOT NULL auto_increment,
    `customer_id` int(10) unsigned NOT NULL,
    `truwallet_credit` DECIMAL(10,2) unsigned NOT NULL default 0,
    `created_time` datetime NULL,
    `updated_time` datetime NULL,
    PRIMARY KEY (`truwallet_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  CREATE TABLE {$this->getTable('truwallet/transaction')} (
    `transaction_id` int(10) unsigned NOT NULL auto_increment,
    `truwallet_id` int(10) unsigned NULL,
    `customer_id` int(10) unsigned NULL,
    `customer_email` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `action_type` smallint(5) NOT NULL default '0',
    `store_id` smallint(5) NOT NULL,
    `status` smallint(5) NOT NULL,
    `created_time` datetime NULL,
    `updated_time` datetime NULL,
    `expiration_date` datetime NULL,
    `order_id` int(10) unsigned NULL,
    `current_credit` DECIMAL(10,2) unsigned NOT NULL default 0,
    `changed_credit` DECIMAL(10,2) NOT NULL default 0,
    `receiver_email` varchar(255) NULL,
    `receiver_customer_id` INT unsigned NULL,
    `recipient_id` INT unsigned,
    `point_back` FLOAT,
    PRIMARY KEY (`transaction_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->getConnection()->addColumn($this->getTable('sales/order'), 'truwallet_earn', 'int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'truwallet_spent', 'int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'truwallet_base_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'truwallet_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'truwallet_base_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'truwallet_amount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'truwallet_earn', 'int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'truwallet_spent', 'int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'truwallet_base_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'truwallet_discount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn($this->getTable('sales/invoice'), 'truwallet_base_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/invoice'), 'truwallet_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/creditmemo'), 'truwallet_base_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/creditmemo'), 'truwallet_discount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'truwallet_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_truwallet_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_truwallet_discount_for_shipping', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'truwallet_discount_for_shipping', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_truwallet_hidden_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'truwallet_hidden_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_truwallet_shipping_hidden_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'truwallet_shipping_hidden_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'truwallet_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_truwallet_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_truwallet_hidden_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'truwallet_hidden_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'truwallet_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_truwallet_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_truwallet_hidden_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'truwallet_hidden_tax', 'decimal(12,4) NULL');

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'truwallet_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_truwallet_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_truwallet_hidden_tax', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'truwallet_hidden_tax', 'decimal(12,4) NULL');




$installer->endSetup(); 