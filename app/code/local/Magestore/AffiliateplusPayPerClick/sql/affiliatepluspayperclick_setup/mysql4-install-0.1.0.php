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
 * @package     Magestore_AffiliateplusPayPerClick
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create affiliatepluspayperclick table
 */
$installer->run("
    
DROP TABLE IF EXISTS {$this->getTable('affiliatepluspayperclick_program_commission')};

CREATE TABLE {$this->getTable('affiliatepluspayperclick_program_commission')} (
  `affiliatepluspayperclick_program_commission_id` int(10) unsigned NOT NULL auto_increment,
  `program_id` int(10) unsigned NOT NULL,
  `commission` decimal(12,4) NOT NULL default 0,
  `is_commission_default`  TINYINT(1) NOT NULL default '1',
  `store_id` smallint(5) unsigned  NOT NULL,
  PRIMARY KEY (`affiliatepluspayperclick_program_commission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

