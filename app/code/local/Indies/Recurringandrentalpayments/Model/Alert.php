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

class Indies_Recurringandrentalpayments_Model_Alert extends Mage_Core_Model_Abstract
{
    const TYPE_DATE_START          = 'date_start';
    const TYPE_DELIVERY            = 'delivery';
    const TYPE_DATE_EXPIRE         = 'date_expire';
    const TYPE_NEW_SUBSCRIPTION    = 'new_subscription';
    const TYPE_CANCEL_SUBSCRIPTION = 'cancel_subscription';
    const TYPE_ACTIVATION          = 'activation';
    const TYPE_SUSPENDED           = 'suspended';

    const MULTIPLIER_DAY = 24;

    const RECIPIENT_CUSTOMER = 'customer';
    const RECIPIENT_ADMIN    = 'admin';

    const SUBMIT_WITHOUT_DELETE = 1;

    /** Status enabled */
    const STATUS_ENABLED = 1;
    /** Status disabled */
    const STATUS_DISABLED = 0;

    protected function _construct()
    {
        $this->_init('recurringandrentalpayments/alert');
    }

    /**
     * Returns events
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Alert_Event_Collection
     */
    public function getEvents()
    {
        if (!$this->getData('events')) {
            $this->setData('events', Mage::getModel('recurringandrentalpayments/alert_event')->getCollection()->addAlertFilter($this));
        }
        return $this->getData('events');
    }

    /**
     * Return identity code for sender
     * @return string
     */
    public function getSender()
    {
        return true;
    }

    protected function _getDate($timeAmount, Zend_Date $date)
    {

        $nowDate = new Zend_Date();
        $dateWithTimeAmount = $date->addHour($timeAmount);

        if ($timeAmount == 0) {

            $date = $date->addMinute(1);
        } elseif ($timeAmount < 0 && $nowDate->isLater($dateWithTimeAmount)) {

            $date = $nowDate->addMinute(1);
        } else {

            $date = $dateWithTimeAmount;
        }
        return $date;
    }

    /**
     * Generates events for one subscription
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @param object                     $woDelete [optional]
     * @return Indies_Recurringandrentalpayments_Model_Alert
     */
    public function generateSubscriptionEvents(Indies_Recurringandrentalpayments_Model_Subscription $Subscription, $woDelete = null)
    {
        // Purge events before
        if (!$Subscription->getId()) {
            return $this;
		}
		$suffix =  -1 * 24 * 1 ;
		$timeAmount = $suffix;
        switch ('date_start') {
            case self::TYPE_DATE_START:
                if ($Subscription->isActive()) {
                    $date = $Subscription->getDateStart();
                    $date = $this->_getDate($timeAmount, $date);
                }
                break;

            case self::TYPE_DATE_EXPIRE:
                if (($Subscription->getIsExpiring() || $Subscription->isActive()) && !$Subscription->isInfinite()) {
                    if ($Subscription->isActive()) {
                        $date = $Subscription->getDateExpire();
                        $date = $this->_getDate($timeAmount, $date);
                    } else {
                        $date = new Zend_Date;
                    }
                }
                break;
            case self::TYPE_NEW_SUBSCRIPTION:
                if ($Subscription->getIsNew()) {
                    $date = new Zend_Date;
                    $date = $this->_getDate($timeAmount, $date);
                } else {

                }
                break;
            case self::TYPE_SUSPENDED:
                if ($Subscription->getIsSuspending()) {
                    $date = new Zend_Date;
                    $date = $this->_getDate($timeAmount, $date);
                } else {

                }
                break;
            case self::TYPE_ACTIVATION:
                if ($Subscription->getIsReactivated()) {
                    $date = new Zend_Date;
                    $date = $this->_getDate($timeAmount, $date);
                } else {

                }
                break;

            case self::TYPE_CANCEL_SUBSCRIPTION:
                if ($Subscription->getIsCancelling()) {
                    $date = new Zend_Date;
                    $date = $this->_getDate($timeAmount, $date);
                } else {

                }
                break;
            default:
                throw new Indies_Recurringandrentalpayments_Exception("Alert for events type '{$this->getType()}' are not supported");
        }
        if (isset($date) && ($date instanceof Zend_Date) && ($date->compare(new Zend_Date) > 0)) {

			$Event = Mage::getModel('recurringandrentalpayments/alert_event')
                    ->setSubscriptionId($Subscription->getId())
                    ->setRecipient($this->getRecipientAddress($Subscription))
                    ->setAlertId(1)
                    ->setDate($date->toString(Indies_Recurringandrentalpayments_Model_Subscription::DB_DATETIME_FORMAT))
				    ->save();
        }
        return $this;
    }

    /**
     * Generates events for all subscriptions
     * @return Indies_Recurringandrentalpayments_Model_Alert
     */
    protected function _generateEvents()
    {
        // Purge events before
        if ($this->getId() &&
            ($this->getType() != self::TYPE_NEW_SUBSCRIPTION) &&
            ($this->getType() != self::TYPE_CANCEL_SUBSCRIPTION)
        ) {
            $this->getResource()->getWriteAdapter()->delete($this->getResource()->getTable('recurringandrentalpayments/alert_event'), 'alert_id=' . $this->getId());
        }
        $Subscriptions = Mage::getModel('recurringandrentalpayments/subscription')->getCollection()->addActiveFilter();
        foreach ($Subscriptions as $Subscription) {
            $this->generateSubscriptionEvents($Subscription, self::SUBMIT_WITHOUT_DELETE);
        }
        return $this;
    }

    /**
     * Returns recipient email
     * @return string
     */
    public function getRecipientAddress(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
		$Subscription->getCustomer()->getEmail();
    }

    /**
     * Returns recipient name
     * @return string
     */
    public function getRecipientName(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
        if ($this->getRecipient() == self::RECIPIENT_CUSTOMER) {
            return $Subscription->getCustomer()->getName();
        } else {
            return Mage::getStoreConfig('trans_email/ident_' . $this->getRecipient() . '/name');
        }
        return $this;
    }

    public function _afterSave()
    {
		$this->_generateEvents();
        return parent::_afterSave();
    }
}

