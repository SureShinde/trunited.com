<?php

class Magestore_Affiliateplus_Helper_Random extends Mage_Core_Helper_Abstract
{
    public function getRemoveStrings()
    {
        return Mage::getStoreConfig('affiliateplus/general/remove_string');
    }

    public function getDefaultLength()
    {
        return Mage::getStoreConfig('affiliateplus/general/length_identity_code');
    }

    public function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min;
        $log = ceil(log($range, 2));
        $bytes = (int)($log / 8) + 1;
        $bits = (int)$log + 1;
        $filter = (int)(1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd > $range);
        return $min + $rnd;
    }

    public function generateRandomString()
    {
        $length = $this->getDefaultLength() == '' ? 5 : $this->getDefaultLength();
        $token = "";
//        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet = "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";

        $removeStrings = explode(',',$this->getRemoveStrings());
        $replaceStrings = array();

        if(sizeof($removeStrings) > 0){
            foreach ($removeStrings as $str)
                $replaceStrings[] = '';

            $codeAlphabet = str_replace($removeStrings,$replaceStrings,$codeAlphabet);
        }

        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max - 1)];
        }

        return $token;
    }

    public function reGenerateIdentifyCode(){
        $collection = Mage::getModel('affiliateplus/account')->getCollection();
        $storeId = Mage::app()->getStore()->getId();
        $transactionSave = Mage::getModel('core/resource_transaction');

        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $connection->beginTransaction();
            $url_param = Mage::getStoreConfig('affiliateplus/general/url_param');
            if($url_param == '')
                $url_param = 'acc';
            foreach($collection as $acc){
                $new_code = Mage::helper('affiliateplus/random')->generateRandomString();
                $idPath = 'affiliateplus/'.$storeId.'/'.$acc->getId();
                $idPathRefer = $idPath.'/fixemail';

                $url_rewrite = Mage::getModel('core/url_rewrite')->load($idPath,'id_path');
                if($url_rewrite->getId()){
                    $url_rewrite->setData('target_path','cms/index/index/?'.$url_param.'='.$new_code);
                    $transactionSave->addObject($url_rewrite);
                }

                $url_rewrite2 = Mage::getModel('core/url_rewrite')->load($idPathRefer,'id_path');
                if($url_rewrite2->getId()){
                    $url_rewrite2->setData('target_path','cms/index/index/?'.$url_param.'='.$new_code);
                    $transactionSave->addObject($url_rewrite2);
                }

                $acc->setData('identify_code',$new_code);
            }

            $collection->walk('save');
            $transactionSave->save();


            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            return 'failure';
        }
        return 'success';
    }
}