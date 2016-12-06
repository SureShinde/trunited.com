<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/
require_once 'Mage/Checkout/controllers/CartController.php';

class Indies_Recurringandrentalpayments_CartController extends Mage_Checkout_CartController
{
	
	/**
     * Add product to shopping cart action
     */
    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
	
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');
            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }
			if(isset($params['super_group']) && !empty($params['super_group']) )
			{
				$data  = $params['super_group'];
				$p_qty = 0 ;
				foreach($data as $key=>$d)
				{
					if($d > 0 )
					{
						$opt = array(                
								'qty' => $d,								
								'indies_recurringandrentalpayments_subscription_type' =>$params['indies_recurringandrentalpayments_subscription_type'],
								'indies_recurringandrentalpayments_subscription_start' =>$params['indies_recurringandrentalpayments_subscription_start'],
								);	
								
						$request = new Varien_Object();
						$request->setData($opt);

						$pd = Mage::getModel('catalog/product')->load($key);	
						$cart->addProduct($pd, $request);	
					}
					else
					{  
						$p_qty++ ; 
					}
					if (count($data) == $p_qty)
					{
						Mage::getSingleton('checkout/session')->addNotice('Please specify the products option(s)');
						$response = Mage::app()->getFrontController()->getResponse();
						$response->setRedirect($product->getProductUrl());
						$response->sendResponse();
						exit;
					}	
				}
			}
			else
			{
            	$cart->addProduct($product, $params);
			}
				
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();
            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }
}
?>