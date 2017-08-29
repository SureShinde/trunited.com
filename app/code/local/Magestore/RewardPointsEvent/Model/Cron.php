<?php

class Magestore_RewardPointsEvent_Model_Cron {

    /**
     * Check event active or not
     */
    public function checkActiveEvent() {
        if (!Mage::getStoreConfig('rewardpoints/eventplugin/enable'))
            return;
        $events = Mage::getModel('rewardpointsevent/rewardpointsevent')->getCollection();
        foreach ($events as $event) {
            if ($event->getStatus() == Magestore_RewardPointsEvent_Model_Status::STATUS_DISABLED) {
                $event->setIsRunning(false)
                        ->save();
                continue;
            }
            $type = $event->getRepeatType();
            if ($type == Magestore_RewardPointsEvent_Model_Repeattype::TYPE_YEAR) {
                $dateFrom = date('Y') . '-' . $event->getMonthFrom() . '-' . $event->getDaymFrom();
                $dateTo = date('Y') . '-' . $event->getMonthTo() . '-' . $event->getDaymTo();
                if ($this->compareDateEvent($dateFrom, $dateTo) > 0)
                    $dateTo = strtotime("$dateTo +1 year");
            }elseif ($type == Magestore_RewardPointsEvent_Model_Repeattype::TYPE_MONTH) {
                $dateFrom = date('Y-m') . '-' . $event->getDayFrom();
                $dateTo = date('Y-m') . '-' . $event->getDayTo();
                if ($this->compareDateEvent($dateFrom, $dateTo) > 0)
                    $dateTo = strtotime("$dateTo +1 month");
            }elseif ($type == Magestore_RewardPointsEvent_Model_Repeattype::TYPE_DAY) {
                $dayFrom = $event->getWeekFrom();
                $dayTo = $event->getWeekTo();
                $day = date('w');
                $check = false;
                if ($dayTo >= $dayFrom) {
                    if ($dayFrom <= $day && $day <= $dayTo)
                        $check = true;
                }else {
                    if ($day >= $dayFrom || $day <= $dayTo)
                        $check = true;
                }
                if ($check) {
                    $event->setIsRunning(true)
                            ->save();
                } else {
                    $event->setIsRunning(false)
                            ->setIsApply(false)
                            ->save();
                }
                continue;
            } else {
                $dateFrom = $event->getApplyFrom();
                if ($event->getApplyTo() == null)
                    $dateTo = $dateFrom;
                else
                    $dateTo = $event->getApplyTo();
            }

            if ($this->compareDateEvent($dateFrom) <= 0 && $this->compareDateEvent($dateTo) >= 0) {
                $event->setIsRunning(true)
                        ->save();
            } else {
                $event->setIsRunning(false)
                        ->setIsApply(false)
                        ->save();
            }
        }
        return $this;
    }

