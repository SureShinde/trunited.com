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
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Magestore_RewardPoints_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('rewardpoints/transaction'), 'receiver_email', 'varchar(255) NULL');
$installer->getConnection()->addColumn($this->getTable('rewardpoints/transaction'), 'receiver_customer_id', 'int NULL');

$installer->endSetup();