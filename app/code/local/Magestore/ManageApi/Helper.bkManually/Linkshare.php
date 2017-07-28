<?php

class Magestore_ManageApi_Helper_Linkshare extends Mage_Core_Helper_Abstract
{
    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function processAPI($url, $file, $start_date)
    {
        $data = $this->getHelperData()->getDataCSV($url, $file);
        if($data != null && is_array($data))
        {
            $transactionSave = Mage::getModel('core/resource_transaction');

            try {

                if(sizeof($data) > 1)
                {
                    $flag = 0;
                    foreach ($data as $ls) {
                        if(sizeof($ls) == 2)
                        {
                            Mage::getSingleton('adminhtml/session')->addError('LINKSHARE API: '.$ls[0].$ls[1]);
                            break;
                        }
                        if($flag > 0)
                        {
                            $model = Mage::getModel('manageapi/linkshare');
                            $_dt = array(
                                'member_id' => $ls[0],
                                'mid' => $ls[1],
                                'advertiser_name' => $ls[2],
                                'order_id' => $ls[3],
                                'transaction_date' => date('Y-m-d',strtotime($ls[4].' '.$ls[5])),
                                'sku' => $ls[6],
                                'sales' => $ls[7],
                                'items' => $ls[8],
                                'total_commission' => $ls[9],
                                'process_date' => date('Y-m-d',strtotime($ls[10].' '.$ls[11])),
                                'created_at' => date('Y-m-d 01:00:00', time()),
                            );

                            $model->setData($_dt);
                            $transactionSave->addObject($model);
                        }
                        $flag++;
                    }
                    Mage::log('LINKSHARE API at '.date('Y-m-d 01:00:00', time()).' - Result:'.sizeof($data).' - URL: '.$url, null, 'run_api.log');
                } else {
                    Mage::getSingleton('adminhtml/session')->addError('LINK SHARE API: Something was wrong with this response. Please click <a href="'.$url.'" target="_blank">here</a> view more detailed information.');
                }

                $transactionSave->save();
                $this->createOnHoldTransaction($start_date);
            } catch (Exception $e) {

            }
        }
    }

    public function createOnHoldTransaction($start_date)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('manageapi/linkshare');
        $query = 'SELECT member_id as customer_id, advertiser_name, process_date, order_id, ROUND(SUM(total_commission), 0) as on_hold_points FROM '.$table.' WHERE process_date = "'.$start_date.'" GROUP BY member_id, advertiser_name, process_date, order_id';
        $results = $readConnection->fetchAll($query);

        if($results != null && is_array($results) && sizeof($results) > 0)
        {
            foreach ($results as $rs) {
                $customer = Mage::getModel('customer/customer')->load($rs['customer_id']);
                if($customer != null && $customer->getId() && floor($rs['on_hold_points']) > 0 && strcasecmp($rs['status'],'Active') == 0){
                    Mage::helper('rewardpoints/action')->addTransaction('global_brand', $customer, new Varien_Object(array(
                            'product_credit_title' => 0,
                            'product_credit' => 0,
                            'point_amount' => floor($rs['on_hold_points']),
                            'title' => Mage::helper('manageapi')->__('Points awarded for global brand %s order %s on %s', $rs['advertiser_name'], $rs['order_id'], $start_date),
                            'expiration_day' => 0,
                            'expiration_day_credit' => 0,
                            'is_on_hold' => 1,
                            'created_time' => date('Y-m-d H:i:s', strtotime($rs['process_date'])),
                            'order_increment_id' => $rs['order_id']
                        ))
                    );
                }
            }

        }

    }

    public function processCron()
    {
        $enable = $this->getHelperData()->getDataConfig('enable', 'link_share');
        if($enable)
        {
            $url = $this->getHelperData()->getDataConfig('link_share_api', 'link_share');
            if($url != null)
            {
                $start_date = $end_data = date('Y-m-d',strtotime('-1 day', time()));
                $_url = str_replace(array('{{start_date}}','{{end_date}}'),array($start_date, $end_data), $url);
                $file = Mage::getBaseDir('media') . DS . Magestore_ManageApi_Helper_Data::LINK_SHARE_FILE;
                $this->processAPI($_url, $file, $start_date);
            }
        }
    }
}