<?php

class Magestore_TruGiftCard_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){


		$this->loadLayout();
		$this->_title(Mage::helper('trugiftcard')->__('My Trunited Gift Card'));
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
				`recipient_transaction_id` int(10) unsigned,
				`point_back` FLOAT,
				`order_filter_ids` text,
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

	public function updateDb3Action(){
		$setup = new Mage_Core_Model_Resource_Setup();
		$installer = $setup;
		$installer->startSetup();
		$installer->run("
              ALTER TABLE {$setup->getTable('trugiftcard/transaction')} ADD recipient_transaction_id int(10) unsigned;
              ALTER TABLE {$setup->getTable('trugiftcard/transaction')} ADD point_back FLOAT;
              ALTER TABLE {$setup->getTable('trugiftcard/transaction')} ADD order_filter_ids text;
        ");
		$installer->endSetup();
		echo "success";
	}


	public function transactionsAction(){
		$this->loadLayout();
		$this->_title(Mage::helper('trugiftcard')->__('Trunited Gift Card Transactions'));
		$this->renderLayout();
	}

	public function shareTruGiftCardAction(){
		if(!Mage::helper('trugiftcard')->getEnableSharing())
		{
			Mage::getSingleton('core/session')->addError(
				Mage::helper('trugiftcard')->__('The sharing Trunited Gift Card feature has been disabled.')
			);
			$this->_redirectUrl(Mage::getUrl('*/*/'));
			return;
		}
		$this->loadLayout();
		$this->_title(Mage::helper('trugiftcard')->__('Share Trunited Gift Card Money'));
		$this->renderLayout();
	}

	public function checkAction()
	{
		$helper = Mage::helper('trugiftcard/transaction');
		$collection = Mage::getModel('trugiftcard/transaction')->getCollection()
			->addFieldToFilter('action_type', Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_SHARING)
			->addFieldToFilter('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING)
			->addFieldToFilter('expiration_date', array('notnull' => true))
			->setOrder('transaction_id', 'desc')
		;

		zend_debug::dump(sizeof($collection));
		zend_debug::dump($collection->getData());

		if (sizeof($collection) > 0) {
			foreach ($collection as $transaction) {
				$expiration_date = strtotime($transaction->getExpirationDate());
				$compare_time = $helper->compareExpireDate($expiration_date, strtotime('04-08-2017 4:59:00'));

				if ($compare_time) {
					/* user still dont register an new account */
					if ($transaction->getReceiverCustomerId() == 0) {
						$helper->updateTransaction(
							$transaction,
							Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED,
							abs($transaction->getChangedCredit())
						);

						$rewardAccount = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getCustomerId());
						$rewardAccount->setTrugiftcardCredit($rewardAccount->getTrugiftcardCredit() + abs($transaction->getChangedCredit()));
						$rewardAccount->save();
					} /* User created an new account and check the amount of truGiftCard in order to return back */
					else {
						$orders = $helper->getCollectionOrderByCustomer(
							$transaction->getReceiverCustomerId(),
							strtotime($transaction->getCreatedTime()),
							$transaction
						);
						$truGiftCard_used = 0;
						$order_filter_ids = array();
						if ($orders != null && sizeof($orders) > 0) {
							foreach ($orders as $order) {
								$truGiftCard_used += $order->getTrugiftcardDiscount();
								$order_filter_ids[] = $order->getEntityId();
							}
						}

						if ($truGiftCard_used >= abs($transaction->getChangedCredit())) {
							$helper->updateTransaction(
								$transaction,
								Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED,
								$order_filter_ids
							);
						} else {
							$return_points = abs($transaction->getChangedCredit()) - $truGiftCard_used;
							$rewardAccount = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getCustomerId());
							$rewardAccount->setTrugiftcardCredit($rewardAccount->getTrugiftcardCredit() + abs($return_points));
							$rewardAccount->save();

							$receiveAccount = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getReceiverCustomerId());
							$receiveAccount->setTrugiftcardCredit($receiveAccount->getTrugiftcardCredit() - abs($return_points));
							$receiveAccount->save();

							$helper->updateTransaction(
								$transaction,
								Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED,
								$order_filter_ids,
								$return_points
							);
						}
					}
				}
			}
		}
	}


}
