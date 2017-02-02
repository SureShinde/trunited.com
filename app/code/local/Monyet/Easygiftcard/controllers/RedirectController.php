<?php

class Monyet_Easygiftcard_RedirectController extends Mage_Core_Controller_Front_Action
{
    /** @var  Monyet_Easygiftcard_Helper_Data */
    protected $helper;

    /**
     * Protected construct method
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->helper = Mage::helper('monyet_easygiftcard');
    }

    /**
     * Make sure the customer is authenticated of necessary
     *
     * @return Mage_Core_Controller_Front_Action | void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $authenticationRequired = (bool) Mage::getStoreConfig(
            Monyet_Easygiftcard_Model_Product_Type::XML_PATH_AUTHENTICATION
        );

        if ($authenticationRequired) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer && $customer->getId()) {
                $validationResult = $customer->validate();
                if ((true !== $validationResult) && is_array($validationResult)) {
                    foreach ($validationResult as $error) {
                        Mage::getSingleton('core/session')->addError($error);
                    }
                    $this->goBack();
                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);

                    return $this;
                }
                return $this;
            } else {
                Mage::getSingleton('customer/session')->addError(
                    $this->helper->__('You must log in to access the video product')
                );
                $this->_redirect('customer/account/login');
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                return $this;
            }
        }
    }

    /**
     * Redirect to partner link
     *
     * @return void
     */
    public function productAction()
    {
        $productId  = (int) $this->getRequest()->getParam('id');

        $product = Mage::getModel('catalog/product')->load($productId);

        if (($product instanceof Mage_Catalog_Model_Product)
            && ($product->getTypeId() === Monyet_Easygiftcard_Model_Product_Type::TYPE_VIDEO)
        ) {
            if (!Zend_Uri::check($product->getVideoLink())) {
                Mage::getSingleton('core/session')->addError(
                    $this->helper->__('The product is not accessible.')
                );

                $this->goBack();
                return;
            }

            $this->getResponse()->setRedirect($product->getVideoLink());
            return;

        } else {
            Mage::getSingleton('core/session')->addError(
                $this->helper->__('Video product not found')
            );

            $this->goBack();
            return;
        }
    }

    /**
     * Performs a redirect to a previously visited page
     *
     * @return Monyet_Easygiftcard_RedirectController
     */
    protected function goBack()
    {
        $returnUrl = $this->_getRefererUrl();

        if ($returnUrl) {
            $this->getResponse()->setRedirect($returnUrl);
        } else {
            $this->_redirect('checkout/cart');
        }

        return $this;
    }
}
