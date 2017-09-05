<?php
$installer = $this;
$setup = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$setup->addAttribute('order', 'pdf_sent', array('type' => 'int', 'default' => 0, 'visible' => false));

$installer->endSetup();


