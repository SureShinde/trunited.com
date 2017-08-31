<?php

class Magestore_SpecialOccasion_Helper_Cron extends Mage_Core_Helper_Abstract
{
    public function getConfigData($section, $field, $store = null)
    {
        return Mage::getStoreConfig('specialoccasion/'.$section.'/'.$field, $store);
    }

    public function getOccasionCollection($is_create_order = false)
    {
        $collection = Mage::getModel('specialoccasion/item')
            ->getCollection()
            ->addFieldToFilter('state', Magestore_SpecialOccasion_Model_Status::STATE_ACTIVE)
            ->setOrder('item_id', 'desc')
            ;

        if(!$is_create_order)
            $collection->addFieldToFilter('status', Magestore_SpecialOccasion_Model_Status::STATUS_ITEM_PENDING);
        else
            $collection->addFieldToFilter('status', Magestore_SpecialOccasion_Model_Status::STATUS_ITEM_PROCESSING);

        return $collection;
    }

    public function checkSendEmailRemind()
    {
        $collection = $this->getOccasionCollection();

        if($collection != null && sizeof($collection) > 0) {
            $default_date = Mage::helper('specialoccasion')->getConfigData('general', 'day_send_email');
            foreach ($collection as $item) {
                $compare_date = $this->compareTime(time(), strtotime($item->getShipDate()));

                if($default_date == $compare_date){
                    $occasion = Mage::getModel('specialoccasion/specialoccasion')->load($item->getSpecialoccasionId());
                    if($occasion != null && $occasion->getId()){
                        $customer = Mage::getModel('customer/customer')->load($occasion->getCustomerId());
                        Mage::helper('specialoccasion')->sendEmailRemind($customer, $item);
                        $item->setStatus(Magestore_SpecialOccasion_Model_Status::STATUS_ITEM_PROCESSING);
                        $item->setUpdatedAt(now());
                        $item->save();
                    }

                }
            }
        }

        echo 'success';
    }

    public function checkAndCreateOrder()
    {
        $collection = $this->getOccasionCollection(true);

        if($collection != null && sizeof($collection) > 0) {
            $default_date = Mage::helper('specialoccasion')->getConfigData('general', 'day_create_order');
            $prepare_data = array();
            foreach ($collection as $item) {
                $compare_date = $this->compareTime(time(), strtotime($item->getShipDate()));
                if ($default_date == $compare_date) {
                    if(!array_key_exists($item->getSpecialoccasionId(), $prepare_data)){
                        $prepare_data[$item->getSpecialoccasionId()] = array(
                            $item->getId()
                        );
                    } else {
                        $prepare_data[$item->getSpecialoccasionId()][] = $item->getId();
                    }
                }
            }

            if(sizeof($prepare_data) > 0)
            {
                Mage::helper('specialoccasion/order')->prepareOrder($prepare_data);
            }
        }
    }

    public function checkAndReset()
    {
        $collection = Mage::getModel('specialoccasion/item')
            ->getCollection()
            ->addFieldToFilter('state', Magestore_SpecialOccasion_Model_Status::STATE_ACTIVE)
            ->setOrder('item_id', 'desc')
        ;

        if(sizeof($collection) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');

            foreach ($collection as $item) {
                if(date('Y', time()) == date('Y', strtotime($item->getShipDate())))
                {
                    if((date('m', time()) == date('m', strtotime($item->getShipDate())) &&
                        date('d', time()) == date('d', strtotime($item->getShipDate()))) ||
                        date('m', time()) < date('m', strtotime($item->getShipDate()))
                    ){
                        $item->setStatus(Magestore_SpecialOccasion_Model_Status::STATUS_ITEM_PENDING);
                        $item->setUpdatedAt(now());
                        $item->setShipDate(date("Y-m-d H:i:s", strtotime("+1 years", strtotime($item->getShipDate()))));
                        $transactionSave->addObject($item);
                    }
                }

            }

            try{
                $transactionSave->save();
            } catch (Exception $ex) {
            }
        }
    }

    public function compareTime($start_time, $end_time)
    {
        $diff = abs($end_time - $start_time);

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

        if ($years > 0 || $months > 1) {
            return false;
        } else {
            return $days;
        }
    }
}
