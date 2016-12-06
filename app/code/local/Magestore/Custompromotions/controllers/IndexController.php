<?php

class Magestore_Custompromotions_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		var_dump(Mage::helper('custompromotions')->isRunPromotions());
		$this->loadLayout();
		$this->renderLayout();
	}

	public function installDbAction() {
		$setup = new Mage_Core_Model_Resource_Setup();
		$installer = $setup;
		$installer->startSetup();
		$installer->run("
			DROP TABLE IF EXISTS {$setup->getTable('custompromotions')};
			CREATE TABLE {$setup->getTable('custompromotions')} (
			  `custompromotions_id` int(11) unsigned NOT NULL auto_increment,
			  `customer_id` int(11) unsigned NOT NULL,
			  `affiliate_id` int(11) unsigned NULL,
			  `register_amount` decimal(10,2),
			  `referred_amount` decimal(10,2),
			  `current_step` int(11) NULL ,
			  `title` varchar(255) NULL default '',
			  `type` int(11) NULL,
			  `order_id` int(11) unsigned NULL,
			  `status` smallint(6) NULL default '0',
			  `created_time` datetime NULL,
			  `updated_time` datetime NULL,
			  PRIMARY KEY (`custompromotions_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		$installer->endSetup();
		echo "success";
	}
	
	public function addTransactionAction()
	{
		$order_id = '100020269';
		$order = Mage::getModel('sales/order')->load($order_id,'increment_id');
	
		Mage::helper('custompromotions')->addTruWalletFromProduct($order);
	}

	public function notifyAction()
	{
		Mage::getSingleton('core/session')->addNotice(Mage::helper('custompromotions/configuration')->getNotifyMessage());
		$this->_redirectUrl(Mage::getUrl('checkout/cart/'));
	}

}