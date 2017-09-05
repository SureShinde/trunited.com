<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('truinted_api/map'))
    ->addColumn('advertiser_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable'  => false), 'Advertiser Id')
    ->addColumn('tr_product_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array('nullable'  => false), 'Advertiser Product Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_BIGINT, null, array('nullable'  => false), 'Magento Product Id');
$installer->getConnection()->createTable($table);

$installer->endSetup();
