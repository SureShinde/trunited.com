<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Eventdiscount
 * @version    1.0.5
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

$installer = $this;
$installer->startSetup();
try {
    $installer->run("

CREATE TABLE IF NOT EXISTS `{$this->getTable('aweventdiscount/timer')}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timer_name` text NOT NULL,
  `title` text NOT NULL,
  `notice` text NOT NULL,
  `active_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active_to` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('0','1') NOT NULL,
  `store_ids` tinytext NOT NULL,
  `customer_group_ids` tinytext NOT NULL,
  `duration` bigint(20) NOT NULL COMMENT 'in seconds',
  `design` tinytext NOT NULL,
  `color` tinytext NOT NULL,
  `position` tinytext NOT NULL,
  `url` text NOT NULL,
  `url_type` INT NOT NULL DEFAULT  '0',
  `appearing` tinytext NOT NULL,
  `conditions_serialized` text NOT NULL,
  `event` tinytext NOT NULL,
  `limit` int(11) NOT NULL DEFAULT  '0',
  `limit_per_customer` int(11) NOT NULL DEFAULT  '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `{$this->getTable('aweventdiscount/action')}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timer_id` int(10) unsigned NOT NULL,
  `type` enum('fixed','percent','change_group') NOT NULL,
  `action` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `{$this->getTable('aweventdiscount/trigger')}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timer_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `duration` bigint(20) NOT NULL COMMENT 'in second',
  `active_to` TIMESTAMP NULL DEFAULT NULL,
  `trigger_status` enum('in_progress','missed','used') NOT NULL DEFAULT 'in_progress',
  `action` text NOT NULL,
  `quote_hash` text NOT NULL,
  `amount_serialized`  text NOT NULL,
  `trigger_event` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`trigger_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

    ");
} catch (Exception $ex) {
    Mage::logException($ex);
}
$installer->endSetup();