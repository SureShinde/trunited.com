<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointscoupon Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Model_Mysql4_Coupon extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('rewardpointscoupon/coupon', 'coupon_id');
        $this->addUniqueField(array(
            'field' => 'code',
            'title' => Mage::helper('rewardpointscoupon')->__('Reward code with the same code')
        ));
    }

    public function loadPrimaryByTemplate(Magestore_RewardPointsCoupon_Model_Coupon $object, $template) {
        $read = $this->_getReadAdapter();

        if ($template instanceof Magestore_RewardPointsCoupon_Model_Template) {
            $templateId = $template->getId();
        } else {
            $templateId = (int) $template;
        }

        $select = $read->select()->from($this->getMainTable())
                ->where('template_id = :template_id')
                ->where('is_primary = :is_primary');

        $data = $read->fetchRow($select, array(':template_id' => $templateId, ':is_primary' => 1));
        if (!$data) {
            return false;
        }
        $object->setData($data);

        $this->_afterLoad($object);
        return true;
    }

    /**
     * Check if code exists
     *
     * @param string $code
     * @return bool
     */
    public function exists($code) {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), 'code');
        $select->where('code = :code');

        if ($read->fetchOne($select, array('code' => $code)) === false) {
            return false;
        }
        return true;
    }

}
