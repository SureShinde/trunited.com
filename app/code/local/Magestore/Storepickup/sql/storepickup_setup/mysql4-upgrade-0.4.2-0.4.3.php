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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

$installer = $this;
$installer->startSetup();
// Required tables
$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');
 
// Insert statuses
//$installer->getConnection()->insertArray(
//    $statusTable,
//    array(
//        'status',
//        'label'
//    ),
//    array(
//        array('status' => 'store_pickup', 'label' => 'Store Pickup')
//
//    )
//);
 
// Insert states and mapping of statuses to states
//$installer->getConnection()->insertArray(
//    $statusStateTable,
//    array(
//        'status',
//        'state',
//        'is_default'
//    ),
//    array(
//        array(
//            'status' => 'store_pickup',
//            'state' => 'store_pickup',
//            'is_default' => 1
//        )
//    )
//);
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('storepickup_tag')};
CREATE TABLE {$this->getTable('storepickup_tag')} (
  `tag_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('storepickup_store')} ADD `tag_ids` varchar(200) NOT NULL default '';

DROP TABLE IF EXISTS {$this->getTable('storepickup/distance')};
            CREATE TABLE {$this->getTable('storepickup/distance')} (
              `distance_id` int(10) unsigned NOT NULL auto_increment,
              `destination_addresses` varchar(255) NOT NULL,
              `origin_addresses` varchar(255) NOT NULL,
              `distance_value` FLOAT NOT NULL,
              `unit` INT(5) NOT NULL,
              `updated_time` datetime NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`distance_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE {$this->getTable('storepickup/distance')} ADD UNIQUE `unique_index`(`destination_addresses`,`origin_addresses`);
    ");
$installer->endSetup();