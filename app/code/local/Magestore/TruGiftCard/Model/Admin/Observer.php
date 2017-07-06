<?php

class Magestore_TruGiftCard_Model_Admin_Observer
{

    /**
     * @param $observer
     * @return $this
     * @throws Exception
     */
    public function customerSaveAfter($observer)
    {
        $customer = $observer['customer'];
        $params = Mage::app()->getRequest()->getParam('truGiftCard');

        if(!isset($params) || $params['credit'] == '')
            return $this;

        if(filter_var($params['credit'], FILTER_VALIDATE_FLOAT) === false)
            throw new Exception(
                Mage::helper('trugiftcard')->__('Data invalid')
            );

        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        try {
            $connection->beginTransaction();

            $truGiftCardAccount = Mage::helper('trugiftcard/account')->updateCredit($customer->getId(), $params['credit']);
            if($truGiftCardAccount != null)
            {
                Mage::helper('trugiftcard/transaction')->createTransaction(
                    $truGiftCardAccount,
                    $params,
                    Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_BY_ADMIN,  // type
                    Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED // status
                );
            }

            $connection->commit();

        } catch (Exception $e) {
            $connection->rollback();
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('trugiftcard')->__($e->getMessage())
            );
        }

    }

    public function salesOrderGridCollectionLoadBefore($observer)
    {
        $collection = $observer->getOrderGridCollection();
        $select = $collection->getSelect();
        $select->joinLeft(array(
            'order'=>$collection->getTable('sales/order')),
            'order.entity_id=main_table.entity_id',
            array('created_by'=>'created_by')
        );
    }
}
