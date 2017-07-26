<?php

class Magestore_ManageApi_Helper_Cj extends Mage_Core_Helper_Abstract
{
    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function processAPI($url, $start_date)
    {
        $data = null;
        $cj_developer_key = $this->getHelperData()->getDataConfig('cj_developer_key', 'cj');
        if($cj_developer_key == null)
            return;

        $params_header = array(
            'authorization: '.$cj_developer_key,
        );
        $_data = $this->getHelperData()->getDataXML($url, $params_header);
        $data = json_decode(json_encode((array)$_data), 1);

        if ($data != null && is_array($data) && sizeof($data) > 0 && isset($data['commissions']['commission'])
            && is_array($data['commissions']['commission']) && sizeof($data['commissions']['commission']) > 0) {

            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            try {
                $connection->beginTransaction();
                foreach ($data['commissions']['commission'] as $v) {
                    $model = Mage::getModel('manageapi/cjactions');
                    $_dt = array();
                    foreach ($v as $k => $_v) {
                        $_dt[str_replace('-','_',$k)] = is_array($_v) ? json_encode($_v) : $_v;
                    }

                    $model->setData($_dt);
                    $model->setData('created_time', now());

                    $customer = Mage::getModel('customer/customer')->load($_dt['sid']);
                    if($customer != null && $customer->getId() && floor($_dt['commission_amount']) > 0){
                        Mage::helper('rewardpoints/action')->addTransaction('global_brand', $customer, new Varien_Object(array(
                                'product_credit_title' => 0,
                                'product_credit' => 0,
                                'point_amount' => floor($_dt['commission_amount']),
                                'title' => Mage::helper('manageapi')->__('Points awarded for global brand %s order %s on %s', $_dt['advertiser_name'], $_dt['order_id'], $start_date),
                                'expiration_day' => 0,
                                'expiration_day_credit' => 0,
                                'is_on_hold' => 1,
                                'created_time' => date('Y-m-d H:i:s', strtotime($_dt['posting_date'])),
                                'order_increment_id' => $_dt['order_id']
                            ))
                        );
                    }
                    $transactionSave->addObject($model);
                }

                $transactionSave->save();
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollback();
            }
        } else {
            if(isset($data['error-message']))
                Mage::getSingleton('adminhtml/session')->addError('CJ API: '.$data['error-message']);
        }
    }

    public function processCron()
    {
        $enable = $this->getHelperData()->getDataConfig('enable_cj', 'cj');
        if ($enable) {
            $url = $this->getHelperData()->getDataConfig('cj_api', 'cj');
            if ($url != null) {
                $data_type = $this->getHelperData()->getDataConfig('cj_data_type', 'cj');
                $start_date = date('Y-m-d', strtotime('-1 day', time()));
                $end_data = date('Y-m-d', time());
                $_url = str_replace(array('{{start_date}}', '{{end_date}}', '{{data_type}}'), array($start_date, $end_data, $data_type), $url);
                $this->processAPI($_url, $start_date);
            }
        }
    }
}