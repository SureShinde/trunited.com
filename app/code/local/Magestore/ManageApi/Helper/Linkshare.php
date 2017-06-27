<?php

class Magestore_ManageApi_Helper_Linkshare extends Mage_Core_Helper_Abstract
{
    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function processAPI($url, $file)
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
                                'advertiser_name' => $ls[1],
                                'order_id' => $ls[2],
                                'transaction_date' => date('Y-m-d',strtotime($ls[3])),
                                'sales' => $ls[4],
                                'total_commission' => $ls[5],
                                'process_date' => date('Y-m-d',strtotime($ls[6])),
                                'created_at' => now(),
                            );

                            $model->setData($_dt);
                            $transactionSave->addObject($model);
                        }
                        $flag++;
                    }
                } else {
                    Mage::getSingleton('adminhtml/session')->addError('LINK SHARE API: Something was wrong with this response. Please click <a href="'.$url.'" target="_blank">here</a> view more detailed information.');
                }

                $transactionSave->save();
            } catch (Exception $e) {

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
                $this->processAPI($_url, $file);
            }
        }
    }
}