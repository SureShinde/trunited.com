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
 * AffiliateplusDirectLink Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Model_Observer {

    /**
     * Check is direct link
     *
     * @return Magestore_AffiliateplusDirectLink_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        $request = $observer['request'];
        if ($request->getParam('acc'))
            return $this;

        $httpReferrer = $request->getServer('HTTP_REFERER');
        if (!$httpReferrer)
            return $this;

        $httpReferrerInfo = parse_url($httpReferrer);
        $domain = isset($httpReferrerInfo['host']) ? $httpReferrerInfo['host'] : '';
        if (!$domain)
            return $this;
        /*edi by blanka*/
        if (isset($httpReferrerInfo['port']) && $httpReferrerInfo['port'])
            $domain.=':' . $httpReferrerInfo['port'];
        /*edit by blanka*/
        $path = isset($httpReferrerInfo['path']) ? trim($httpReferrerInfo['path'], '/') : '';
        $directCollection = Mage::getResourceModel('affiliateplusdirectlink/directlink_collection');
        $directCollection->getSelect()
                ->joinLeft(
                        array('d' => $directCollection->getTable('affiliateplusdirectlink/domain')), 'main_table.domain_id = d.domain_id', array('account_id' => 'account_id')
                )->where('d.domain = ?', $domain)
                ->where("(main_table.source_page = '') OR (main_table.source_page = '$path')")
                ->where("d.status = 2")
                ->where('main_table.status = 2')
                ->order('main_table.source_page DESC');
        $direct = $directCollection->getFirstItem();
        if ($direct && $direct->getId()) {
            if ($request->getRequestedRouteName() != 'affiliateplus'
                    || $request->getRequestedControllerName() != 'banner'
                    || $request->getRequestedActionName() != 'image'
            ) {
                $account = Mage::getModel('affiliateplus/account')->load($direct->getAccountId());

                $request->setParam('acc', $account->getIdentifyCode());
                $request->setParam('bannerid', $direct->getBannerId());
            }
            $request->setParam('affiliateplus_direct_link', $direct->getId());
        }
        return $this;
    }

}
