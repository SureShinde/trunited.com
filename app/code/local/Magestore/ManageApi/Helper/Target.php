<?php

class Magestore_ManageApi_Helper_Target extends Mage_Core_Helper_Abstract
{
    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function processAPI($url, $start_date)
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

                    $customer = Mage::getModel('customer/customer')->load($_dt['subid1']);
                    if($customer != null && $customer->getId() && $_dt['payout'] > 0){
                        Mage::helper('rewardpoints/action')->addTransaction('global_brand', $customer, new Varien_Object(array(
                                'product_credit_title' => 0,
                                'product_credit' => 0,
                                'point_amount' => $_dt['payout'],
                                'title' => Mage::helper('manageapi')->__('Points awarded for global brand Target.com order on %s. Action ID [%s]', $start_date, $_dt['action_id']),
                                'expiration_day' => 0,
                                'expiration_day_credit' => 0,
                                'is_on_hold' => 1,
                                'created_time' => date('Y-m-d H:i:s', strtotime($_dt['action_date'])),
                                'order_increment_id' => $_dt['action_id']
                            ))
                        );
                    }

                    $transactionSave->addObject($model);
                }
                Mage::log('TARGET API at '.date('Y-m-d H:i:s', time()).' - Result:'.sizeof($data['Records']['Record']).' - URL: '.$url, null, 'check_manage_api.log');
                $transactionSave->save();
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollback();
            }
        } else {
            if(isset($data['Status']) && strcasecmp($data['Status'], 'ERROR') == 0)
                Mage::getSingleton('adminhtml/session')->addError('TARGET API: '.$data['Message']);
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
                $this->processAPI($_url, $start_date);
            }
        }
    }
}
