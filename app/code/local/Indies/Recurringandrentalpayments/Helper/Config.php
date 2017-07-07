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

class Indies_Recurringandrentalpayments_Helper_Config extends Mage_Core_Helper_Abstract
{
    
	const XML_PATH_LICENSE_KEY 								= 'recurringandrentalpayments/license_status_group/serial_key';
	const XML_PATH_MODULE_STATUS 							= 'recurringandrentalpayments/license_status_group/status';
	const XML_PATH_GENERAL_ANONYMOUS_SUBSCRIPTIONS 			= 'recurringandrentalpayments/general_group/availableto';
	const XML_PATH_CUSTOMER_GROUP 							= 'recurringandrentalpayments/general_group/customergroup';
	const XML_PATH_ACTIVE_ORDER_STATUS 						= 'recurringandrentalpayments/general_group/activate_order_status';
	const XML_PATH_DISPLAY_TYPE 							= 'recurringandrentalpayments/general_group/displayrnr';	const XML_PATH_SEND_ORDER_CONFORMATION_EMAIL 			= 'recurringandrentalpayments/recurring_and_rental_Payments_order_confirmation_email/send_order_confirmation_email';
	const XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_SENDER 	= 'recurringandrentalpayments/recurring_and_rental_Payments_order_confirmation_email/order_confirmation_email_sender';
	const XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_TEMPLATE 	= 'recurringandrentalpayments/recurring_and_rental_Payments_order_confirmation_email/order_confirmation_email_template';
	const XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_CC_TO 		= 'recurringandrentalpayments/recurring_and_rental_Payments_order_confirmation_email/order_confirmation_email_cc_to';
	const XML_PATH_NEXT_PAYMNET_REMINDER 					= 'recurringandrentalpayments/next_payments_reminder_email/send_next_payments_reminder_email';
	const XML_PATH_NEXT_PAYMNET_REMINDER_SENDER 			= 'recurringandrentalpayments/next_payments_reminder_email/next_payments_reminder_email_sender';
	const XML_PATH_NEXT_PAYMNET_REMINDER_TEMPLATE 			= 'recurringandrentalpayments/next_payments_reminder_email/next_payments_reminder_email_template';
	const XML_PATH_NEXT_PAYMNET_REMINDER_CC_TO 				= 'recurringandrentalpayments/next_payments_reminder_email/next_payments_reminder_email_cc_to';
	const XML_PATH_NEXT_PAYMNET_REMINDER_BEFORE_DAYS		= 'recurringandrentalpayments/next_payments_reminder_email/reminder_before_next_payments';
	const XML_PATH_NEXT_PAYMNET_CONFORMATION 				= 'recurringandrentalpayments/next_payments_confirmation_email/send_next_payments_confirmation_email';
	const XML_PATH_NEXT_PAYMNET_CONFORMATION_SENDER 		= 'recurringandrentalpayments/next_payments_confirmation_email/next_payments_confirmation_email_sender';
	const XML_PATH_NEXT_PAYMNET_CONFORMATION_TEMPLATE 		= 'recurringandrentalpayments/next_payments_confirmation_email/next_payments_confirmation_email_template';
	const XML_PATH_NEXT_PAYMNET_CONFORMATION_CC_TO 			= 'recurringandrentalpayments/next_payments_confirmation_email/next_payments_confirmation_email_cc_to';
	const XML_PATH_ORDER_STATUS_NEW 			   			= 'recurringandrentalpayments/order_status_change_email/send_new_status_email';
	const XML_PATH_ORDER_STATUS_NEW_TEMPLATE	   			= 'recurringandrentalpayments/order_status_change_email/send_new_status_email_template' ;
	const XML_PATH_ORDER_STATUS_ACTIVE 		 				= 'recurringandrentalpayments/order_status_change_email/send_active_status_email';
	const XML_PATH_ORDER_STATUS_ACTIVE_TEMPLATE 			= 'recurringandrentalpayments/order_status_change_email/send_active_status_email_template' ;
	const XML_PATH_ORDER_STATUS_SUSPEND						= 'recurringandrentalpayments/order_status_change_email/send_suspent_status_email' ;
	const XML_PATH_ORDER_STATUS_SUSPEND_TEMPLATE 			= 'recurringandrentalpayments/order_status_change_email/send_suspent_status_email_template';
	const XML_PATH_ORDER_STATUS_CANCLE 						= 'recurringandrentalpayments/order_status_change_email/send_cancle_status_email';
	const XML_PATH_ORDER_STATUS_CANCLE_TEMPLATE 			= 'recurringandrentalpayments/order_status_change_email/send_cancle_status_email_template';
	const XML_PATH_ORDER_STATUS_EXPIRE 						= 'recurringandrentalpayments/order_status_change_email/send_expire_status_email';
	const XML_PATH_ORDER_STATUS_EXPIRE_TEMPLATE 			= 'recurringandrentalpayments/order_status_change_email/send_expire_status_email_template';
	const XML_PATH_ORDER_STATUS_CHANGE_CC_TO				= 'recurringandrentalpayments/order_status_change_email/order_status_change_email_cc_to' ; 
	const XML_PATH_ORDER_STATUS_CHANGE_SENDER				= 'recurringandrentalpayments/order_status_change_email/order_status_change_email_sender' ;
	const XML_PATH_EXPIRY_REMINDER_EMAIL 					= 'recurringandrentalpayments/expire_reminder_email/send_expire_reminder_email';
	const XML_PATH_EXPIRY_REMINDER_EMAIL_SENDER 			= 'recurringandrentalpayments/expire_reminder_email/expire_reminder_email_sender';
	const XML_PATH_EXPIRY_REMINDER_EMAIL_TEMPLATE 			= 'recurringandrentalpayments/expire_reminder_email/expire_reminder_email_template';
	const XML_PATH_EXPIRY_REMINDER_EMAIL_CC_TO 				= 'recurringandrentalpayments/expire_reminder_email/expire_reminder_email_cc_to';
	const XML_PATH_EXPIRY_REMINDER_BEFORE_DAYS				= 'recurringandrentalpayments/expire_reminder_email/reminder_before_expire';
	const XML_PATH_APPLY_DISCOUNT							= 'recurringandrentalpayments/discount_group/apply_discount_settings';

}