<?php

class Magestore_TruWallet_Model_Admin_Observer
{

    /**
     * @param $observer
     * @return $this
     * @throws Exception
     */
    public function customerSaveAfter($observer)
    {
        $customer = $observer['customer'];
        $params = Mage::app()->getRequest()->getParam('truWallet');

        if(!isset($params) || $params['credit'] == '')
            return $this;

        if(filter_var($params['credit'], FILTER_VALIDATE_FLOAT) === false)
            throw new Exception(
                Mage::helper('truwallet')->__('Data invalid')
            );

        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        try {
            $connection->beginTransaction();

            $truWalletAccount = Mage::helper('truwallet/account')->updateCredit($customer->getId(), $params['credit']);
            if($truWalletAccount != null)
            {
                Mage::helper('truwallet/transaction')->createTransaction(
                    $truWalletAccount,
                    $params,
                    Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_BY_ADMIN,  // type
                    Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED // status
                );
            }

            $connection->commit();

        } catch (Exception $e) {
            $connection->rollback();
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('truwallet')->__($e->getMessage())
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
