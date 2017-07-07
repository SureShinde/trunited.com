<?php
class Magestore_Affiliateplus_Block_Customerregister extends Mage_Core_Block_Template
{
    /**
     * get Helper
     *
     * @return Magestore_Affiliateplus_Helper_Config
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    protected function _toHtml() {
        //        if (!Mage::getStoreConfigFlag('rewards/referral/show_in_register'))
        //            return '';
        //        if (!$this->showReferralEmail() && !$this->showReferralCode()  )
        //            return '';
        return parent::_toHtml();
    }
    public function getAffiliateName(){
        $models = Mage::getModel('affiliateplus/account')->getCollection();
        $jsonArray = array();
        $jsonAffiliateName = array();
        foreach ($models as $model){
            $jsonArray['label'] = $model->getName();
            $jsonArray['value'] = $model->getAccountId();
//            zend_debug::dump($model);
//            json_encode($jsonArray);
//            zend_debug::dump(json_encode($jsonArray));
            array_push($jsonAffiliateName,$jsonArray);
//            zend_debug::dump(array_push($test,$jsonArray));

        }
//        zend_Debug::dump($test);
        return json_encode($jsonAffiliateName);
    }
}