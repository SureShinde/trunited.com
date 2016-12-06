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
 * @package     Magestore_AffiliateplusBanner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusbanner Custom Link Form Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Verify extends Mage_Core_Block_Template
{
    private $_domain=null;
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('affiliateplusdirectlink/verify.phtml');
    }
    public function setDomain($id) {
        return $this->_domain = Mage::getModel('affiliateplusdirectlink/domain')->load($id);
    }

    public function getDomain() {
        return $this->_domain->getDomain();
    }
    public function getDomainId() {
        return $this->_domain->getId();
    }
    public function getFile(){
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $verifynamehtml = 'verify_domain_' . md5($account->getId() . $this->getDomain().'1905') . '.html';
        return $verifynamehtml;
    }
}