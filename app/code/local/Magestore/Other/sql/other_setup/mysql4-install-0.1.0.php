<?php

$installer = $this;
$installer->startSetup();

/**
DROP TABLE IF EXISTS {$this->getTable('other')};
CREATE TABLE {$this->getTable('other')} (
  `other_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`other_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

*/
$installer->run("



    ");

$installer->endSetup(); 
