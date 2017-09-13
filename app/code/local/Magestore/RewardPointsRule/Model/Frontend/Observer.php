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
 * @package     Magestore_RewardPointsRule
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsRule Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsRule
 * @author      Magestore Developer
 */
class Magestore_RewardPointsRule_Model_Frontend_Observer {

    /**
     * 
     * @param type $observer
     * @return Magestore_RewardPointsRule_Model_Frontend_Observer
     */
    public function dashboardCanShowEarning($observer) {
        if (!Mage::helper('rewardpointsrule')->isEnabled()) {
            return $this;
        }
        $container = $observer['container'];
        if (!$container->getCanShow()) {
            $block = Mage::getBlockSingleton('rewardpointsrule/account_dashboard_earn');
            if (count($block->getCatalogRules()) || count($block->getShoppingCartRules())) {
                $container->setCanShow(true);
            }
        }
        return $this;
    }

    /**
     * 
     * @param type $observer
     * @return Magestore_RewardPointsRule_Model_Frontend_Observer
     */
    public function dashboardCanShowSpending($observer) {
        if (!Mage::helper('rewardpointsrule')->isEnabled()) {
            return $this;
        }
        $container = $observer['container'];
        if (!$container->getCanShow()) {
            $block = Mage::getBlockSingleton('rewardpointsrule/account_dashboard_spend');
            if (count($block->getCatalogRules()) || count($block->getShoppingCartRules())) {
                $container->setCanShow(true);
            }
        }
        return $this;
    }

    /**
     * Check to show reward points core earning on product
     * 
     * @param type $observer
     * @return Magestore_RewardPointsRule_Model_Frontend_Observer
     */
    public function showEarningOnProduct($observer) {
        if (!Mage::helper('rewardpointsrule')->isEnabled()) {
            return $this;
        }
        $container = $observer['container'];
        if ($container->getEnableDisplay()) {
            $block = Mage::getBlockSingleton('rewardpointsrule/product_view_earning');
            if ($block->getEarningPoints() > 0) {
                $container->setEnableDisplay(false);
            }
        }
        return $this;
    }

