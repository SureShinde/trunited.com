<?php
class Magestore_Affiliateplus_TestController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
        Mage::helper('affiliateplus/random')->reGenerateIdentifyCode();
        zend_debug::dump($_SERVER['REMOTE_ADDR']);
        zend_debug::dump(Mage::log(Mage::helper('affiliateplus/random')->reGenerateIdentifyCode(), null, 'test.log'));
//        $transaction = Mage::getModel('affiliateplus/transaction')->load(43);
//        
//        $transaction->setCommission(100)
//                        ->setDiscount(100)
//                        ->save();
//        print_r($transaction->getData());die('z');
//        $abc = implode(',', array(2));
//        print_r($abc); die('z');
//        $installer = new Mage_Core_Model_Resource_Setup();
//        $installer->startSetup();
//        $installer->getConnection()->addColumn($installer->getTable('affiliateplus/account'), 'refer_by_email', 'varchar(255) default ""');
//
//        $installer->endSetup();
//        $order = Mage::getModel('sales/order')->load(214);
//    
//        foreach($order->getAllItems() as $item) {
//            
//                    $sfoi = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
//                    $sfvi = Mage::getSingleton('core/resource')->getTableName('sales/invoice_item');
//                    $collection = Mage::getModel('sales/order_item')->getCollection();
//                    $collection->getSelect()
//                            ->join($sfvi, $sfvi.'.order_item_id = main_table.item_id', array('invoice_qty'=>$sfvi.'.qty', 'affiliateplus_commission_flag' => 'affiliateplus_commission_flag'))
//                            ->where($sfvi.'.affiliateplus_commission_flag = 0')
//                            ->where('main_table.item_id = '.$item->getId())
//                            ;
//                    $collection->printlogquery(true);
//                    die('x');
//        }
    }
    public function testAction(){
        $models = Mage::getModel('affiliateplus/account')->getCollection();
        $jsonArray = array();
        $test = array();
        foreach ($models as $model){
            $jsonArray['label'] = $model->getName();
            $jsonArray['value'] = $model->getAccountId();
//            zend_debug::dump($model);
//            json_encode($jsonArray);
//            zend_debug::dump(json_encode($jsonArray));
            array_push($test,$jsonArray);
//            zend_debug::dump(array_push($test,$jsonArray));

        }
//        zend_Debug::dump($test);
        zend_debug::dump(json_encode($test));
        die('fds');
    }

    public function test1Action(){
        $installer =  new Mage_Core_Model_Resource_Setup();
        $installer->startSetup();
        $installer->run("
           ALTER TABLE {$installer->getTable('affiliateplus_transaction')}
              ADD COLUMN `rw_earning_point` int(11) NOT NULL default '0';
      ");
        $installer->endSetup();
    }

    public function addCustomerAttributeAction(){
        $installer =  new Mage_Eav_Model_Entity_Setup('core_setup');
        $installer->startSetup();

        $entityTypeId     = $installer->getEntityTypeId('customer');
        $attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

        $installer->addAttribute("customer", "no_refers_me",  array(
            "type"     => "int",
            "backend"  => "",
            "label"    => "No One Referred Me",
            "input"    => "select",
            "source"   => "eav/entity_attribute_source_boolean",
            "visible"  => true,
            "required" => false,
            "default" => "0",
            "frontend" => "",
            "unique"     => false,
            "note"       => "No one referred me."

        ));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "no_refers_me");
        $installer->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            'no_refers_me',
            '999'
        );

        $used_in_forms=array();

        $used_in_forms[]="adminhtml_customer";
        $used_in_forms[]="checkout_register";
        $used_in_forms[]="customer_account_create";
        $used_in_forms[]="adminhtml_checkout";
        $attribute->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100)
        ;
        $attribute->save();

        $installer->endSetup();
        echo 'success';
    }

    public function addCustomerAttribute2Action(){
        $installer =  new Mage_Eav_Model_Entity_Setup('core_setup');
        $installer->startSetup();

        $entityTypeId     = $installer->getEntityTypeId('customer');
        $attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

        $installer->addAttribute("customer", "referred_affiliate_name",  array(
            "type"     => "varchar",
            "backend"  => "",
            "label"    => "Name of referred Affiliate",
            "input"    => "text",
            "source"   => "",
            "visible"  => true,
            "required" => false,
            "default" => "0",
            "frontend" => "",
            "unique"     => false,
            "note"       => "Name of referred Affiliate"

        ));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "referred_affiliate_name");
        $installer->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            'referred_affiliate_name',
            '998'
        );

        $used_in_forms=array();

        $used_in_forms[]="adminhtml_customer";
        $used_in_forms[]="checkout_register";
        $used_in_forms[]="customer_account_create";
        $used_in_forms[]="adminhtml_checkout";
        $attribute->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100)
        ;
        $attribute->save();

        $installer->endSetup();
        echo 'success';
    }
}
        