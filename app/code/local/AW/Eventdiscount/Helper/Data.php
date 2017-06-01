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

class AW_Eventdiscount_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static function customerGroupsToArray()
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()->toOptionArray();
        return $customerGroups;
    }

    public function isEditAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('eventdiscount/timer/new');
    }

    public function isViewAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('eventdiscount/timer/list');
    }

    public function getExtDisabled()
    {
        return Mage::getStoreConfig('advanced/modules_disable_output/AW_Eventdiscount');
    }

    public function getCustomerGroupId()
    {
        $_customerGroupId = 0;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $_customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        return $_customerGroupId;
    }

    public static function isNewRules()
    {
        if ((version_compare(Mage::getVersion(), '1.7.0.0', '>=')
            && Mage::helper('awall/versions')->getPlatform() == AW_ALL_Helper_Versions::CE_PLATFORM) ||
            (version_compare(Mage::getVersion(), '1.12.0.0', '>=')
                && Mage::helper('awall/versions')->getPlatform() == AW_ALL_Helper_Versions::EE_PLATFORM)
        ) {
            return true;
        }
        return false;
    }

    public function saveTimerProduct($timer_id, $product_ids)
    {
        $collection = $this->getTimerProductCollection($timer_id);

        $transactionSave = Mage::getModel('core/resource_transaction');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $connection->beginTransaction();

            if(sizeof($collection) > 0 && sizeof($product_ids) > 0)
            {
                foreach ($collection as $timer_product) {
                    $timer_product->delete();
                }
            }

            if(sizeof($product_ids) > 0)
            {
                foreach ($product_ids as $pid) {
                    $model = Mage::getModel('aweventdiscount/product');
                    $_data = array(
                        'timer_id' => $timer_id,
                        'product_id'    => $pid
                    );
                    $model->setData($_data);
                    $transactionSave->addObject($model);
                }
                $transactionSave->save();
            }

            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
        }
    }

    public function getTimerProductCollection($timer_id)
    {
        $collection = Mage::getModel('aweventdiscount/product')->getCollection()
            ->addFieldToFilter('timer_id', $timer_id)
            ->setOrder('timer_product_id', 'desc')
        ;

        return $collection;
    }

    public function getProductByTimerId($timer_id)
    {
        return $this->getTimerProductCollection($timer_id)->getColumnValues('product_id');
    }

    public function removeProductByTimerId($timer_id)
    {
        $collection = $this->getTimerProductCollection($timer_id);

        if(sizeof($collection) > 0)
        {
            foreach ($collection as $product) {
                $product->delete();
            }
        }
    }

    public function getTypeNotifyConfiguration($store = null)
    {
        return Mage::getStoreConfig('awall/eventdiscount/type_notify', $store);
    }
}