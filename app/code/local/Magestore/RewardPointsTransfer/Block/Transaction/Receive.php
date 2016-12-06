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
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardpointsTransfer Receive Transactions
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Block_Transaction_Receive extends Magestore_RewardPointsTransfer_Block_Rewardpointstransfer
{
    /**
     * construct
     */
    protected function _construct()
    {
        parent::_construct();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $collection = Mage::getResourceModel('rewardpointstransfer/rewardpointstransfer_collection')
            ->addFieldToFilter('receiver_email', $customer->getEmail());
        $collection->getSelect()->order('created_time DESC');
        $this->setCollection($collection);
    }
    
    /**
     * prepare layout
     * @return \Magestore_RewardPointsTransfer_Block_Transaction_Receive
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'receivetransfer')
            ->setCollection($this->getCollection());
        //$pager->setAvailableLimit(array(5=>5,10=>10));
        $this->setChild('receivetransfer_pager', $pager);
        //$this->getCollection()->load();
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('receivetransfer_pager');
    }
}
