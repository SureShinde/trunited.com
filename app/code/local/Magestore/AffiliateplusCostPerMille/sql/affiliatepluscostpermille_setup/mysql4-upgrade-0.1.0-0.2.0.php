<?php

$installer = $this;

$installer->startSetup();

// Convert from old configuration to new configuration fields
$movingPre = 'affiliateplus/';
$movingMap = array(
    'costpermille/enable'   => 'commission/costpermille_enable',
    'costpermille/commission_per_thousand_impressions'  => 'commission/commission_per_thousand_impressions',
);

$movingSql = '';
foreach ($movingMap as $moveFrom => $moveTo) {
    $movingSql .= "UPDATE {$this->getTable('core/config_data')} ";
    $movingSql .= "SET path = '" . $movingPre . $moveTo . "' ";
    $movingSql .= "WHERE path = '" . $movingPre . $moveFrom . "'; ";
}
$installer->run($movingSql);

$installer->endSetup();
