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
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create affiliateplusdirectlink table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('affiliateplus_direct_link_domain')};

CREATE TABLE {$this->getTable('affiliateplus_direct_link_domain')} (
  `domain_id` int(10) unsigned NOT NULL auto_increment,
  `account_id` int(10) unsigned NOT NULL,
  `domain` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '1',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  INDEX (`account_id`),
  FOREIGN KEY (`account_id`) REFERENCES {$this->getTable('affiliateplus_account')} (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('affiliateplus_direct_link')};

CREATE TABLE {$this->getTable('affiliateplus_direct_link')} (
  `direct_link_id` int(10) unsigned NOT NULL auto_increment,
  `domain_id` int(10) unsigned NOT NULL,
  `source_page` text NOT NULL default '',
  `banner_id` int(10) unsigned NOT NULL,
  `link` text NOT NULL default '',
  `store_id` smallint(6) unsigned  NOT NULL,
  `status` smallint(6) NOT NULL default '1',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  INDEX (`domain_id`),
  FOREIGN KEY (`domain_id`) REFERENCES {$this->getTable('affiliateplus_direct_link_domain')} (`domain_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`direct_link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

