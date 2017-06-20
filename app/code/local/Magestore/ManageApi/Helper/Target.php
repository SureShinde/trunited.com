<?php

class Magestore_ManageApi_Helper_Target extends Mage_Core_Helper_Abstract
{
    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function processAPI($url)
    {
        $data = null;
        $_data = $this->getHelperData()->getDataXML($url);
        $data = json_decode(json_encode((array)$_data), 1);

        if ($data != null && is_array($data) && sizeof($data) > 0 && isset($data['Records']['Record'])
            && is_array($data['Records']['Record']) && sizeof($data['Records']['Record']) > 0) {

            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            try {
                $connection->beginTransaction();
                foreach ($data['Records']['Record'] as $v) {
                    $model = Mage::getModel('manageapi/targetactions');
                    $_dt = array();
                    foreach ($v as $k => $_v) {
                        $_dt[strtolower(trim(str_replace('-','_',$k)))] = is_array($_v) ? json_encode($_v) : $_v;
                    }
                    $model->setData($_dt);
                    $model->setData('created_time', now());
                    $transactionSave->addObject($model);
                }

                $transactionSave->save();
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollback();
            }
        }
    }

    public function processCron()
    {
        $enable = $this->getHelperData()->getDataConfig('enable_target', 'target');
        if ($enable) {
            $url = $this->getHelperData()->getDataConfig('target_api', 'target');
            if ($url != null) {
                $days = $this->getHelperData()->getDataConfig('target_days', 'target');
                $_days = $days != null ? $days : 1;
                $start_date = date('Y-m-d', strtotime('-'.$_days.' day', time()));
                $end_data = date('Y-m-d', strtotime('-'.$_days.' day', time()));
                $_url = str_replace(array('{{start_date}}', '{{end_date}}'), array($start_date, $end_data), $url);
                $this->processAPI($_url);
            }
        }
    }
}