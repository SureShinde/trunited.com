<?php

class Magestore_Interest_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	public function saveInterestAction()
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectUrl(Mage::getUrl('customer/account/login'));
            return;
        }

        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $interest_param = $this->getRequest()->getParam('interest');

        $count = 0;

        if ($interest_param != null && is_array($interest_param))
        {
            $delete_items = Mage::getModel('interest/customer')
                ->getCollection()
                ->addFieldToFilter('customer_id', $customer_id)
                ;

            if(sizeof($delete_items) > 0)
            {
                foreach ($delete_items as $delete_item) {
                    $delete_item->delete();
                }
            }

            $transactionSave = Mage::getModel('core/resource_transaction');

            try {
                foreach ($interest_param as $param) {
                    $obj = Mage::getModel('interest/customer');
                    $obj->setData('interest_id', $param);
                    $obj->setData('customer_id', $customer_id);
                    $obj->setData('created_at', now());
                    $obj->setData('updated_at', now());

                    $transactionSave->addObject($obj);
                    $count++;
                }

                $transactionSave->save();
            } catch (Exception $e) {
                $count = 0;
                Mage::getSingleton('core/session')->addError(
                    $e->getMessage()
                );
            }
        } else {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('interest')->__('No Interests to save')
            );
        }

        if($count > 0)
            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('interest')->__('You have just updated the Interests and Leisure successfully')
            );

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function clearAction()
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectUrl(Mage::getUrl('customer/account/login'));
            return;
        }

        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();

        $delete_items = Mage::getModel('interest/customer')
            ->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
        ;

        if(sizeof($delete_items) > 0)
        {
            foreach ($delete_items as $delete_item) {
                try {
                    $delete_item->delete();
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError(
                        $e->getMessage()
                    );
                }
            }
        }

        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('interest')->__('You have just cleared all the Interests and Leisure successfully')
        );

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }
}
