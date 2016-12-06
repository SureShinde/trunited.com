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
 * @package     Magestore_affiliateplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * affiliateplus Tab on Customer Edit Form Block
 * 
 * @category    Magestore
 * @package     Magestore_affiliateplus
 * @author      Magestore Developer
 */
class Magestore_Affiliateplus_Block_Adminhtml_Customer_Edit_Tab_Affiliate
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getCustomerId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function getAffiliateAccount()
    {
        $customer_id = $this->getCustomerId();
        $affiliate = Mage::getModel('affiliateplus/account')->load($customer_id,'customer_id');
        if($affiliate->getId())
            return $affiliate;
        else
            return null;
    }

    public function getAffiliateReferredAccount()
    {
        $customer_id = $this->getCustomerId();
        $tracking = Mage::getModel('affiliateplus/tracking')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->setOrder('tracking_id','desc')
            ->getFirstItem()
            ;

        if($tracking->getId()){
            $affiliate = Mage::getModel('affiliateplus/account')->load($tracking->getAccountId());
            if($affiliate->getId())
                return $affiliate;
            else
                return null;
        } else {
            return null;
        }
    }
    /**
     * prepare tab form's information
     *
     * @return Magestore_affiliateplus_Block_Adminhtml_Customer_Edit_Tab_affiliateplus
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('affiliateplus_');
        $this->setForm($form);

        $account = $this->getAffiliateAccount();

        $storeId = $this->getRequest()->getParam('store');
        if ($storeId) {
            $store = Mage::getModel('core/store')->load($storeId);
        } else {
            $store = Mage::app()->getStore();
        }

        $fieldset = $form->addFieldset('affiliateplus_form', array(
            'legend' => Mage::helper('affiliateplus')->__('Affiliate Information')
        ));
		
		if($account != null){
			$fieldset->addField('name', 'note', array(
				'label' => Mage::helper('affiliateplus')->__('Name'),
				'title' => Mage::helper('affiliateplus')->__('Name'),
				'text'  => '<strong>' . $account != null ? '<a href="' . $this->getUrl('adminhtml/affiliateplus_account/edit', array('id' => $account->getId())) . '" target="_blank" title="' . $account->getName() . '">' . $account->getName() . '</a>' : '' . '</strong>',
			));

			$fieldset->addField('email', 'note', array(
				'label' => Mage::helper('affiliateplus')->__('Email'),
				'title' => Mage::helper('affiliateplus')->__('Email'),
				'text'  => '<strong>' . $account != null ? $account->getEmail() : '' . '</strong>',
			));

			$fieldset->addField('balance', 'note', array(
				'label' => Mage::helper('affiliateplus')->__('Balance'),
				'title' => Mage::helper('affiliateplus')->__('Balance'),
				'text'  => '<strong>' . $account != null ? $store->convertPrice($account->getBalance(), true, true) : '' . '</strong>',
			));
		} else {
			$fieldset->addField('name', 'note', array(
				'label' => Mage::helper('affiliateplus')->__('No Affiliate Account'),
				'title' => Mage::helper('affiliateplus')->__('No Affiliate Account'),
				'text'  => '',
			));
		}

        /** Affiliate Referred The Customer **/
        $referred = $this->getAffiliateReferredAccount();
        $fieldset_referred = $form->addFieldset('affiliateplus_referred_form', array(
            'legend' => Mage::helper('affiliateplus')->__('Affiliate Referred Information')
        ));
		
		if($referred != null){
			$fieldset_referred->addField('referred_name', 'note', array(
				'label' => Mage::helper('affiliateplus')->__('Name'),
				'title' => Mage::helper('affiliateplus')->__('Name'),
				'text'  => '<strong>' . $referred != null ? '<a href="' . $this->getUrl('adminhtml/affiliateplus_account/edit', array('id' => $referred->getId())) . '" target="_blank" title="' . $referred->getName() . '">' . $referred->getName() . '</a>' : '' . '</strong>',
			));

			$fieldset_referred->addField('referred_email', 'note', array(
				'label' => Mage::helper('affiliateplus')->__('Email'),
				'title' => Mage::helper('affiliateplus')->__('Email'),
				'text'  => '<strong>' . $referred != null ? $referred->getEmail() : '' . '</strong>',
			));

			$fieldset_referred->addField('referred_balance', 'note', array(
				'label' => Mage::helper('affiliateplus')->__('Balance'),
				'title' => Mage::helper('affiliateplus')->__('Balance'),
				'text'  => '<strong>' . $referred != null ? $store->convertPrice($referred->getBalance(), true, true) : '' . '</strong>',
			));
		} else {
			$fieldset_referred->addField('referred_name', 'note', array(
				'label' => Mage::helper('affiliateplus')->__('No Affiliate Referred'),
				'title' => Mage::helper('affiliateplus')->__('No Affiliate Referred'),
				'text'  => '',
			));
		}

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('affiliateplus')->__('Affiliate Account');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('affiliateplus')->__('Affiliate Account');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
}
