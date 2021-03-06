<?php

class Magestore_TruWallet_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
//		$customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
//		if($customer->getId())
//			Mage::helper('truwallet/transaction')->checkCreditFromSharing($customer);

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
				`recipient_id` INT unsigned,
                `point_back` FLOAT,
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
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            Mage::getSingleton('core/session')->addError(
                Mage::helper('truwallet')->__('You have to log in before sharing truWallet.')
            );
            $this->_redirectUrl(Mage::getUrl('customer/account/login/'));
        }

		$this->loadLayout();
		$this->_title(Mage::helper('truwallet')->__('Share TruWallet Money'));
		$this->renderLayout();
	}

	public function synchFebAction()
	{
		Mage::helper('truwallet')->synchFeb();
	}

	public function synchGiftCardAction()
	{
		Mage::helper('truwallet')->synchGiftCard();
	}

	public function synchTransferAction()
	{
		Mage::helper('truwallet')->synchTransfer();
	}

	public function synchPurchaseGiftCardAction()
	{
		Mage::helper('truwallet')->synchPurchaseGiftCard();
	}

	public function synchOrderAction()
	{
		Mage::helper('truwallet')->synchOrder();
	}

	public function synchTruwalletAction()
	{
		Mage::helper('truwallet/account')->updateCredit(6053, -500);
		echo 'success';
	}

	public function addSalesAction()
	{
		$setup = new Mage_Core_Model_Resource_Setup('core_setup');
		$installer = $setup;
		$installer->startSetup();
		$installer->getConnection()->addColumn($setup->getTable('sales/order'), 'created_by', 'tinyint NULL default 2');
		$installer->endSetup();
		echo "success";
	}

    public function addRefundAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("");

        if (version_compare(Mage::getVersion(), '1.4.1.0', '>=')) {
            $installer->getConnection()->addColumn($setup->getTable('sales/invoice'), 'truwallet_earn', 'decimal(12,4) NOT NULL default 0');
            $installer->getConnection()->addColumn($setup->getTable('sales/creditmemo'), 'truwallet_earn', 'decimal(12,4) NOT NULL default 0');
        } else {
            $setup = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
            $setup->addAttribute('invoice', 'truwallet_earn', array('type' => 'decimal(12,4)'));
            $setup->addAttribute('creditmemo', 'truwallet_earn', array('type' => 'decimal(12,4)'));
        }
        $installer->endSetup();
        echo "success";
    }

	public function checkAction()
	{
		Mage::helper('truwallet/transaction')->checkExpiryDateTransaction();
	}

	public function testEmailAction()
	{
		var_dump($_SERVER['REMOTE_ADDR']);
		$msg = "First line of text\nSecond line of text";
		$msg = wordwrap($msg,70);
		zend_debug::dump(mail("longvuxuan1989@gmail.com","My subject",$msg));
	}

	public function dbAction()
	{
		Mage::helper('truwallet/db')->getCronTableData();
	}

	public function customerAction()
	{
		$customers = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToSelect('*')
			->setOrder('entity_id','desc')
		;

		foreach ($customers as $customer)
		{
			if($customer->getPhoneNumber() != null)
				zend_debug::dump($customer->getName() .' - '. $customer->getPhoneNumber());
		}
	}

	public function plasticAction()
	{
		$collection = Mage::helper('truwallet/giftcard')->getPlasticGiftCards();
		$_collection = Mage::helper('truwallet/giftcard')->plasticInCart();
		echo (Mage::getStoreConfig('truwallet/spend/background_color',Mage::app()->getStore()));
		echo '<br />';
		echo (Mage::getStoreConfig('truwallet/spend/text_color',Mage::app()->getStore()));
		zend_debug::dump($collection);
		zend_debug::dump($_collection);

	}

    public function updateDb2Action(){
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
              ALTER TABLE {$setup->getTable('truwallet/transaction')} ADD recipient_id int(10) unsigned;
              ALTER TABLE {$setup->getTable('truwallet/transaction')} ADD point_back FLOAT;
              ALTER TABLE {$setup->getTable('truwallet/transaction')} ADD order_filter_ids text;
        ");
        $installer->endSetup();
        echo "success";
    }

}
