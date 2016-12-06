<?php
$installer = $this;
    $installer->startSetup();

    $installer->getConnection()
    ->addColumn($installer->getTable('recurringandrentalpayments/subscription'),'payflow_pnref_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => false,
        'length'    => 40,
        'comment'   => 'Paypal Payflow Pnref Id'
        ));   
	$installer->getConnection()
    ->addColumn($installer->getTable('recurringandrentalpayments/subscription'),'pnref_expiry_date', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
        'comment'   => 'Pnref Expiry Date'
        ));   
	$installer->getConnection()
    ->addColumn($installer->getTable('recurringandrentalpayments/subscription'),'pnref_mail_sent', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable'  => false,
        'default'   => 0,
        'comment'   => 'Sent update mail'
        ));   
	$installer->getConnection()
    ->addColumn($installer->getTable('recurringandrentalpayments/sequence'),'transaction_status', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'comment'   => 'Captured sequence transaction status'
        ));   
    $installer->endSetup();

?>
