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
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Directlink Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Model_Directlink extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('affiliateplusdirectlink/directlink');
    }

    public function DomainChangeStatus($domain_id,$status) {
        $collection = $this->getCollection()
                ->addFieldToFilter('domain_id', $domain_id);
        foreach ($collection as $directlink) {
            $directlink->setStatus($status)->save();
        }
        return $this;
    }

    public function isDirectLinkExited($directlinkId) {
        $data = Mage::app()->getRequest()->getParams();
        $domain = Mage::helper('affiliateplusdirectlink')->refineDomain($data['source_page']);
        $data['source_page'] = Mage::helper('affiliateplusdirectlink')->refineSourcePage($data['source_page']);
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $accountId = $data['account_id'];
        if (!$accountId)
            $accountId = $account->getId();
        $collection = $this->getCollection()
                ->addFieldToFilter('source_page', $data['source_page']);
        if ($directlinkId) {
            $collection->addFieldToFilter('direct_link_id', array('nin' => $directlinkId));
        }
        $collection->getSelect()
                ->join(array('adld' => Mage::getModel('core/resource')->getTableName('affiliateplus_direct_link_domain')), 'main_table.domain_id =adld.domain_id AND adld.account_id=' . $accountId);
        $collection->addFieldToFilter('domain', $domain);
        if ($collection->getSize()) {
            return TRUE;
        }
        return FALSE;
    }

}