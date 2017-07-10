<?php

class Magestore_ManageApi_Helper_Hotel extends Mage_Core_Helper_Abstract
{
    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function processAPI($url)
    {
        $data = null;
        $is_xml = false;
        if (strpos($url, 'format=xml') > 0) {
            $_data = $this->getHelperData()->getDataXML($url);
            $data = json_decode(json_encode((array)$_data), 1);
            $is_xml = true;
        } else if (strpos($url, 'format=json') > 0) {
            $_data = $this->getHelperData()->getContentByCurl($url);
            $dt = json_decode($_data, true);
            $data = $dt['getSharedTRK.Sales.Select.Hotel'];
        } else
            return;

        if ($data != null && is_array($data) && sizeof($data) > 0 && isset($data['results']) && sizeof($data['results']) > 0) {
            $transactionSave = Mage::getModel('core/resource_transaction');
//            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            try {
//                $connection->beginTransaction();
                $other_data = array();
                foreach ($data['results'] as $k => $v) {
                    if ($k != 'sales_data') {
                        if (is_array($v))
                            $other_data[$k] = sizeof($v) > 0 ? json_encode($v) : '';
                        else
                            $other_data[$k] = $v;
                    }
                }

                if (isset($data['results']['sales_data']) && sizeof($data['results']['sales_data']) > 0) {
                    if ($is_xml && isset($data['results']['sales_data']['sale']) && sizeof($data['results']['sales_data']['sale']) > 0) {
                       
                        foreach ($data['results']['sales_data']['sale'] as $sale) {
                            $model = Mage::getModel('manageapi/hotelactions');
                            foreach ($sale as $k => $v) {
                                if (is_array($v))
                                    $_dt[$k] = sizeof($v) > 0 ? json_encode($v) : '';
                                else
                                    $_dt[$k] = $v;
                            }
                            $_dt['other'] = json_encode($other_data);
                            $_dt['created_time'] = now();
                            $model->setData($_dt);
                            $transactionSave->addObject($model);
                        }
                    } else if (!$is_xml) {
                        foreach ($data['results']['sales_data'] as $sale) {
                            $model = Mage::getModel('manageapi/hotelactions');
                            foreach ($sale as $k => $v) {
                                if (is_array($v))
                                    $_dt[$k] = sizeof($v) > 0 ? json_encode($v) : '';
                                else
                                    $_dt[$k] = $v;
                            }
                            $_dt['other'] = json_encode($other_data);
                            $_dt['created_time'] = now();
                            $model->setData($_dt);
                            $transactionSave->addObject($model);
                        }
                    }
                } else {
                    $model = Mage::getModel('manageapi/hotelactions');
                    $_dt = array(
                        'other' => json_encode($other_data),
                        'created_time' => now(),
                    );
                    $model->setData($_dt);
                    $transactionSave->addObject($model);
                }

                $transactionSave->save();
//                $connection->commit();
            } catch (Exception $e) {
//                $connection->rollback();
                zend_debug::dump($e);
                exit;
            }
        } else {
            $error_message = '';
            $flag_error = false;
            if($is_xml){
                $errors = json_decode(json_encode((array)$_data), 1);
                $error_message .= $errors['error']['status'];
                $flag_error = true;
            } else {
                $errors = json_decode($_data, true);
                $error_message .= $errors['getSharedTRK.Sales.Select.Hotel']['error']['status'];
                $flag_error = true;
            }
            if($flag_error)
                Mage::getSingleton('adminhtml/session')->addError('PRICE LINE HOTEL API: '.$error_message);
        }
    }

    public function processCron()
    {
        $enable = $this->getHelperData()->getDataConfig('enable_hotel', 'price_line');
        if ($enable) {
            $url = $this->getHelperData()->getDataConfig('hotel_api', 'price_line');
            if ($url != null) {
                $format = $this->getHelperData()->getDataConfig('hotel_format', 'price_line');
                $days = $this->getHelperData()->getDataConfig('hotel_days', 'price_line');
                $_days = $days != null ? $days : 1;
                $start_date = date('Y-m-d_00:00:00', strtotime('-' . $_days . ' day', time()));
                $end_data = date('Y-m-d_23:59:59', strtotime('-' . $_days . ' day', time()));
                $_url = str_replace(array('{{start_date}}', '{{end_date}}', '{{format}}'), array($start_date, $end_data, $format), $url);
                $this->processAPI($_url);
            }
        }
    }
}