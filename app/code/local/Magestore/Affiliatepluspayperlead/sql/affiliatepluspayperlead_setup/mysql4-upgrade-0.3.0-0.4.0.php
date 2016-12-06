<?php

$installer = $this;
$installer->startSetup();

$selectSQL = $installer->getConnection()->select()->reset()
    ->from(array('p' => $installer->getTable('affiliateplus_lead')), array())
    ->columns(array(
        'account_id',
        'account_name',
        'account_email',
        'customer_id',
        'customer_email',
        'type'      => 'IF (action IS NULL, NULL, action + 3)',
        'commission',
        'created_time',
        'status'    => 'IF (status < 3, 3 - status, 3)',
        'store_id',
    ));
$insertSQL = $selectSQL->insertFromSelect($installer->getTable('affiliateplus_transaction'),
    array(
        'account_id',
        'account_name',
        'account_email',
        'customer_id',
        'customer_email',
        'type',
        'commission',
        'created_time',
        'status',
        'store_id',
    ),
    true
);
$installer->getConnection()->query($insertSQL);

$installer->endSetup();
