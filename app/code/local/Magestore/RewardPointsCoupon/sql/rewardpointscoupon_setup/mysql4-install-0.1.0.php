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
 * @package     Magestore_RewardPointsCoupon
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create rewardpointscoupon table
 */
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('rewardpointscoupon/template')};
DROP TABLE IF EXISTS {$this->getTable('rewardpointscoupon/coupon')};
DROP TABLE IF EXISTS {$this->getTable('rewardpointscoupon/coupon_usage')};
DROP TABLE IF EXISTS {$this->getTable('rewardpointscoupon/coupon_customer')};
CREATE TABLE {$this->getTable('rewardpointscoupon/template')} (
  `template_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) default '',
  `coupon_code` varchar(255) default '',
  `point_balance` decimal(12,4) default '0',
  `currency` varchar(45) default '',
  `from_date` datetime NULL,
  `to_date` datetime NULL,
  `created_at` datetime NULL,
  `point_expired` int(10) unsigned,
  `uses_per_coupon` int(10) default '0',
  `uses_per_customer` int(10) default '0',
  `use_auto_generation` smallint(5) unsigned NOT NULL default '0',
  `website_ids` TEXT DEFAULT '',
  `customer_group_ids` TEXT DEFAULT '',
  `status` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE {$this->getTable('rewardpointscoupon/coupon')} (
  `coupon_id` int(10) unsigned NOT NULL auto_increment,
  `template_id` int(10) unsigned NOT NULL,
  `code` varchar(255) default '',
  `created_at` datetime NULL,
  `expiration_date` datetime NULL,
  `usage_limit` int(10) default '0',
  `usage_per_customer` int(10) default '0',
  `times_used` int(10) default '0',
  `type` smallint(5) default '0',
  `is_primary` smallint(5) unsigned,
  PRIMARY KEY (`coupon_id`),
  KEY `FK_REWARDPOINTS_TEMPLATE_COUPON` (`template_id`),
  CONSTRAINT `FK_REWARDPOINTS_TEMPLATE_COUPON` FOREIGN KEY (`template_id`) REFERENCES {$this->getTable('rewardpointscoupon/template')}(`template_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
/**
 * Create table 'rewardpointscoupon/coupon_usage'
 */
if (version_compare(Mage::getVersion(), '1.6.0.2', '>=')) {
    $table = $installer->getConnection()
            ->newTable($installer->getTable('rewardpointscoupon/coupon_usage'))
            ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                    ), 'Coupon Id')
            ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                    ), 'Customer Id')
            ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                    ), 'Times Used')
            ->addIndex($installer->getIdxName('rewardpointscoupon/coupon_usage', array('coupon_id')), array('coupon_id'))
            ->addIndex($installer->getIdxName('rewardpointscoupon/coupon_usage', array('customer_id')), array('customer_id'))
            ->addForeignKey($installer->getFkName('rewardpointscoupon/coupon_usage', 'coupon_id', 'rewardpointscoupon/coupon', 'coupon_id'), 'coupon_id', $installer->getTable('rewardpointscoupon/coupon'), 'coupon_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('rewardpointscoupon/coupon_usage', 'customer_id', 'rewardpoints/customer', 'customer_id'), 'customer_id', $installer->getTable('rewardpoints/customer'), 'customer_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->setComment('Rewardpoints Coupon Usage');
    $installer->getConnection()->createTable($table);

    /**
     * Create table 'rewardpoints/coupon_customer'
     */
    $table = $installer->getConnection()
            ->newTable($installer->getTable('rewardpointscoupon/coupon_customer'))
            ->addColumn('coupon_customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                    ), 'Coupon Customer Id')
            ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                    ), 'Template Id')
            ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                    ), 'Customer Id')
            ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                    ), 'Times Used')
            ->addIndex($installer->getIdxName('rewardpointscoupon/coupon_customer', array('template_id', 'customer_id')), array('template_id', 'customer_id'))
            ->addIndex($installer->getIdxName('rewardpointscoupon/coupon_customer', array('customer_id', 'template_id')), array('customer_id', 'template_id'))
            ->addForeignKey($installer->getFkName('rewardpointscoupon/coupon_customer', 'customer_id', 'customer/entity', 'entity_id'), 'customer_id', $installer->getTable('customer/entity'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('rewardpointscoupon/coupon_customer', 'template_id', 'rewardpointscoupon/template', 'template_id'), 'template_id', $installer->getTable('rewardpointscoupon/template'), 'template_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->setComment('Rewardpoints Coupon Customer');
    $installer->getConnection()->createTable($table);
} else {
    $tableRewardpointsCouponTemplate = $installer->getTable('rewardpointscoupon/template');
    $tableRewardpointsCouponUsage = $installer->getTable('rewardpointscoupon/coupon_usage');
    $tableRewardpointsCouponCustomer = $installer->getTable('rewardpointscoupon/coupon_customer');
    $tableCustomerEntity = $installer->getTable('customer/entity');
    $installer->run("
   CREATE TABLE `{$tableRewardpointsCouponUsage}` (
  `coupon_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `times_used` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`coupon_id`, `customer_id`),
  KEY `FK_RP_COUPON_CUSTOMER_COUPON_ID_CUSTOMER_ENTITY` (`coupon_id`),
  KEY `FK_RP_COUPON_CUSTOMER_CUSTOMER_ID_CUSTOMER_ENTITY` (`customer_id`),
  CONSTRAINT `FK_RP_COUPON_CUSTOMER_COUPON_ID_CUSTOMER_ENTITY` FOREIGN KEY (`coupon_id`) REFERENCES `{$this->getTable('rewardpointscoupon/coupon')}` (`coupon_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_RP_COUPON_CUSTOMER_CUSTOMER_ID_CUSTOMER_ENTITY` FOREIGN KEY (`customer_id`) REFERENCES `{$tableCustomerEntity}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 CREATE TABLE `{$tableRewardpointsCouponCustomer}` (
  `template_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `times_used` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`template_id`, `customer_id`),
  KEY `FK_RP_COUPON_CUSTOMER_COUPON_ID_TEMPLATE` (`template_id`),
  KEY `FK_RP_COUPON_USAGE_CUSTOMER_CUSTOMER_ID_CUSTOMER_ENTITY` (`customer_id`),
  CONSTRAINT `FK_RP_COUPON_CUSTOMER_COUPON_ID_TEMPLATE` FOREIGN KEY (`template_id`) REFERENCES `{$this->getTable('rewardpointscoupon/template')}` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_RP_COUPON_USAGE_CUSTOMER_CUSTOMER_ID_CUSTOMER_ENTITY` FOREIGN KEY (`customer_id`) REFERENCES `{$tableCustomerEntity}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
            ");
}
$installer->endSetup();

 