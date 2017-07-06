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

class Indies_Recurringandrentalpayments_Model_Recurringandrentalpaymentscron extends Varien_Object
{

    protected static $isRun;
    protected static $subscriptionIterated;

    public static $isCronSession = null;

    /**
     * Run cron jobs
     * @return
     */
    public function autoorder()
    {
		if (!self::$isRun) {
			self::$isCronSession = 1;
			$this->sentPayflowUpdateTokenNotification();
			$this->payflowUpdatePnrefToken();
          //  if (!self::cleanState())  // We decide to not capture overdue subscription so comment this
			self::processTodaySubscriptions();
            self::processAlerts();
            self::markExpiredSubscriptions();
            self::$isRun = 1;
            self::$isCronSession = 0;
        }
    }


    /**
     * Processes all today subscriptions
     * @return
     */
    public function processTodaySubscriptions()
    {
        // Get all active subscriptions	
        foreach ($this->getTodayPendingSubscriptions() as $subscription) {	
            $subscription->payForDate(new Zend_Date);
        }
    }

    /**
     * Returns all active subscriptions for today
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection
     */
    public function getTodayPendingSubscriptions()
    {
		$collection = Mage::getModel('recurringandrentalpayments/subscription')
                ->getCollection()
                ->addActiveFilter()
                ->addTodayFilter();
        return $collection;
    }
 
    /**
     * Sends alerts matching rules
     * @return
     */
	
