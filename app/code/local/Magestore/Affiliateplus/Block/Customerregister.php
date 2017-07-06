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
        return parent::_toHtml();
    }
    public function getAffiliateName(){
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('phone_number')
            ->addAttributeToFilter('phone_number',array('notnull'=>true))
            ->setOrder('entity_id','desc')
            ;

        $affiliate_table = Mage::getSingleton('core/resource')->getTableName('affiliateplus/account');

        $collection->getSelect()->join(
            array('account'=> $affiliate_table),
            '`account`.customer_id = `e`.entity_id',
            array(
                'account_id'=>'account_id',
                'account_name'=>'name',
            )
        );

        $jsonArray = array();
        $jsonAffiliateName = array();
        foreach ($collection as $col){
            $jsonArray['label'] = $col->getPhoneNumber();
            $jsonArray['value'] = $col->getAccountId();
            $jsonArray['desc'] = $col->getAccountName();
            array_push($jsonAffiliateName,$jsonArray);
        }
        return json_encode($jsonAffiliateName);
    }
}