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
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create rewardpointstransfer table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('rewardpoints_transfer')};

CREATE TABLE {$this->getTable('rewardpoints_transfer')} (
  `transfer_id` int(10) unsigned NOT NULL auto_increment,
  `sender_email` varchar(255) NOT NULL default '',
  `receiver_email` varchar(255) NOT NULL default '',
  `point_amount` int(11),
  `send_transaction_id` int(10) unsigned,
  `receive_transaction_id` int(10) unsigned,
  `sender_customer_id` int(11) unsigned,
  `receiver_customer_id` int(11) unsigned,
  `extra_content` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '1',
  `store_id` smallint(5) unsigned default '0',
  `pending_day` int(11) unsigned NOT NULL default '0',
  `holding_day` int(11) unsigned NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`transfer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ALTER TABLE {$this->getTable('rewardpoints_customer')}
   ADD COLUMN `transfer_notification` smallint(6) NOT NULL default '1';
 
");

$installer->endSetup();

