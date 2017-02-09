<?php

class Magestore_TruWallet_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
		if($customer->getId())
			Mage::helper('truwallet/transaction')->checkCreditFromSharing($customer);

		$this->loadLayout();
		$this->_title(Mage::helper('truwallet')->__('My truWallet'));
		$this->renderLayout();
	}

	public function updateDbAction(){
		$setup = new Mage_Core_Model_Resource_Setup();
		$installer = $setup;
		$installer->startSetup();
		$installer->run("
			  DROP TABLE IF EXISTS {$setup->getTable('truwallet/customer')};
			  DROP TABLE IF EXISTS {$setup->getTable('truwallet/transaction')};

			  CREATE TABLE {$setup->getTable('truwallet/customer')} (
				`truwallet_id` int(11) unsigned NOT NULL auto_increment,
				`customer_id` int(10) unsigned NOT NULL,
				`truwallet_credit` DECIMAL(10,2) unsigned NOT NULL default 0,
				`created_time` datetime NULL,
				`updated_time` datetime NULL,
				PRIMARY KEY (`truwallet_id`)
			  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			  CREATE TABLE {$setup->getTable('truwallet/transaction')} (
				 `transaction_id` int(10) unsigned NOT NULL auto_increment,
				`truwallet_id` int(10) unsigned NULL,
				`customer_id` int(10) unsigned NULL,
				`customer_email` varchar(255) NOT NULL,
				`title` varchar(255) NOT NULL,
				`action_type` smallint(5) NOT NULL default '0',
				`store_id` smallint(5) NOT NULL,
				`status` smallint(5) NOT NULL,
				`created_time` datetime NULL,
				`updated_time` datetime NULL,
				`expiration_date` datetime NULL,
				`order_id` int(10) unsigned NULL,
				`current_credit` DECIMAL(10,2) unsigned NOT NULL default 0,
				`changed_credit` DECIMAL(10,2) NOT NULL default 0,
				`receiver_email` varchar(255) NULL,
				`receiver_customer_id` INT unsigned NULL,
				PRIMARY KEY (`transaction_id`)
			  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
		$installer->endSetup();
		echo "success";
	}

	public function updateV2Action()
	{
		$setup = new Mage_Core_Model_Resource_Setup();
		$installer = $setup;
		$installer->startSetup();
		$installer->run("");

		$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'truwallet_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_truwallet_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_truwallet_discount_for_shipping', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'truwallet_discount_for_shipping', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_truwallet_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'truwallet_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_truwallet_shipping_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'truwallet_shipping_hidden_tax', 'decimal(12,4) NULL');

		$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'truwallet_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_truwallet_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'base_truwallet_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'truwallet_hidden_tax', 'decimal(12,4) NULL');

		$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'truwallet_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_truwallet_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'base_truwallet_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'), 'truwallet_hidden_tax', 'decimal(12,4) NULL');

		$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'truwallet_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_truwallet_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'base_truwallet_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'truwallet_hidden_tax', 'decimal(12,4) NULL');

		$installer->endSetup();
		echo "success";
	}

	public function synchAction()
	{
		Mage::helper('truwallet')->synchronizeCredit();
	}

	public function synchTransactionAction()
	{
		Mage::helper('truwallet')->synchronizeTransaction();
	}

	public function transactionsAction(){
		$this->loadLayout();
		$this->_title(Mage::helper('truwallet')->__('truWallet Transactions'));
		$this->renderLayout();
	}

	public function shareTruWalletAction(){
		$this->loadLayout();
		$this->_title(Mage::helper('truwallet')->__('Share TruWallet Money'));
		$this->renderLayout();
	}

}