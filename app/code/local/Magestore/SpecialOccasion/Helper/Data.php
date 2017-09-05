<?php

class Magestore_SpecialOccasion_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EMAIL_REMIND = 'specialoccasion/email/remind';
    const XML_PATH_EMAIL_SENDER = 'specialoccasion/email/sender';

    public function getConfigData($section, $field, $store = null)
    {
        return Mage::getStoreConfig('specialoccasion/'.$section.'/'.$field, $store);
    }

    public function isEnable()
    {
        return $this->getConfigData('general', 'enable');
    }

    public function getOccastions()
    {
        $occasions = $this->getConfigData('general', 'occasions');
        if ($occasions) {
            $occasion_data = unserialize($occasions);
            if (is_array($occasion_data)) {
                $rs = array();
                foreach($occasion_data as $occasion) {
                    $rs[] = $occasion['occasion_name'];
                }
                return $rs;
            } else {
                return null;
            }
        }

        return null;
    }
    
    public function getCategories()
    {
        return $this->getConfigData('general', 'categories');
    }

    public function getProductCollection()
    {
        $categories = $this->getCategories();
        $list_categories = explode(',', $categories);
        if($list_categories != null && is_array($list_categories))
        {
            $_productCollection = Mage::getModel('catalog/product')
                ->getCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('in' => array('finset' => $list_categories)))
                ->addAttributeToSelect('*');

            return $_productCollection;
        }

        return null;
    }

    public function getItemCollectionByCustomerId()
    {
        $occasion_id = $this->getCurrentOccasionId();
        return Mage::getModel('specialoccasion/item')->getCollection()
            ->addFieldToFilter('specialoccasion_id', $occasion_id)
            ->setOrder('item_id', 'desc')
            ;
    }

    public function getShipName($item_id)
    {
        $ship = $this->getShipObj($item_id);
        if($ship != null && $ship->getId())
            return $ship->getFirstname().' '.$ship->getLastname();
        else
            return '';
    }

    public function getShipObj($item_id)
    {
        return Mage::getModel('specialoccasion/address')->getCollection()
            ->addFieldToFilter('item_id', $item_id)
            ->setOrder('item_id', 'desc')
            ->getFirstItem()
            ;
    }

    public function checkRegionId($country, $region_name, $region_id = 0)
    {
        if($region_id > 0  && !filter_var($region_id, FILTER_VALIDATE_INT) === false)
        {
            return $region_id;
        } else {
            $region = Mage::getModel('directory/region')->getCollection()
                ->addFieldToSelect('region_id')
                ->addFieldToFilter('country_id', $country)
                ->addFieldToFilter('default_name', $region_name)
                ->getFirstItem()
            ;

            if($region->getId())
                return $region->getId();
            else
                return null;
        }
    }

    public function getCurrentOccasion()
    {
        $occasionObj_id = $this->getCurrentOccasionId();
        $occasionObj = Mage::getModel('specialoccasion/specialoccasion')->load($occasionObj_id);
        if ($occasionObj != null && $occasionObj->getId())
            return $occasionObj;
        else
            return null;
    }

    public function getCurrentOccasionId($customer_id = null)
    {
        if ($customer_id == null)
            $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();

        $occasionObj = Mage::getModel('specialoccasion/specialoccasion')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->getFirstItem();

        if ($occasionObj->getId()) {
            $occasionObjId = $occasionObj->getId();
        } else {
            $_occasionObj = Mage::getModel('specialoccasion/specialoccasion');
            $_occasionObj->setData('customer_id', $customer_id);
            $_occasionObj->setData('created_at', now());
            $_occasionObj->setData('updated_at', now());
            $_occasionObj->save();
            $occasionObjId = $_occasionObj->getId();
        }

        return $occasionObjId;
    }

    /**
     * @param $customer
     * @param $products
     * @return $this
     */
    public function sendEmailRemind($customer, $item = null)
    {
        $store = Mage::app()->getStore();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        if (!$customer->getId())
            return $this;

        $email_path =  Mage::getStoreConfig(self::XML_PATH_EMAIL_REMIND, $store);

        $data = array(
            'store' => $store,
            'customer_name' => $customer->getName(),
            'items' => $item
        );

        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => Mage::app()->getStore()->getId()
            ))->sendTransactional(
                $email_path,
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store->getId()),
                $customer->getEmail(),
                $customer->getName(),
                $data
            );

        $translate->setTranslateInline(true);
        return $this;
    }

    public function getItemData($item_id)
    {
        $item = Mage::getModel('specialoccasion/item')->load($item_id);
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $price_options = 0;

        $option_params = json_decode($item->getOptionParams(), true);
        $product_url = Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/edit",array("id"=>$product->getId()));

        $name = '<a href="'.$product_url.'" target="_blank"><strong>'.$product->getName().'</strong></a>';
        $name .= '<dl class="item-options">';
        if ($product->getTypeId() == 'configurable') {
            $_options = Mage::helper('speicaloccasion')->getConfigurableOptionProduct($product);
            if ($_options && sizeof($option_params) > 0){
                foreach ($_options as $_option){
                    $_attribute_value = 0;
                    foreach($option_params as $k=>$v){
                        if($k == $_option['attribute_id']){
                            $_attribute_value = $v;
                            break;
                        }
                    }

                    if($_attribute_value > 0){
                        $name .= '<dt>'.$_option['label'].'</dt>';
                        foreach($_option['values'] as $val){

                            if($val['value_index'] == $_attribute_value){
                                $name .= '<dd>'.$val['default_label'].'</dd>';
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($product->getOptions() as $o) {
                $values = $o->getValues();
                $_attribute_value = 0;

                foreach($option_params as $k=>$v){
                    if($k == $o->getOptionId()){
                        $_attribute_value = $v;
                        break;
                    }
                }
                if($_attribute_value > 0){
                    $name .= '<dt>'.$o->getTitle().'</dt>';
                    foreach($values as $val){
                        if(is_array($_attribute_value)){
                            if(in_array($val->getOptionTypeId(), $_attribute_value)) {
                                $name .= '<dd>'.$val->getTitle().'</dd>';
                                $price_options += $val->getPrice();
                            }
                        } else if($val->getOptionTypeId() == $_attribute_value){
                            $name .= '<dd>'.$val->getTitle().'</dd>';
                            $price_options += $val->getPrice();
                        }
                    }
                }
            }
        }
        $name .= '</dl>';

        $price = ($product->getFinalPrice() + $price_options)*$item->getQty();

        return array(
            'item_name' => $name,
            'item_price' => $price,
            'product_id' => $product->getId(),
        );
    }

    public function getItemPrice($item)
    {
        $data = $this->getItemData($item->getId());
        return $data['item_price'];
    }
	
}
