<?php

class Magestore_TruGiftCard_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
		if($customer->getId())
			Mage::helper('trugiftcard/transaction')->checkCreditFromSharing($customer);

		$this->loadLayout();
		$this->_title(Mage::helper('trugiftcard')->__('My truGiftCard'));
		$this->renderLayout();
	}

	public function updateDbAction(){
		$setup = new Mage_Core_Model_Resource_Setup();
		$installer = $setup;
		$installer->startSetup();
		$installer->run("
			  DROP TABLE IF EXISTS {$setup->getTable('trugiftcard/customer')};
			  DROP TABLE IF EXISTS {$setup->getTable('trugiftcard/transaction')};

			  CREATE TABLE {$setup->getTable('trugiftcard/customer')} (
				`trugiftcard_id` int(11) unsigned NOT NULL auto_increment,
				`customer_id` int(10) unsigned NOT NULL,
				`trugiftcard_credit` DECIMAL(10,2) unsigned NOT NULL default 0,
				`created_time` datetime NULL,
				`updated_time` datetime NULL,
				PRIMARY KEY (`trugiftcard_id`)
			  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			  CREATE TABLE {$setup->getTable('trugiftcard/transaction')} (
				 `transaction_id` int(10) unsigned NOT NULL auto_increment,
				`trugiftcard_id` int(10) unsigned NULL,
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

	public function updateDb2Action(){
		$setup = new Mage_Core_Model_Resource_Setup();
		$installer = $setup;
		$installer->startSetup();
		$installer->run("");

		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_earn', 'int(11) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_spent', 'int(11) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_base_discount', 'decimal(12,4) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_discount', 'decimal(12,4) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_base_amount', 'decimal(12,4) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_amount', 'decimal(12,4) NOT NULL default 0');

		$installer->getConnection()->addColumn($setup->getTable('sales/order_item'), 'trugiftcard_earn', 'int(11) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/order_item'), 'trugiftcard_spent', 'int(11) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/order_item'), 'trugiftcard_base_discount', 'decimal(12,4) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/order_item'), 'trugiftcard_discount', 'decimal(12,4) NOT NULL default 0');

		$installer->getConnection()->addColumn($setup->getTable('sales/invoice'), 'trugiftcard_base_discount', 'decimal(12,4) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/invoice'), 'trugiftcard_discount', 'decimal(12,4) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/creditmemo'), 'trugiftcard_base_discount', 'decimal(12,4) NOT NULL default 0');
		$installer->getConnection()->addColumn($setup->getTable('sales/creditmemo'), 'trugiftcard_discount', 'decimal(12,4) NOT NULL default 0');

		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'base_trugiftcard_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'base_trugiftcard_discount_for_shipping', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_discount_for_shipping', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'base_trugiftcard_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'base_trugiftcard_shipping_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'trugiftcard_shipping_hidden_tax', 'decimal(12,4) NULL');

		$installer->getConnection()->addColumn($setup->getTable('sales/invoice'), 'trugiftcard_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/invoice'), 'base_trugiftcard_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/invoice'), 'base_trugiftcard_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/invoice'), 'trugiftcard_hidden_tax', 'decimal(12,4) NULL');

		$installer->getConnection()->addColumn($setup->getTable('sales/creditmemo'), 'trugiftcard_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/creditmemo'), 'base_trugiftcard_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/creditmemo'), 'base_trugiftcard_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/creditmemo'), 'trugiftcard_hidden_tax', 'decimal(12,4) NULL');

		$installer->getConnection()->addColumn($setup->getTable('sales/order_item'), 'trugiftcard_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order_item'), 'base_trugiftcard_discount', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order_item'), 'base_trugiftcard_hidden_tax', 'decimal(12,4) NULL');
		$installer->getConnection()->addColumn($setup->getTable('sales/order_item'), 'trugiftcard_hidden_tax', 'decimal(12,4) NULL');
		
		$installer->endSetup();
		echo "success";
	}


	public function transactionsAction(){
		$this->loadLayout();
		$this->_title(Mage::helper('trugiftcard')->__('truGiftCard Transactions'));
		$this->renderLayout();
	}

	public function shareTruGiftCardAction(){
		$this->loadLayout();
		$this->_title(Mage::helper('trugiftcard')->__('Share TruGiftCard Money'));
		$this->renderLayout();
	}
}
