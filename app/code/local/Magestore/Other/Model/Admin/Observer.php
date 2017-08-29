<?php

class Magestore_Other_Model_Admin_Observer
{

    public function prepareCustomerSave($observer)
    {
        $customer = $observer->getCustomer();
        $before_customer = Mage::getModel('customer/customer')->load($customer->getId());
        $changed_active = $customer->getData('is_active');
        $old_active = $before_customer->getData('is_active');
        $is_error = false;
        if($changed_active != $old_active) {
            if($changed_active == Magestore_Other_Model_Status::STATUS_CUSTOMER_INACTIVE)
            {
                $new_email = Mage::helper('other')->getPrefixCustomer().''.$customer->getEmail();
                if($this->checkExistCustomerEmail($new_email)){
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('other')->__('Please update the email again before changing to Inactive status. Because <b>%s</b> is existed the customer', $new_email)
                    );
                    $customer->setData($before_customer->getData());
                    $is_error = true;
                } else
                    $customer->setEmail($new_email);
            } else if($changed_active == Magestore_Other_Model_Status::STATUS_CUSTOMER_ACTIVE) {
                $new_email = str_replace(Mage::helper('other')->getPrefixCustomer(),'',$customer->getEmail());
                if($this->checkExistCustomerEmail($new_email)){
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('other')->__('Please update the email again before changing to Active status. Because <b>%s</b> is existed the customer', $new_email)
                    );
                    $customer->setData($before_customer->getData());
                    $is_error = true;
                } else
                    $customer->setEmail($new_email);
            }
        }

        if($is_error)
            throw new Exception();

        return;
    }

    public function checkExistCustomerEmail($email)
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addFieldToFilter('email', $email)
            ->getFirstItem();

        if($collection != null && $collection->getId())
            return true;
        else
            return false;
    }
}
