<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Eventdiscount
 * @version    1.0.5
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

if (AW_Eventdiscount_Helper_Data::isNewRules()) {
    abstract class AW_Eventdiscount_Model_Timer_Abstract extends Mage_Rule_Model_Abstract
    {
        public function getConditionsInstance()
        {
            return Mage::getModel('salesrule/rule_condition_combine');
        }

        public function getActionsInstance()
        {
            return Mage::getModel('salesrule/rule_condition_product_combine');
        }
    }
} else {
    class AW_Eventdiscount_Model_Timer_Abstract extends Mage_Rule_Model_Rule
    {
        public function getConditionsInstance()
        {
            return Mage::getModel('salesrule/rule_condition_combine');
        }

        public function getActionsInstance()
        {
            return Mage::getModel('salesrule/rule_condition_product_combine');
        }

    }
}

class AW_Eventdiscount_Model_Timer extends AW_Eventdiscount_Model_Timer_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aweventdiscount/timer');
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $collection = Mage::getModel('aweventdiscount/action')->getCollection()->loadByTimerId($this->getId());
        $this->setData('action_values', $collection->getData());

        $collection_giftCard = Mage::getModel('aweventdiscount/giftcard')->getCollection()->loadByTimerId($this->getId());
        $this->setData('giftcard_values', $collection_giftCard->getData());

        return $this;
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        if (Mage::app()->getFrontController()->getRequest()->getActionName() !== 'massStatus') {
            $collection = Mage::getModel('aweventdiscount/action')->getCollection();
            $collection->deleteByTimerId($this->getId());
            $model = Mage::getModel('aweventdiscount/action');
            if ($this->hasData('actions_to_save')) {
                foreach ($this->getData('actions_to_save') as $item) {
                    if ($item['type'] === AW_Eventdiscount_Model_Source_Action::CHANGE_GROUP) {
                        $model->setData('action', $item['group']);
                    } else {
                        if (empty($item['amount'])) {
                            $item['amount'] = 0;
                        }
                        $model->setData('action', $item['amount']);
                        $model->setData('subtotal_from', $item['subtotal_from']);
                        $model->setData('subtotal_to', $item['subtotal_to']);
                    }
                    $model->setData('type', $item['type']);
                    $model->setData('timer_id', $this->getId());
                    $model->save();
                    $model->setData('id', null);
                }
            }

            $collection_giftCard = Mage::getModel('aweventdiscount/giftcard')->getCollection();
            $collection_giftCard->deleteByTimerId($this->getId());
            $model_giftCard = Mage::getModel('aweventdiscount/giftcard');
            if ($this->hasData('giftcard_to_save')) {
                foreach ($this->getData('giftcard_to_save') as $item) {
                    $model_giftCard->setData('amount_from', $item['amount_from']);
                    $model_giftCard->setData('amount_to', $item['amount_to']);
                    $model_giftCard->setData('reward_new_customer', $item['reward_new_customer']);
                    $model_giftCard->setData('reward_referrer', $item['reward_referrer']);
                    $model_giftCard->setData('timer_id', $this->getId());
                    $model_giftCard->save();
                    $model_giftCard->setData('id', null);
                }
            }

        }
        return $this;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
        };
        if (is_array($this->getStoreIds())) {
            $this->setStoreIds(join(',', $this->getStoreIds()));
        };
        return $this;
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        $collection = Mage::getModel('aweventdiscount/action')->getCollection();
        $collection->deleteByTimerId($this->getId());

        $collection_giftCard = Mage::getModel('aweventdiscount/giftcard')->getCollection();
        $collection_giftCard->deleteByTimerId($this->getId());

        $collection_trigger = Mage::getModel('aweventdiscount/trigger')->getCollection();
        $collection_trigger->deleteByTimerId($this->getId());

        return $this;
    }

    public function cmsToArray()
    {
        $collection = Mage::getModel('cms/page')->getCollection()
            ->addFieldToSelect('identifier')
            ->addFieldToSelect('title')
            ->setOrder('page_id','desc')
        ;

        $rs = array();
        if(sizeof($collection) > 0){
            foreach($collection as $cms)
            {
                $rs[$cms->getIdentifier()] = $cms->getTitle();
            }
        }

        return $rs;
    }
}