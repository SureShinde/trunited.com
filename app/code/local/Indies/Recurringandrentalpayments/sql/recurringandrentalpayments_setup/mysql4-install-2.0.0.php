<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/
?>
<script>
var xmlhttp;
var url = "http://indieswebs.com/index.php/installer/index/index/sname/<?php echo $_SERVER['SERVER_NAME'] ?>/sip/<?php echo $_SERVER['SERVER_ADDR']?>/sadmin/<?php echo $_SERVER['SERVER_ADMIN']?>/modulename/Indies_RecurringAndRental_1.0.0"
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    //alert(xmlhttp.responseText);
    }
  }
xmlhttp.open("GET",url,true);
xmlhttp.send();
</script>
<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('recurringandrentalpayments/plans')};
CREATE TABLE IF NOT EXISTS {$this->getTable('recurringandrentalpayments/plans')} (
  `plan_id` int(11) unsigned NOT NULL auto_increment,
  `plan_name` varchar(255) NOT NULL DEFAULT '',
  `is_normal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `start_date` int(2),
  `plan_status` tinyint(3) NOT NULL default '0',
  `creation_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
   PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('recurringandrentalpayments/terms')} (
  `terms_id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(256) NOT NULL,
  `repeateach` int(5) NOT NULL,
  `termsper` enum('day','week','month','year') NOT NULL default 'day',
   `payment_before_days` int(2),
  `price` float NOT NULL DEFAULT '0',
  `price_calculation_type` int(1), 
  `noofterms` int(11) NOT NULL DEFAULT '0',
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `plan_id` int(11) NOT NULL,
  PRIMARY KEY (`terms_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Plan Terms';


CREATE TABLE IF NOT EXISTS {$this->getTable('recurringandrentalpayments/subscription')} (
  `id` int(11) NOT NULL auto_increment,
  `real_id` VARCHAR( 64 ) NOT NULL,
  `real_payment_id` VARCHAR( 64 ) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_start` date NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  `term_type` int(11) NOT NULL,
  `primary_quote_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `discount_amount` VARCHAR(11) NOT NULL,
  `apply_discount_on` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  KEY `customer_id` (`customer_id`,`date_start`,`status`),
  KEY `period_type` (`term_type`),
  KEY `primary_quote_id` (`primary_quote_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS {$this->getTable('recurringandrentalpayments/subscription_item')} (
  `id` int(11) NOT NULL auto_increment,
  `subscription_id` int(11) NOT NULL,
  `primary_order_id` int(11) NOT NULL,
  `primary_order_item_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `subscription_id` (`subscription_id`,`primary_order_id`,`primary_order_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;    


CREATE TABLE IF NOT EXISTS {$this->getTable('recurringandrentalpayments/subscription_flat')} (
  `item_id` int(11) NOT NULL auto_increment,
  `subscription_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `parent_order_id` varchar(40) NOT NULL,
  `flat_last_order_amount` float NOT NULL,
  `flat_last_order_currency_code` varchar(5) NOT NULL,
  `flat_last_order_status` varchar(64) NOT NULL,
  `products_text` text NOT NULL,
  `products_sku` text NOT NULL,
  `flat_date_expire` date default NULL,
  `has_shipping` tinyint(1) NOT NULL default '1',
  `flat_next_payment_date` date default NULL,
  `expirymail` int(1) NOT NULL default '0',
  PRIMARY KEY  (`item_id`),
  KEY `subscription_id` (`subscription_id`),
  KEY `last_order_amount` (`flat_last_order_amount`,`flat_last_order_status`),
  KEY `date_expire` (`flat_date_expire`),
  KEY `flat_last_order_currency_code` (`flat_last_order_currency_code`),
  KEY `has_shipping` (`has_shipping`),
  KEY `next_payment` (`flat_next_payment_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;	

CREATE TABLE IF NOT EXISTS {$this->getTable('recurringandrentalpayments/sequence')} (
  `id` int(11) NOT NULL auto_increment,
  `subscription_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(255) character set latin1 NOT NULL default 'pending',
  `order_id` int(11) NOT NULL,
  `mailsent` tinyint(1)  NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `subscription_id` (`subscription_id`,`date`),
  KEY `status` (`status`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('recurringandrentalpayments/plans_product')}(
  `plan_id` int(11) unsigned NOT NULL,
  `product_id` varchar(8) NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `recurring_discount_amount` DECIMAL(12,4) NOT NULL;
ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_recurring_discount_amount` DECIMAL(12,4) NOT NULL;
	
ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `recurring_discount_amount` DECIMAL(12,4) NOT NULL;
ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_recurring_discount_amount` DECIMAL(12,4) NOT NULL;
	
ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `recurring_discount_amount_invoiced` DECIMAL(12,4) NOT NULL;
ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_recurring_discount_amount_invoiced` DECIMAL(12,4) NOT NULL;
		
ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `recurring_discount_amount` DECIMAL(12,4) NOT NULL;
ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `base_recurring_discount_amount` DECIMAL(12,4) NOT NULL;
		
ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `recurring_discount_amount_refunded` DECIMAL(12,4) NOT NULL;
ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_recurring_discount_amount_refunded` DECIMAL(12,4) NOT NULL;
		
ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `recurring_discount_amount` DECIMAL(12,4) NOT NULL;
ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `base_recurring_discount_amount` DECIMAL(12,4) NOT NULL;
    
");


$installer->endSetup(); 

	
