<?php

$installer = $this;

$installer->startSetup();

// Convert from old configuration to new configuration fields
$movingPre = 'affiliateplus/';
$movingMap = array(
    'payperclick/enable'            => 'commission/payperclick_enable',
    'payperclick/clickcommission'   => 'commission/clickcommission',
);

$movingSql = '';
foreach ($movingMap as $moveFrom => $moveTo) {
    $movingSql .= "UPDATE {$this->getTable('core/config_data')} ";
    $movingSql .= "SET path = '" . $movingPre . $moveTo . "' ";
    $movingSql .= "WHERE path = '" . $movingPre . $moveFrom . "'; ";
}
$installer->run($movingSql);

$installer->endSetup();
