<?php

class Magestore_TruWallet_Model_Transaction extends Mage_Core_Model_Abstract
{
	const XML_PATH_EMAIL_ENABLE = 'truwallet/email/enable';
	const XML_PATH_EMAIL_SENDER = 'truwallet/email/sender';
	const XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER = 'truwallet/email/share_email_customer';
	const XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER = 'truwallet/email/share_email_non_customer';
	const XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE = 'truwallet/email/share_email_expiry_date';

	public function _construct(){
		parent::_construct();
		$this->_init('truwallet/transaction');
	}

	/**
	 * @param $customer_id
	 * @param $amount
	 * @param $type
	 * @param $email
	 * @param $message
	 * @return $this
	 */
	public function sendEmailWhenSharingTruWallet($type, $email, $message)
	{
		if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
			return $this;
		}

		$store = Mage::app()->getStore($this->getStoreId());
		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);
		$customer = Mage::getModel('customer/customer')->load($customer_id);

		$name = Mage::helper('truwallet')->__('There');
		$current_credit = 0;
		$link = '';
		if($type) {
			$email_path = Mage::getStoreConfig(self::XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER, $store);
			$customer_receiver = Mage::getModel("customer/customer");
			$customer_receiver->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
			$customer_receiver->loadByEmail($email);
			if($customer_receiver->getId()){
				$name = $customer_receiver->getName();
				$truWalletAccount = Mage::helper('truwallet/account')->loadByCustomerId($customer_receiver->getId());
				if($truWalletAccount->getId())
					$current_credit = $truWalletAccount->getTruwalletCredit();
			}

		} else {
			$email_path =  Mage::getStoreConfig(self::XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER, $store);
			$_sender = Mage::getModel('customer/customer')->load($customer_id);
			$link = Mage::getUrl('truwallet/transaction/register',array('email'=>$_sender->getEmail()));
		}


		$data = array(
			'store' => $store,
			'customer_name' => $name,
			'amount' => Mage::helper('core')->currency(abs($amount), true, false),
			'sender_email' => $customer->getEmail(),
			'title' => $this->getTitle(),
			'point_balance' => Mage::helper('core')->currency(abs($current_credit), true, false),
			'status' => $this->getStatusLabel(),
			'register_link' => $link,
			'message' => $message,
		);

		Mage::getModel('core/email_template')
			->setDesignConfig(array(
				'area' => 'frontend',
				'store' => Mage::app()->getStore()->getId()
			))->sendTransactional(
				$email_path,
				Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, Mage::app()->getStore()->getId()),
				$email,
				$name,
				$data
			);

		$translate->setTranslateInline(true);
		return $this;
	}

	/**
	 * get status label of transaction
	 *
	 * @return string
	 */
	public function getStatusLabel() {
		$statusHash = Magestore_TruWallet_Model_Status::getTransactionOptionArray();
		if (isset($statusHash[$this->getStatus()])) {
			return $statusHash[$this->getStatus()];
		}
		return '';
	}
}