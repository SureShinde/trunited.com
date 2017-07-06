<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('onestepcheckout_delivery')} ADD `delivery_security_code` text NULL
");
$installer->endSetup();