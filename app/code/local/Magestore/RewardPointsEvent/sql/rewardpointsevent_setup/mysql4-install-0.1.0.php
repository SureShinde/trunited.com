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
 * @package     Magestore_RewardPointsEvent
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create rewardpointsevent table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('rewardpoints_event')};

CREATE TABLE {$this->getTable('rewardpoints_event')} (
  `event_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `message` text NOT NULL default '',
  `website_ids` text default '',
  `customer_apply` smallint(6) unsigned NOT NULL,
  `customer_group_ids` text default '',
  `conditions_serialized` mediumtext default '',
  `apply_from` datetime NOT NULL,
  `apply_to` datetime NULL,
  `point_amount` int(11) NOT NULL,
  `expire_day` int(11),
  `enable_email` smallint(6) NOT NULL default 0,
  `email_template_id` varchar(250) NOT NULL default '',
  `repeat_type` smallint(6) NOT NULL default 0,
  `status` smallint(6) NOT NULL default 2,
  `month_from` smallint(6) unsigned NOT NULL,
  `month_to` smallint(6) unsigned NULL,
  `daym_from` smallint(6) unsigned NOT NULL,
  `daym_to` smallint(6) unsigned NULL,
  `day_from` smallint(6) unsigned NOT NULL,
  `day_to` smallint(6) unsigned NULL,
  `week_from` smallint(6) unsigned NOT NULL,
  `week_to` smallint(6) unsigned NULL,
  `file_name` varchar(255) NOT NULL default '',
  `is_apply` tinyint(1) NOT NULL default 0,
  `is_running` tinyint(1) NOT NULL default 0,
  `apply_success` int(10) NOT NULL default 0,
  `allow_create` smallint(6) NOT NULL default 0,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS {$this->getTable('rewardpoints_event_customer')};
CREATE TABLE {$this->getTable('rewardpoints_event_customer')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL,
  `email` varchar(255) NOT NULL default '',
  `name` varchar(250) NOT NULL default '',
  `store_id` smallint(5),
  `event_id` int(11) unsigned NOT NULL, 
  FOREIGN KEY (`event_id`) REFERENCES {$this->getTable('rewardpoints_event')} (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES {$this->getTable('customer/entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

