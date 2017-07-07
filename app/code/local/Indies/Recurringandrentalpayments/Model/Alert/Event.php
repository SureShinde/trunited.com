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

class Indies_Recurringandrentalpayments_Model_Alert_Event extends Mage_Core_Model_Abstract
{

    /** Status "Pending" for alert event */
    const STATUS_PENDING = 'pending';
    /** Status "Processing" for alert event */
    const STATUS_PROCESSING = 'processing';
    /** Status "Sent" for alert event */
    const STATUS_SENT = 'sent';
    /** Status "Failed" for alert event */
    const STATUS_FAILED = 'failed';
	const SENDER = 'general';

    public function send( Indies_Recurringandrentalpayments_Model_Subscription $subscribe ,$mode , $admin , $snd = self::SENDER ,$ccto = '' )
    { 
	    try
		{   
			$sender = $this->getEmailsender($snd);
			if ($subscribe)
			{
   				$storeId = Mage::app()->getStore()->getId();
                $mailTemplate = Mage::getModel('core/email_template');
               	//$subscribe->setData('next_payment_date', Mage::helper('core')->formatDate($subscribe->getFlatNextPaymentDate()));
				if($admin)
				{
					$mailTemplate->setDesignConfig(array('area' => 'admin', 'store' => $storeId))
							 ->sendTransactional(
								$mode,
								$sender,
								$subscribe->getCustomer()->getEmail(),
								$subscribe->getCustomer()->getName(),
								array (
										'subscription' => $subscribe,
										'alert' =>$mode
									  )
								);
				}
				else
				{		
		
					if($ccto)
					{
						$mailTemplate->addBcc($ccto);
					}
					$mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
							 ->sendTransactional(
								$mode,
								$sender,
								$subscribe->getCustomer()->getEmail(),
								$subscribe->getCustomer()->getName(),
								array (
										'subscription' => $subscribe,
										'alert' =>$mode
									  )
								);	
				}
            }
			else
			{
                $this->setStatus(self::STATUS_FAILED)->save();
            }
        }
		catch (Exception $e)
		{
            throw $e;
        }	 
    }
	public function send1($vars ,$mode , $admin , $snd = self::SENDER ,$ccto = '' )
    { 
	    try
		{   
        	$sender = $this->getEmailsender($snd);
   			$storeId = Mage::app()->getStore()->getId();
            $mailTemplate = Mage::getModel('core/email_template');
            if($ccto)
			{
				$mailTemplate->addBcc($ccto);
			}
			$mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
						 ->sendTransactional(
							$mode,
							$sender,
							$vars['email'],
							$vars['name'],
							$vars
							);	
        }
		catch (Exception $e)
		{
            throw $e;
        }	 
    }
	public function getEmailsender($data)
	{
		$sender = array(); 
		switch($data)
		{
			case 'general':
       			$sender = array(
								'name'  => Mage::getStoreConfig('trans_email/ident_general/name') ,
								'email' => Mage::getStoreConfig('trans_email/ident_general/email')
							 );
       			break;
			case 'sales':
				$sender = array(
								'name'  => Mage::getStoreConfig('trans_email/ident_sales/name') ,
								'email' => Mage::getStoreConfig('trans_email/ident_sales/email')
							 );
       			break;
			case 'support':
				$sender = array(
								'name'  => Mage::getStoreConfig('trans_email/ident_support/name') ,
								'email' => Mage::getStoreConfig('trans_email/ident_support/email')
							 );
       			break;
			case 'custom1':
				$sender = array(
								'name'  => Mage::getStoreConfig('trans_email/ident_custom1/name') ,
								'email' => Mage::getStoreConfig('trans_email/ident_custom1/email')
							 );
       			break;	
			case 'custom2':
				$sender = array(
								'name'  => Mage::getStoreConfig('trans_email/ident_custom2/name') ,
								'email' => Mage::getStoreConfig('trans_email/ident_custom2/email')
							 );
       			break;	
		}
		return $sender;		
	}
}