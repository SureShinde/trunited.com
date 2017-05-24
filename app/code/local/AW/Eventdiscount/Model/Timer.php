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
    class AW_Eventdiscount_Model_Timer_Abstract extends  Mage_Rule_Model_Rule
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
        return $this;
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        if (Mage::app()->getFrontController()->getRequest()->getActionName() !== 'massStatus') {
        $collection = Mage::getModel('aweventdiscount/action')->getCollection();
        $collection->deleteByTimerId($this->getId());
        $model = Mage::getModel('aweventdiscount/action');
        if ($this->hasData('actions_to_save'))
            foreach ($this->getData('actions_to_save') as $item) {
                if ($item['type'] === AW_Eventdiscount_Model_Source_Action::CHANGE_GROUP) {
                    $model->setData('action', $item['group']);
                } else {
                    if (empty($item['amount'])) {
                        throw new Exception(Mage::helper('eventdiscount')->__('Empty action'));
                    }
                    $model->setData('action', $item['amount']);
                }
                $model->setData('type', $item['type']);
                $model->setData('timer_id', $this->getId());
                $model->save();
                $model->setData('id', null);
            }
        }
        return $this;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
        }
        ;
        if (is_array($this->getStoreIds())) {
            $this->setStoreIds(join(',', $this->getStoreIds()));
        }
        ;
        return $this;
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        $collection = Mage::getModel('aweventdiscount/action')->getCollection();
        $collection->deleteByTimerId($this->getId());
        return $this;
    }
}