    public function processAlerts()
    {
		if( Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_REMINDER) == 1)
		{
				$event = Mage::getModel('recurringandrentalpayments/alert_event');
				$date_start = new Zend_Date( date(now()),Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
				$reminderBeforeDays = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_REMINDER_BEFORE_DAYS);
		
				$date_start->addDay($reminderBeforeDays);
				
				$sequences = Mage::getModel('recurringandrentalpayments/sequence')->getCollection()
							->setOrder('date', 'ASC')
							->addFieldToFilter('mailsent',0)
							->addFieldToFilter('date',$date_start->toString('yyyy-MM-dd'));	
			
				$sequence_id=-1;	
				foreach ($sequences as $sequence) {
					$subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($sequence->getSubscriptionId());
					if($subscription->getId() != $sequence_id)
					{
						$sequence_id = $subscription->getId();
						if( $sequence->getMailsent()!=1 && (Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_REMINDER) == '1') && $subscription->getStatus() == 1)
						{
							$event->send($subscription,
							 Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_REMINDER_TEMPLATE),
							 0,
							 Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_REMINDER_SENDER),
							 Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_REMINDER_CC_TO));
							$sequence->setMailsent(1)->save();
						}
					}
				}
		 }
    }	

    /**
     * Gets expired subscriptions and marks them as expired
     * @return
     */
    public function markExpiredSubscriptions()
    {
        $collection = Mage::getModel('recurringandrentalpayments/subscription')
                ->getCollection()
                ->addActiveFilter();
		
		/* Start : Date : add for send subscription expiration reminder email */
		$event = Mage::getModel('recurringandrentalpayments/alert_event');
		$today_date = new Zend_Date( Mage::getModel('core/date')->date('Y-m-d H:i:s'),Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
		$reminderBeforeDays =  Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_EXPIRY_REMINDER_BEFORE_DAYS);
		$today_date->addDay($reminderBeforeDays);
		/* End : Date : add for send subscription expiration reminder email */
		
		foreach ($collection as $Subscription)
		{
			if($Subscription->getDateExpire()->compare($today_date) == 0 && $Subscription->getExpirymail() == 0) 
			{
				/* This email is for Expiry Reminder  . */
				$event->send($Subscription,
							 Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_EXPIRY_REMINDER_EMAIL_TEMPLATE),
							 0,
							 Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_EXPIRY_REMINDER_EMAIL_SENDER),
							 Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_EXPIRY_REMINDER_EMAIL_CC_TO));		
			
				$collection = Mage::getModel('recurringandrentalpayments/subscription_flat')->load($Subscription->getSubscriptionId(),'subscription_id');
				$collection->setExpirymail(1)->save();
			}
			/* This email is for sent notification about recently expired any subscriptions . */
			
            if ($Subscription->getDateExpire()->compare(new Zend_Date, Zend_Date::DATE_SHORT) <= 0) {
                try {
                    throw new Indies_Recurringandrentalpayments_Exception("Subscription {$Subscription->getId()} marked as expired(" . $Subscription->getDateExpire() . ")");
                } catch (exception $e) {
                }
                $Subscription->setStatus(Indies_Recurringandrentalpayments_Model_Subscription::STATUS_EXPIRED)->save();
    			
				$event->send($Subscription,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_EXPIRE_TEMPLATE),
				0,
				Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_SENDER),
				Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_CC_TO));	
		    }
        }
    }

    /**
     * searches for overdued
     * Returns true if any changes has been made
     * @return bool
     */
    public function cleanState()
    {
		$today = new Zend_Date;
        $collection = Mage::getModel('recurringandrentalpayments/subscription')
                ->getCollection()
                ->addLessTodayFilter()
                ->addActiveFilter();
  		foreach ($collection as $subscription)
        {
			 $coll = Mage::getModel('recurringandrentalpayments/sequence')
                    ->getCollection()
                    ->addSubscriptionFilter($subscription)
                    ->addStatusFilter(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PENDING);


            foreach ($coll as $sequence)
            {
				$past = new Zend_Date($sequence->getDate(), Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
                if ($past->compare($today, Zend_Date::DATE_SHORT) < 0) {
                    $subscription->updateSequences(); 
                    $result = $subscription->payBySequence($sequence);
                    return $result; /* only one fix on each cron execution */
                }
            }
        }
        return false;
    }
	public function sentPayflowUpdateTokenNotification()
	{
		$sent_notification_before_days = Mage::getStoreConfig('recurringandrentalpayments/recurring_payflow_settings/update_token_notification');
		$update_token_before_days = Mage::getStoreConfig('recurringandrentalpayments/recurring_payflow_settings/update_token');
		if ($sent_notification_before_days != '' && $update_token_before_days != '')
		{
			$total = $sent_notification_before_days + $update_token_before_days;

			$sent_mail_for_date = new Zend_Date(Mage::getModel('core/date')->date('Y-m-d H:i:s'),Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
			$sent_mail_for_date->addDay($total);


			$update_on = new Zend_Date(Mage::getModel('core/date')->date('Y-m-d H:i:s'),Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
			$update_on->addDay($sent_notification_before_days);

			$collection = Mage::getModel('recurringandrentalpayments/subscription')
					->getCollection()
					->addFieldToFilter('status',1);
			$event = Mage::getModel('recurringandrentalpayments/alert_event');	

			foreach ($collection as $Subscription) 
			{
				$order = Mage::getModel('sales/order')->load($Subscription->getParentOrderId(), 'increment_id');
				$payment_method = $order->getPayment()->getMethodInstance()->getCode();
				if ($payment_method == 'verisign')
				{	
					$pnref_date = new Zend_Date($Subscription->getPnrefExpiryDate(), Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);

					$Subscription->setUpdateondate($update_on->toString('yyyy-MM-dd'));
					if($pnref_date->compare($sent_mail_for_date,Zend_Date::DATE_SHORT) == 0 && 
					   $Subscription->getPnrefMailSent() == 0 && 
					   $Subscription->getStatus() == 1 /*&&
					   $pnref_date->compare($sent_mail_for_date,Zend_Date::DATE_SHORT) > 1*/
					  ) 
					{
						$event->send($Subscription,
									 Mage::getStoreConfig('recurringandrentalpayments/recurring_payflow_settings/notification_email_template'),
									 0,
									 Mage::getStoreConfig('recurringandrentalpayments/recurring_payflow_settings/notification_email_sender'),
									 Mage::getStoreConfig('recurringandrentalpayments/recurring_payflow_settings/notification_email_cc_to'));		

						$Subscription->setPnrefMailSent(1)->save();
					}
				}
			}
		}
	}
	public function payflowUpdatePnrefToken()
	{
		$update_token_before_days = Mage::getStoreConfig('recurringandrentalpayments/recurring_payflow_settings/update_token');
		if($update_token_before_days != '')
		{
			$update_token_date = new Zend_Date( Mage::getModel('core/date')->date('Y-m-d H:i:s'),Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
			$update_token_date->addDay($update_token_before_days);
			$collection = Mage::getModel('recurringandrentalpayments/subscription')
					->getCollection()
					->addFieldToFilter('status',1);
		
			foreach ($collection as $Subscription) 
			{
				
				$order = Mage::getModel('sales/order')->load($Subscription->getParentOrderId(), 'increment_id');
				$payment_method = $order->getPayment()->getMethodInstance()->getCode();
				if ($payment_method == 'verisign')
				{
					$pnref_date = new Zend_Date($Subscription->getPnrefExpiryDate(), Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
					$sub_expiry_date = new Zend_Date($Subscription->getDateExpire(), Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);

					if($pnref_date->compare($update_token_date,Zend_Date::DATE_SHORT) == 0 && 
					   $Subscription->getStatus() == 1 &&
					   $sub_expiry_date->compare($update_token_date,Zend_Date::DATE_SHORT) >= 1 ) 
					{
						$service = Mage::getModel('recurringandrentalpayments/web_service_Verisign_client');
						$amount  = 1.00;

						$result = array();
						$request = $service->buildBasicRequest();
						$request->setTrxtype('A');
						$request->setTender('C');
						$request->setOrigid($Subscription->getPayflowPnrefId());
						$request->setAmt($amount);
						$request->setComment1('Update token call for order '.$Subscription->getParentOrderId());
						$request->setCustref($Subscription->getCustomerId());

						$response = $service->postRequest($request);
						unset($request);
						$service->processErrors($response);

						Zend_Date::setOptions(array('extend_month' => true)); // Fix Zend_Date::addMonth unexpected result
						$magento_date = (Mage::getModel('core/date')->date('Y-m-d'));
						$pnref_expireDate = date('Y-m-d', strtotime("+1 years", strtotime($magento_date)));

						$void = 0;


						switch ($response->getResultCode()){
							case 0:
								$void = 1;
								$Subscription
								->setPayflowPnrefId($response->getPnref())	
								->setPnrefExpiryDate($pnref_expireDate)
								->save();
								break;
							case 126:		//RESPONSE_CODE_FRAUDSERVICE_FILTER
								$void = 1;
								$Subscription
								->setPayflowPnrefId($response->getPnref())	
								->setPnrefExpiryDate($pnref_expireDate)
								->save();
								break;
						}
						/* We will immediately void above transaction and save new pnref id
						   in database */
						if($void == 1)
						{
							$request = $service->buildBasicRequest();
							$request->setTrxtype('V');
							$request->setTender('C');
							$request->setOrigid($Subscription->getPayflowPnrefId());
							$request->setAmt($amount);
							$request->setComment1('Void token call for order '.$Subscription->getParentOrderId());
							$request->setCustref($Subscription->getCustomerId());
							$response = $service->postRequest($request);
							$service->processErrors($response);

						}
					}
				}
			}
		}
	}
}
