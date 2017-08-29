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
 * Domain Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Model_Domain extends Mage_Core_Model_Abstract {

    const XML_PATH_EMAIL_IDENTITY = 'trans_email/ident_sales';
    const XML_PATH_OTHER_ACCOUNT_VERIFY_DOMAIN_EMAIL = 'affiliateplus/directlink/other_account_verify_domain_email_template';

    public function _construct() {
        parent::_construct();
        $this->_init('affiliateplusdirectlink/domain');
    }

    public function getDomainCollectionByAccountId($accountId) {
        $collection = $this->getCollection()
                ->addFieldToFilter('account_id', $accountId);
        return $collection;
    }

    public function getDomainOtherAccountByName($domain, $accountId) {
        $collection = $this->getCollection()
                ->addFieldToFilter('domain', $domain)
                ->addFieldToFilter('status', 2)
                ->addFieldToFilter('account_id', array('nin' => $accountId));
        return $collection;
    }

    public function saveDomainAccount($domain, $accountId) {
        $collection = $this->getCollection()
                ->addFieldToFilter('domain', $domain)
                ->addFieldToFilter('account_id', $accountId);
        if ($collection->getSize()) {
            $model = $collection->getFirstItem();
            $model->setAccountId($accountId)
                    ->setDomain($domain)
                    ->setUpdateTime(now());
        } else {
            $model = $this;
            $model->setAccountId($accountId)
                    ->setDomain($domain)
                    ->setCreatedTime(now())
                    ->setUpdateTime(now());
        }
        try {
            $model->save();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return $model;
    }

    public function sendMailDomainDisable($domain, $accountId) {

        $domain_collection = $this->getDomainOtherAccountByName($domain, $accountId);

        if (!$domain_collection->getSize())
            return;
        $sendTo = array();
        $store = Mage::getModel('core/store')->load($this->getStoreId());

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getStoreConfig(self::XML_PATH_OTHER_ACCOUNT_VERIFY_DOMAIN_EMAIL, $store->getId());
        foreach ($domain_collection as $value) {
            $account = Mage::getModel('affiliateplus/account')->load($value->getAccountId());
            $vars = array(
            'account' => $account->getName(),
            'domain' => $domain,
            );
            $sendTo[] = array(
                'email' => $account->getEmail(),
                'name' => $account->getName(),
                'var'=>$vars,
            );
        }
        $mailSubject = 'Verify Domain';
        $storeId = Mage::app()->getStore()->getId();
        
        $mailTemplate = Mage::getModel('core/email_template');
        $sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $store->getId());
        foreach ($sendTo as $recipient) {
            $mailTemplate->setTemplateSubject($mailSubject)
                    ->sendTransactional($template, $sender, $recipient['email'], $recipient['name'], $recipient['var'], $storeId);
            $translate->setTranslateInline(true);
        }
        $translate->setTranslateInline(true);
        return $this;
    }
    public function isDomainExited($domainId) {
        $data = Mage::app()->getRequest()->getParams();
        $data['domain'] = Mage::helper('affiliateplusdirectlink')->refineDomain($data['domain']);
        $collection = $this->getCollection()
                ->addFieldToFilter('domain', $data['domain'])
                ->addFieldToFilter('account_id', $data['account_id']);
        if ($domainId) {
            $collection->addFieldToFilter('domain_id', array('nin' => $domainId));
        }
        if ($collection->getSize()) {
            return TRUE;
        }
        return FALSE;
    }

}