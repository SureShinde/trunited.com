<?php

class Magestore_TruGiftCard_Model_Transaction extends Mage_Core_Model_Abstract
{
	const XML_PATH_EMAIL_ENABLE = 'trugiftcard/email/enable';
	const XML_PATH_EMAIL_SENDER = 'trugiftcard/email/sender';
	const XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER = 'trugiftcard/email/share_email_customer';
	const XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER = 'trugiftcard/email/share_email_non_customer';
	const XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE = 'trugiftcard/email/share_email_expiry_date';

	public function _construct(){
		parent::_construct();
		$this->_init('trugiftcard/transaction');
	}


}