    public function showEarningOnCategory($observer) {
		if (!Mage::helper('rewardpointsrule')->isEnabled()) {
            return $this;
        }
        $block = $observer['block'];
        if ($block instanceof Mage_Catalog_Block_Product_Price) {
            $requestPath = $block->getRequest()->getRequestedRouteName()
                    . '_' . $block->getRequest()->getRequestedControllerName()
                    . '_' . $block->getRequest()->getRequestedActionName();
            $product = $block->getProduct();      
            $transport = $observer['transport'];  
            if($product->getRewardpointsSpend() && $requestPath == 'catalog_product_view' && strpos($transport->getHtml(),Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol())!==false){
                $html = '<div class="price-box">
                            <span id="fix-product-price-'.$product->getId().'" class="regular-price">
                                <span class="price">'.Mage::helper('rewardpoints')->__('Buy with %s',Mage::helper('rewardpoints/point')->formatProductCredit($product->getRewardpointsSpend(),Mage::app()->getStore()->getStoreId())).'</span>
                            </span>
                        </div>';
                $transport->setHtml($html);
                return $this;
            }
            if ($requestPath != 'catalog_category_view' && $requestPath != 'catalogsearch_result_index') {
                return;
            }
            $points = 0;
            if($product->getRewardpointsEarn() != null){
                $points = $product->getRewardpointsEarn();
            } else {
                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                $websiteId = Mage::app()->getWebsite()->getId();
                $earningProductCollection = Mage::getModel('rewardpointsrule/earning_product')->getCollection()
                        ->addFieldToFilter('customer_group_id', $customerGroupId)
                        ->addFieldToFilter('website_id', $websiteId)
                        ->addFieldToFilter('product_id', $product->getId());
                if (count($earningProductCollection)) {
                    $points = $earningProductCollection->getFirstItem()->getData('earning_point');
                }
            }
//            if (!$points || $points <= 0) return $this;
              
            if($product->getRewardpointsSpend()){
                $html = '<div class="price-box">
                            <span id="product-price-'.$product->getId().'" class="regular-price">
                                <span class="price">'.Mage::helper('rewardpoints')->__('Buy with %s',Mage::helper('rewardpoints/point')->formatProductCredit(
                        $product->getRewardpointsSpend(), Mage::app()->getStore()->getStoreId())).'</span>
                            </span>
                        </div>';
                $transport->setHtml($html);
                return $this;
            }else{
                $html = $transport->getHtml(); 
            }
            $pointHtml = '';
            /*$pointHtml = '<div class="earning-product-list">';
            if(Mage::helper('rewardpointsrule')->getCanShow(Mage::app()->getStore()) && $points>0)     
            $pointHtml .= Mage::helper('rewardpoints')->__(' Earn %s Profit Points',$points);
            $pointHtml .='</div>';*/
            $html = $pointHtml.$html;
            $transport->setHtml($html);
        }
    }

    public function sales_quote_item_save_before($observer){
		if (!Mage::helper('rewardpointsrule')->isEnabled()) {
            return $this;
        }
        $item = $observer->getItem();
        // if already checked
        if(Mage::registry('check'.$item->getId()))
            return $this;
        Mage::register('check'.$item->getId(),1);

        $productid = Mage::app()->getRequest()->getParam('product');
        if(!$productid){
            $productid = $item->getProductId();
        }
        $product = Mage::getModel('catalog/product')->load($productid);
        if(!$product->getRewardpointsSpend())
            return $this;
        $session = Mage::getSingleton('checkout/session');
        $customerPoints = Mage::helper('rewardpoints/customer')->getProductCredit();
        $customerPoints -= Mage::helper('rewardpointsrule/calculation_spending')->getPointItemSpent();
        $customerPoints +=Mage::helper('rewardpointsrule/calculation_spending')->getPointItemSpent($item);
        $qty = (int)($customerPoints/$product->getRewardpointsSpend());
        if($qty && $qty < $item->getQty()){
            $item->setQty($qty)->save();
            $session->addError(Mage::helper('rewardpoints')->__('You don\'t have enough credits to buy more quantiy of %s.',$product->getName()));
        }
        if($qty){
            $item->setCustomPrice(0);
            $item->setOriginalCustomPrice(0);
            $item->getProduct()->setIsSuperMode(true);
            $item->setRewardpointsSpent($item->getQty() * $product->getRewardpointsSpend());
            $catalogRules = $session->getCatalogRules();
            if (!is_array($catalogRules)) {
                $catalogRules = array();
            }
            $catalogRules[$item->getId()] = array(
                'item_id'   => $item->getId(),
                'item_qty'  => $item->getQty(),
                'rule_id'   => 0,
                'point_used'    => $product->getRewardpointsSpend(),
                'base_point_discount'   => null,
                'point_discount'        => null,
                'type'      => 'catalog_spend'
            );
            $session->setCatalogRules($catalogRules);
        }else{
            $session->getQuote()->removeItem($item->getId())->save();
            $session->setRewardError(Mage::helper('rewardpoints')->__('This product can be purchased by credits only. Need more credits to get it!'));
            Mage::throwException(Mage::helper('rewardpoints')->__('This product can be purchased by credits only. Need more credits to get it!'));
        }
    }
    
    public function controller_action_predispatch($observer){
		if (!Mage::helper('rewardpointsrule')->isEnabled()) {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');
        if($session->getRewardError()){
            $session->addError($session->getRewardError());
        }
        $session->setRewardError('');
        $quote = $session->getQuote();
        foreach ($quote->getAllItems() as $item) {
            $productid = $item->getProductId();
            $product = Mage::getModel('catalog/product')->load($productid);
            if(!is_object($product) || !$product->getId() || !$product->getRewardpointsSpend()){ continue;}
            $customerPoints = Mage::helper('rewardpoints/customer')->getProductCredit();
            $customerPoints -= Mage::helper('rewardpointsrule/calculation_spending')->getPointItemSpent();
            $customerPoints +=Mage::helper('rewardpointsrule/calculation_spending')->getPointItemSpent($item);
            $qty = (int)($customerPoints/$product->getRewardpointsSpend());
            if($qty && $qty < $item->getQty()){
                $item->setQty($qty)->save();
                $session->addError(Mage::helper('rewardpoints')->__('You don\'t have enough credits to buy more quantiy of %s.',$product->getName()));
            }
            if($qty){
                $item->setCustomPrice(0);
                $item->setOriginalCustomPrice(0);
                $item->getProduct()->setIsSuperMode(true);
                $item->setRewardpointsSpent($qty*$product->getRewardpointsSpend());
                $catalogRules = $session->getCatalogRules();
                if (!is_array($catalogRules)) {
                    $catalogRules = array();
                }
                $catalogRules[$item->getId()] = array(
                    'item_id'   => $item->getId(),
                    'item_qty'  => $item->getQty(),
                    'rule_id'   => 0,
                    'point_used'    => $product->getRewardpointsSpend(),
                    'base_point_discount'   => null,
                    'point_discount'        => null,
                    'type'      => 'catalog_spend'
                );
                $session->setCatalogRules($catalogRules);
            }else{                            
                $quote->removeItem($item->getId())->save();
                $session->addError(Mage::helper('rewardpoints')->__('Not enough points to buy %s.',$product->getName()));
               // Mage::throwException(Mage::helper('rewardpoints')->__('This product can be purchased by points only. Need more points to get it!'));
            }   
        }
    }
	
    public function couponPost($observer) {
        $action = $observer->getEvent()->getControllerAction();
        $code = trim($action->getRequest()->getParam('coupon_code'));
        if(!strlen($code)){
            return $this;
        }
		$session = Mage::getSingleton('checkout/session');
		$quote = $session->getQuote();
		$address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $customerGroupId = $quote->getCustomerGroupId();
        $websiteId = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
        $date = now(true);
		$earningRuleId = '';
		
		$couponEarningRules = Mage::getResourceModel('rewardpointsrule/earning_sales_collection')
							->setAvailableFilter($customerGroupId, $websiteId, $date)
							->addFieldToFilter('coupon_code',$code);
							
		if($couponEarningRules->getSize() > 0){
			$rule = $couponEarningRules->getFirstItem();
            $rule->afterLoad();
            if ($rule->validate($address)) {
				$earningRuleId = $rule->getId();
			}
		}
		if ($action->getRequest()->getParam('remove') == 1) {
			if($session->getData('coupon_code') == $code){
                $session->addSuccess(Mage::helper('rewardpointsrule')->__('Coupon code "%s" was canceled.', $session->getData('coupon_code')));
				$session->setData('coupon_code', '');
				$session->setData('reward_shoppingcart_earning_rule_id', '');
			}else{
				return $this;
			}
		}else{
            if ($earningRuleId) {
                $session->addSuccess(Mage::helper('rewardpointsrule')->__('Coupon code "%s" was applied.', $code));
				$session->setData('coupon_code', $code);
				$session->setData('reward_shoppingcart_earning_rule_id', $earningRuleId);
				$quote->setCouponCode($code);
				$quote->collectTotals()->save();
            }else{
				$session->setData('coupon_code', '');
				$session->setData('reward_shoppingcart_earning_rule_id', '');
				return $this;
			}
		}
		
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
		$action->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
	}
	
    public function couponPostOSC($observer) {
        $action = $observer->getEvent()->getControllerAction();
        $code = trim($action->getRequest()->getParam('coupon_code'));
        if(!strlen($code)){
            return $this;
        }
		$session = Mage::getSingleton('checkout/session');
		$quote = $session->getQuote();
		$address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $customerGroupId = $quote->getCustomerGroupId();
        $websiteId = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
        $date = now(true);
		$earningRuleId = '';
		
		$couponEarningRules = Mage::getResourceModel('rewardpointsrule/earning_sales_collection')
							->setAvailableFilter($customerGroupId, $websiteId, $date)
							->addFieldToFilter('coupon_code',$code);
							
		if($couponEarningRules->getSize() > 0){
			$rule = $couponEarningRules->getFirstItem();
            $rule->afterLoad();
            if ($rule->validate($address)) {
				$earningRuleId = $rule->getId();
			}
		}
		if ($action->getRequest()->getParam('remove') == 1) {
			if($session->getData('coupon_code') == $code){
				$error = false;
				$message = Mage::helper('rewardpointsrule')->__('Coupon code "%s" was canceled.', $session->getData('coupon_code'));
				$quote->setCouponCode('');
				$session->setData('coupon_code', '');
				$session->setData('reward_shoppingcart_earning_rule_id', '');
			}else{
				return $this;
			}
		}else{
            if ($earningRuleId) {
				$error = false;
				$message = Mage::helper('rewardpointsrule')->__('Coupon code "%s" was applied.', $code);
				$session->setData('coupon_code', $code);
				$session->setData('reward_shoppingcart_earning_rule_id', $earningRuleId);
				$quote->setCouponCode($code);
				$quote->collectTotals()->save();
            }else{
				$session->setData('coupon_code', '');
				$session->setData('reward_shoppingcart_earning_rule_id', '');
				return $this;
			}
		}
		
		$result = array(
            'error' => $error,
            'message' => $message
        );
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        $action->getResponse()->setBody(Zend_Json::encode($result));
	}
}
