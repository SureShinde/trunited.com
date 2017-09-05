<?php

class Magestore_ManageApi_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LINK_SHARE_FILE = 'manage_api/linkshare/linkshare.csv';
    const PRICE_LINE_HOTEL = 'manage_api/priceline/hotel.xml';
    const PRICE_LINE_FIGHT = 'manage_api/priceline/fight.xml';
    const PRICE_LINE_CAR = 'manage_api/priceline/car.xml';
    const PRICE_LINE_VACATION = 'manage_api/priceline/vacation.xml';
    const CJ_VACATION = 'manage_api/cj/cj.xml';

    public function getDataConfig($file_name, $group = 'general')
    {
        return Mage::getStoreConfig(
            'manageapi/' . $group . '/' . $file_name,
            Mage::app()->getStore()
        );
    }

    public function getContentByCurl($url, $param_header = array(), $is_post = false, $param_fields = array())
    {
        if(sizeof($param_header) > 0)
        {
            foreach ($param_header as $_header) {
                header($_header);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if(sizeof($param_header) > 0)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $param_header);

        if($is_post && sizeof($param_fields) > 0){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($param_fields));
        } else {
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);


        try {
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

        try {
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

        if ($data != null) {
            $_file = $this->downloadFile($data, $file);
            if ($_file != null) {
                $_data = $this->getCsvData($_file);
            }
        }

        return $_data;
    }

    public function getXMLData($str)
    {
        return simplexml_load_string($str);
    }

    public function getDataXML($url, $param_header = array())
    {
        $_data = null;
        $data = $this->getContentByCurl($url, $param_header);

        if ($data != null) {
            $_data = $this->getXMLData($data);
        }

        return $_data;
    }

}