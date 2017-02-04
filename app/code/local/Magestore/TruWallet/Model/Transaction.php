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
}