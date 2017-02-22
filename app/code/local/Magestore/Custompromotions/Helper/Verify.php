<?php

class Magestore_Custompromotions_Helper_Verify extends Mage_Core_Helper_Abstract
{
    public function getPhoneNumberFormat($prefix, $number)
    {
        $_number = $this->formatPhoneToDatabase($number);
        return '+'.$prefix.''.$_number;
    }

    public function formatPhoneToDatabase($phone)
    {
        return trim(str_replace(array('(',')','-',' '),array('','','',''), $phone));
    }

    public function isVerified($phone)
    {
        $_phone = $this->formatPhoneToDatabase($phone);
        $collection = Mage::getModel('custompromotions/verifymobile')->getCollection()
            ->addFieldToFilter('phone',$_phone)
            ->getFirstItem()
            ;

        if($collection->getId())
        {
            if($collection->getStatus() == Magestore_Custompromotions_Model_Verifymobile::STATUS_VERIFIED)
            {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function saveVerify($phone, $code)
    {
        $model = Mage::getModel('custompromotions/verifymobile')->getCollection()
            ->addFieldToFilter('phone',$phone)
            ->getFirstItem()
        ;

        if($model->getId()){
            $model->setCode($code);
            $model->setStatus(Magestore_Custompromotions_Model_Verifymobile::STATUS_UNVERIFIED);
            $model->setCreatedTime(now());
            $model->setUpdatedTime(now());
            $model->save();
        } else {
            $model = Mage::getModel('custompromotions/verifymobile');
            $model->setPhone($phone);
            $model->setCode($code);
            $model->setStatus(Magestore_Custompromotions_Model_Verifymobile::STATUS_UNVERIFIED);
            $model->setCreatedTime(now());
            $model->setUpdatedTime(now());
            $model->save();
        }
    }

    public function verify($phone, $code)
    {
        $collection = Mage::getModel('custompromotions/verifymobile')->getCollection()
            ->addFieldToFilter('phone',$phone)
            ->addFieldToFilter('code',$code)
            ->getFirstItem()
            ;

        if($collection->getId())
        {
            if($collection->getStatus() == Magestore_Custompromotions_Model_Verifymobile::STATUS_VERIFIED){
                return Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_VERIFIED;
            } else {
                try{
                    $collection->setStatus(Magestore_Custompromotions_Model_Verifymobile::STATUS_VERIFIED);
                    $collection->setUpdatedTime(now());
                    $collection->save();
                    return Magestore_Custompromotions_Model_Verifymobile::VERIFY_SUCCESS;
                } catch (Exception $ex){
                    return Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_VERIFIED;
                }
            }
        } else {
            return Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_NON_EXIST;
        }
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

    public function getDefaultLength()
    {
        return Mage::getStoreConfig('custompromotions/verify/code_length');
    }

    public function isEnable()
    {
        return Mage::getStoreConfig('custompromotions/verify/enable');
    }

    public function getAccountSID()
    {
        return Mage::getStoreConfig('custompromotions/verify/account_sid');
    }

    public function getAuthToken()
    {
        return Mage::getStoreConfig('custompromotions/verify/auth_token');
    }

    public function getSenderNumber()
    {
        return '+'.Mage::getStoreConfig('custompromotions/verify/sender_number');
    }

    public function getAllowEdit()
    {
        return Mage::getStoreConfig('custompromotions/verify/allow_edit');
    }

    public function getMobileCode()
    {
        return Mage::getStoreConfig('custompromotions/verify/mobile_code') != null ? Mage::getStoreConfig('custompromotions/verify/mobile_code') : '1';
    }

    public function generateRandomString()
    {
        $length = $this->getDefaultLength() == '' ? 5 : $this->getDefaultLength();
        $token = "";
        $codeAlphabet = "0123456789";

        $max = strlen($codeAlphabet);
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max - 1)];
        }

        return $token;
    }

    public function getVerifyByPhoneCode($phone, $code)
    {
        $collection =  Mage::getModel('custompromotions/verifymobile')->getCollection()
            ->addFieldToFilter('phone',$phone)
            ->addFieldToFilter('code',$code)
            ->addFieldToFilter('status',Magestore_Custompromotions_Model_Verifymobile::STATUS_VERIFIED)
            ->getFirstItem()
        ;

        if($collection->getId())
            return $collection;
        else
            return null;
    }
}