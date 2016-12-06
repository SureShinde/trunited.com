<?php
$installer = $this;
    $installer->startSetup();
    $installer->getConnection()
    ->addColumn($installer->getTable('recurringandrentalpayments/subscription'),'payment_token', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => false,
        'length'    => 40,
        'comment'   => 'Token ids of different payment method'
        ));   
    $installer->endSetup();

?>
