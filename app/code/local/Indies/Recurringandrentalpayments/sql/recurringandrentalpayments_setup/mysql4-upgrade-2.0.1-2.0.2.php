<?php

$installer = $this;
    $installer->startSetup();

    $installer->getConnection()
    ->addColumn($installer->getTable('recurringandrentalpayments/subscription'),'billing_agreement_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => false,
        'length'    => 30,
        'comment'   => 'Paypal Billing Agreement Id'
        ));   
    $installer->endSetup();

?>