    /**
     * cron check pending, holding transfer -> complete, cancel transfer
     * @param type $observer
     */
    public function addEventTransaction() {
        if (!Mage::getStoreConfig('rewardpoints/eventplugin/enable'))
            return;
        //$this->checkActiveEvent();
        $array = array(Magestore_RewardPointsEvent_Model_Scope::SCOPE_GLOBAL, Magestore_RewardPointsEvent_Model_Scope::SCOPE_CUSTOMER, Magestore_RewardPointsEvent_Model_Scope::SCOPE_GROUPS, Magestore_RewardPointsEvent_Model_Scope::SCOPE_CSV);
        $customers = Mage::getModel('customer/customer')->getCollection();
        foreach ($array as $arr){
            $events = Mage::getModel('rewardpointsevent/rewardpointsevent')->getCollection()
                    ->addFieldToFilter('is_running', true)
                    ->addFieldToFilter('is_apply', false)
                    ->addFieldToFilter('customer_apply', $arr);
            if (count($events) == 0) {
                continue;
            }
            if ($arr == Magestore_RewardPointsEvent_Model_Scope::SCOPE_GLOBAL) { //all
                $customers->addNameToSelect();
            } elseif ($arr == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CUSTOMER) {//rule
                $customers  ->addAttributeToSelect('*')
                            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
                            ->joinAttribute('city', 'customer_address/city', 'default_billing', null, 'left')
                            ->joinAttribute('telephone', 'customer_address/telephone', 'default_billing', null, 'left')
                            ->joinAttribute('region_id', 'customer_address/region', 'default_billing', null, 'left')
                            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
                            ->joinAttribute('street', 'customer_address/street', 'default_billing', null, 'left')
                            ->joinAttribute('fax', 'customer_address/fax', 'default_billing', null, 'left')
                            ->joinAttribute('country_id', 'customer_address/country_id', 'default_billing', null, 'left');
            } 
            foreach ($events as $event) {
                if ($arr == Magestore_RewardPointsEvent_Model_Scope::SCOPE_GROUPS) {//select group
                    $group = explode(',', $event->getCustomerGroupIds());
                    $website = explode(',', $event->getWebsiteIds());
                    $customers = Mage::getModel('customer/customer')->getCollection()
                                ->addNameToSelect()
                                ->addFieldToFilter('group_id', array('in' => $group))
                                ->addFieldToFilter('website_id', array('in' => $website));
                }elseif ($arr == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CSV) {//import
                    $customers = Mage::getModel('rewardpointsevent/customerevent')->getCollection()
                            ->addFieldToFilter('event_id', $event->getId());
                }
                /* Get list of customer receive points */
                //$select = $event->getCustomerApply();
//                if ($select == Magestore_RewardPointsEvent_Model_Scope::SCOPE_GLOBAL) { //all
//                    $customers = Mage::getModel('customer/customer')->getCollection()
//                            ->addNameToSelect();
//                } elseif ($select == Magestore_RewardPointsEvent_Model_Scope::SCOPE_GROUPS) {//select group
//                    $group = explode(',', $event->getCustomerGroupIds());
//                    $website = explode(',', $event->getWebsiteIds());
//                    $customers = Mage::getModel('customer/customer')->getCollection()
//                            ->addNameToSelect()
//                            ->addFieldToFilter('group_id', array('in' => $group))
//                            ->addFieldToFilter('website_id', array('in' => $website));
//                } elseif ($select == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CUSTOMER) {//rule
//                    $customers = Mage::getModel('customer/customer')->getCollection()
//                            ->addAttributeToSelect('*')
//                            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
//                            ->joinAttribute('city', 'customer_address/city', 'default_billing', null, 'left')
//                            ->joinAttribute('telephone', 'customer_address/telephone', 'default_billing', null, 'left')
//                            ->joinAttribute('region_id', 'customer_address/region', 'default_billing', null, 'left')
//                            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
//                            ->joinAttribute('street', 'customer_address/street', 'default_billing', null, 'left')
//                            ->joinAttribute('fax', 'customer_address/fax', 'default_billing', null, 'left')
//                            ->joinAttribute('country_id', 'customer_address/country_id', 'default_billing', null, 'left');
//                } elseif ($select == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CSV) {//import
//                    $customers = Mage::getModel('rewardpointsevent/customerevent')->getCollection()
//                            ->addFieldToFilter('event_id', $event->getId());
//                }
                /* End get customers */

                if (!count($customers))
                    continue;
                try {
                    Mage::getResourceModel('rewardpointsevent/rewardpointsevent')->addEventTransaction($customers, $event);
                    $numberOfApply = $event->getData('apply_success');
                    $event->setData('apply_success', $numberOfApply + 1);
                    $event->setData('is_apply', true);
                    $event->save();
                } catch (Exception $e) {
                    continue;
                }
            }
        }
        return $this;
    }

    public function compareDateEvent($date1, $date2 = null) {
        if ($date2 == null)
            $date2 = now();
        $date1 = strtotime(date('Y-m-d', strtotime($date1)));
        $date2 = strtotime(date('Y-m-d', strtotime($date2)));
        if ($date1 < $date2)
            return -1;
        elseif ($date1 == $date2)
            return 0;
        else
            return 2;
    }

}
