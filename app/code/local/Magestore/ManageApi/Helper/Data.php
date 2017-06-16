<?php

class Magestore_ManageApi_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LINK_SHARE_FILE = 'manage_api/linkshare/linkshare.csv';
    const PRICE_LINE_HOTEL = 'manage_api/priceline/hotel.xml';
    const PRICE_LINE_FIGHT = 'manage_api/priceline/fight.xml';
    const PRICE_LINE_CAR = 'manage_api/priceline/car.xml';
    const PRICE_LINE_VACATION = 'manage_api/priceline/vacation.xml';

    public function getDataConfig($file_name, $group='general')
    {
        return Mage::getStoreConfig(
            'manageapi/'.$group.'/'.$file_name,
            Mage::app()->getStore()
        );
    }

    public function getContentByCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try{
            $data = curl_exec($ch);
        } catch (Exception $ex) {
            return null;
        }
        curl_close($ch);

        return $data;
    }

    public function downloadFile($data, $file_name)
    {
        if (file_exists($file_name)) {
            unlink($file_name);
        }

        try{
            $file = fopen($file_name, "w+");
            fputs($file, $data);
        } catch (Exception $ex) {
            return null;
        }

        return $file_name;
    }

    public function getCsvData($file)
    {
        $csvObject = new Varien_File_Csv();
        try {
            return $csvObject->getData($file);
        } catch (Exception $e) {
            Mage::log('Csv: ' . $file . ' - getCsvData() error - ' . $e->getMessage(), Zend_Log::ERR, 'exception.log', true);
            return false;
        }

    }

    public function getDataCSV($url, $file)
    {
        $_data = null;
        $data = $this->getContentByCurl($url);

        if($data != null)
        {
            $_file = $this->downloadFile($data, $file);
            if($_file != null)
            {
                $_data = $this->getCsvData($_file);
            }
        }

        return $_data;
    }

    public function processLinkShareAPI($url, $file)
    {
        $data = $this->getDataCSV($url, $file);
        if($data != null && is_array($data))
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            try {
                $connection->beginTransaction();

                if(sizeof($data) > 1)
                {
                    $flag = 0;
                    foreach ($data as $ls) {
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
                }

                $transactionSave->save();
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollback();
            }
        }
    }

    public function processLinkShareCron()
    {
        $enable = $this->getDataConfig('enable');
        if($enable)
        {
            $url = $this->getDataConfig('link_share_api');
            if($url != null)
            {
                $start_date = $end_data = date('Y-m-d',strtotime('-1 day', time()));
                $_url = str_replace(array('{{start_date}}','{{end_date}}'),array($start_date, $end_data), $url);
                $file = Mage::getBaseDir('media') . DS . self::LINK_SHARE_FILE;
                $this->processLinkShareAPI($_url, $file);
            }
        }
    }
    /** END LINK SHARE API **/

    /** BEGIN PRICE LINE API **/
    public function getXMLData($str)
    {
        return simplexml_load_string($str);
    }

    public function getDataXML($url)
    {
        $_data = null;
        $data = $this->getContentByCurl($url);

        if($data != null)
        {
            $_data = $this->getXMLData($data);
        }

        return $_data;
    }

    public function processPriceLineHotelAPI($url, $file)
    {
        $data = $this->getDataXML($url);
        $array = json_decode(json_encode((array) $data), 1);
        $_array = array($data->getName() => $array);
        zend_debug::dump($_array);

        exit;
        if($data != null && is_array($data))
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            try {
                $connection->beginTransaction();

                if(sizeof($data) > 1)
                {
                    $flag = 0;
                    foreach ($data as $ls) {
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
                }

                $transactionSave->save();
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollback();
            }
        }
    }

    public function processPriceLineHotelCron()
    {
        $enable = $this->getDataConfig('enable');
        if($enable)
        {
            $url = 'https://api.rezserver.com/api/shared/getTRK.Sales.Select.Hotel?refid=7844&api_key=04dbbbc0c546b154e6d4f4f9b34f8b52&format=xml&time_start=2017-06-15_00:00:00&time_end=2017-06-15_23:59:59&accountid_value=7844';
            if($url != null)
            {
                $start_date = $end_data = date('Y-m-d',strtotime('-1 day', time()));
                $_url = str_replace(array('{{start_date}}','{{end_date}}'),array($start_date, $end_data), $url);
                $file = Mage::getBaseDir('media') . DS . self::PRICE_LINE_HOTEL;
                $this->processPriceLineHotelAPI($_url, $file);
            }
        }
    }
    /** END PRICE LINE API **/
}