<?php

class Magestore_ManageApi_Helper_Shareasale extends Mage_Core_Helper_Abstract
{
    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function processAPI($url, $start_date)
    {
        $data = null;

        $_data = $this->getHelperData()->getDataXML($url, $this->getParamsHeader());
        $data = json_decode(json_encode((array)$_data), 1);

        if ($data != null && is_array($data) && sizeof($data) > 0 && isset($data['activitydetailsreportrecord']) && sizeof($data['activitydetailsreportrecord']) > 0) {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            try {
                $connection->beginTransaction();

                if (isset($data['activitydetailsreportrecord']) && sizeof($data['activitydetailsreportrecord']) > 0) {
                    foreach ($data['activitydetailsreportrecord'] as $sale) {
                        $model = Mage::getModel('manageapi/shareasale');
                        foreach ($sale as $k => $v) {
                            if (is_array($v))
                                $_dt[$k] = sizeof($v) > 0 ? json_encode($v) : '';
                            else
                                $_dt[$k] = $v;
                        }
                        $_dt['created_time'] = now();
                        $model->setData($_dt);

                        $customer = Mage::getModel('customer/customer')->load($_dt['affcomment']);
                        if($customer != null && $customer->getId() && $_dt['commission'] > 0){
                            Mage::helper('rewardpoints/action')->addTransaction('global_brand', $customer, new Varien_Object(array(
                                    'product_credit_title' => 0,
                                    'product_credit' => 0,
                                    'point_amount' =>$_dt['commission'],
                                    'title' => Mage::helper('manageapi')->__('Points awarded for Shareasale: %s on %s', $_dt['merchantorganization'], $start_date),
                                    'expiration_day' => 0,
                                    'expiration_day_credit' => 0,
                                    'is_on_hold' => 1,
                                    'created_time' => now(),
                                    'order_increment_id' => $_dt['transid']
                                ))
                            );
                        }
                        $transactionSave->addObject($model);
                    }
                    Mage::log('SHAREASALE at '.date('Y-m-d H:i:s', time()).' - Result:'.sizeof($data['activitydetailsreportrecord']).' - URL: '.$url, null, 'check_manage_api.log');
                }

                $transactionSave->save();
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollback();
            }
        } else {
            $error_message = '';
            $errors = json_decode(json_encode((array)$_data), 1);
            $error_message .= $errors;
            Mage::getSingleton('adminhtml/session')->addError('SHAREASALE API: '.$error_message);
        }
    }

    public function processCron()
    {
        $enable = $this->getHelperData()->getDataConfig('enable', 'share_a_sale');
        if ($enable) {
            $url = $this->getHelperData()->getDataConfig('shareasale_api', 'share_a_sale');
            if ($url != null) {
                $affiliateId = $this->getHelperData()->getDataConfig('affiliate_id', 'share_a_sale');
                $token = $this->getHelperData()->getDataConfig('token', 'share_a_sale');
                $_days = 1;
                $start_date = date('m-d-Y', strtotime('-'.$_days.' day', time()));
                $end_data = date('m-d-Y', strtotime('-'.$_days.' day', time()));
                $_url = str_replace(array('{{affiliate_id}}', '{{token}}', '{{start_date}}', '{{end_date}}'), array($affiliateId, $token, $start_date, $end_data), $url);
                $this->processAPI($_url, $start_date);
            }
        }
    }

    public function getParamsHeader()
    {
        $APIToken = $this->getHelperData()->getDataConfig('token', 'share_a_sale');
        $APISecretKey = $this->getHelperData()->getDataConfig('secret_key', 'share_a_sale');
        $myTimeStamp = gmdate(DATE_RFC1123);

        $actionVerb = "activity";
        $sig = $APIToken.':'.$myTimeStamp.':'.$actionVerb.':'.$APISecretKey;
        $sigHash = hash("sha256",$sig);

        return array(
            "x-ShareASale-Date: $myTimeStamp",
            "x-ShareASale-Authentication: $sigHash"
        );
    }
}
