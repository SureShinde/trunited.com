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
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Adminhtml_Reward_TransactionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_RewardPoints_Adminhtml_TransactionController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('rewardpoints/transaction')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Transactions Manager'),
                Mage::helper('adminhtml')->__('Transaction Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_title($this->__('Reward Points'))
            ->_title($this->__('Transaction Manager'));
        $this->_initAction()
            ->renderLayout();
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $transactionId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('rewardpoints/transaction')->load($transactionId);

        if ($model->getId() || $transactionId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('transaction_data', $model);
            
            $this->loadLayout();
            
            $this->_setActiveMenu('rewardpoints/transaction');
            
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Transactions Manager'),
                Mage::helper('adminhtml')->__('Transaction Manager')
            );
            
            $this->_title($this->__('Reward Points'))
                ->_title($this->__('Transaction Manager'));
            if ($model->getId()) {
                $this->_title($this->__('Transaction #%s', $model->getId()));
            } else {
                $this->_title($this->__('New Transaction'));
            }
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('rewardpoints/adminhtml_transaction_edit'))
                ->_addLeft($this->getLayout()->createBlock('rewardpoints/adminhtml_transaction_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('rewardpoints')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function customerAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }
    
    public function customerGridAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }
 
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $request = $this->getRequest();

                $id = $request->getParam('id');
                if($id == null)
                {
                    $customer = Mage::getModel('customer/customer')->load($request->getPost('customer_id'));
                    if (!$customer->getId()) {
                        throw new Exception($this->__('Not found customer to create transaction.'));
                    }

                    $transaction = Mage::helper('rewardpoints/action')->addTransaction(
                        'admin',
                        $customer,
                        new Varien_Object(array(
                            'point_amount'  => $request->getPost('point_amount'),
                            'title'         => $request->getPost('title'),
                            'expiration_day'=> (int)$request->getPost('expiration_day'),
                        ))
                    );
                    if (!$transaction->getId()) {
                        throw new Exception(
                            $this->__('Cannot create transaction, please recheck form information.')
                        );
                    }
                } else {
                    $transaction = Mage::getModel('rewardpoints/transaction')->load($id);
                    $point_amount = $request->getParam('point_amount');
                    if (!$transaction->getId()) {
                        throw new Exception(
                            $this->__('Cannot create transaction, please recheck form information.')
                        );
                    }

                    if(!filter_var($point_amount, FILTER_VALIDATE_INT))
                    {
                        throw new Exception(
                            $this->__('The Points is not a number')
                        );
                    }

                    if($point_amount < 0)
                    {
                        throw new Exception(
                            $this->__('The Points is greater than zero')
                        );
                    }

                    $old_point = $transaction->getData('point_amount');
                    $transaction->setData('title', $request->getParam('title'));
                    $status_arr = array(
                        Magestore_RewardPoints_Model_Transaction::STATUS_ON_HOLD,
                        Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED,
                    );
                    if(in_array($transaction->getStatus(), $status_arr))
                    {
                        $transaction->setData('point_amount', $point_amount);
                        $transaction->setData('hold_point', $point_amount);
                    }

                    $transaction->save();

                    /* Update balance */
                    if($transaction->getStatus() == Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED)
                    {
                        $this->updateBalance($transaction, $old_point);
                    }
                    /* END Update balance */
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Transaction has been created successfully.')
                );
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($request->getPost());
                $this->_redirect('*/*/edit', array('id' => $request->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('rewardpoints')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    public function updateBalance($transaction, $old_point)
    {
        $rewardAccount = Mage::getModel('rewardpoints/customer')->load($transaction->getRewardId());

        $maxBalance = (int) Mage::getStoreConfig(
            Magestore_RewardPoints_Model_Transaction::XML_PATH_MAX_BALANCE,
            $transaction->getStoreId()
        );

        if ($maxBalance > 0 && $transaction->getRealPoint() > 0 &&
            $rewardAccount->getPointBalance() + $transaction->getRealPoint() > $maxBalance)
        {
            if ($maxBalance > $rewardAccount->getPointBalance()) {
                $transaction->setPointAmount(abs($maxBalance - ($rewardAccount->getPointBalance() + $transaction->getPointAmount())));
                $transaction->setRealPoint($maxBalance - $rewardAccount->getPointBalance());
                $rewardAccount->setPointBalance($maxBalance);
                $transaction->sendUpdateBalanceEmail($rewardAccount);
            } else {
                throw new Exception(
                    Mage::helper('rewardpoints')->__('Maximum points allowed in account balance is %s.', $maxBalance)
                );
            }
        } else {
            $new_balance = $rewardAccount->getPointBalance() + ($transaction->getPointAmount() - $old_point);
            $rewardAccount->setPointBalance($new_balance > 0 ? $new_balance : 0);
            $transaction->sendUpdateBalanceEmail($rewardAccount);
        }

        // Save reward account and transaction to database
        $rewardAccount->save();
    }
    
    /**
     * complete reward points transaction
     */
    public function completeAction()
    {
        $transactionId  = $this->getRequest()->getParam('id');
        $transaction    = Mage::getModel('rewardpoints/transaction')->load($transactionId);
        try {
            $transaction->completeTransaction();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('Transaction has been completed successfully.')
            );
            $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
            return ;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * cancel reward points transaction
     */
    public function cancelAction()
    {
        $transactionId  = $this->getRequest()->getParam('id');
        $transaction    = Mage::getModel('rewardpoints/transaction')->load($transactionId);
        try {
            /*
            * xuanbinh
            */
           if($transaction->getAction() == 'receivepoint'){
               $tranferId = $transaction->getExtraContent();
               $arrExtra = explode("=",$tranferId);
               $transfer = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($arrExtra[1]);
               $reason = Mage::helper('rewardpointstransfer')->__('Transfer was canceled by admin.');
               Mage::helper('rewardpointstransfer')->cancelTransfer($transfer,$reason);
           }else{
               $transaction->cancelTransaction();
           }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('Transaction has been canceled successfully.')
            );
            $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
            return ;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * expire reward points transaction
     */
    public function expireAction()
    {
        $transactionId  = $this->getRequest()->getParam('id');
        $transaction    = Mage::getModel('rewardpoints/transaction')->load($transactionId);
        try {
            $transaction->expireTransaction();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('Transaction has been expired successfully.')
            );
            $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
            return ;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * mass complete transaction(s) action
     */
    public function massCompleteAction()
    {
        $tranIds = $this->getRequest()->getParam('transactions');
        if (!is_array($tranIds) || !count($tranIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $collection = Mage::getResourceModel('rewardpoints/transaction_collection')
                ->addFieldToFilter('point_amount', array('gt' => 0))
                ->addFieldToFilter('status', array(
                    'lt' => Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED
                ))
                ->addFieldToFilter('transaction_id', array('in' => $tranIds));
            $total = 0;
            foreach ($collection as $transaction) {
                try {
                    $transaction->completeTransaction();
                    $total++;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            if ($total > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d transaction(s) were successfully completed', $total)
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No transaction was completed')
                );
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass cancel transaction(s) action
     */
    public function massCancelAction()
    {
        $tranIds = $this->getRequest()->getParam('transactions');
        if (!is_array($tranIds) || !count($tranIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $collection = Mage::getResourceModel('rewardpoints/transaction_collection')
                ->addFieldToFilter('point_amount', array('gt' => 0))
                ->addFieldToFilter('status', array(
                    'lteq' => Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED
                ))
                ->addFieldToFilter('transaction_id', array('in' => $tranIds));
            $total = 0;
            foreach ($collection as $transaction) {
                try {
                    /*
                     * xuanbinh
                     */
                    if($transaction->getAction() == 'receivepoint'){
                        $tranferId = $transaction->getExtraContent();
                        $arrExtra = explode("=",$tranferId);
                        $transfer = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($arrExtra[1]);
                        $reason = Mage::helper('rewardpointstransfer')->__('Transfer was canceled by admin.');
                        Mage::helper('rewardpointstransfer')->cancelTransfer($transfer,$reason);
                    }else{
                        $transaction->cancelTransaction();
                    }
                    /**
                     * end
                     */
                    $total++;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            if ($total > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d transaction(s) were successfully canceled', $total)
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No transaction was canceled')
                );
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass expire selected transaction(s)
     */
    public function massExpireAction()
    {
        $tranIds = $this->getRequest()->getParam('transactions');
        if (!is_array($tranIds) || !count($tranIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $collection = Mage::getResourceModel('rewardpoints/transaction_collection')
                ->addAvailableBalanceFilter()
                ->addFieldToFilter('status', array(
                    'lteq' => Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED
                ))
                ->addFieldToFilter('expiration_date', array('notnull' => true))
                ->addFieldToFilter('expiration_date', array('to' => now()))
                ->addFieldToFilter('transaction_id', array('in' => $tranIds));
            
            $total = 0;
            foreach ($collection as $transaction) {
                try {
                    $transaction->expireTransaction();
                    $total++;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            if ($total > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d transaction(s) were successfully expired', $total)
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('No transaction was expired')
                );
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'rewardpoints_transaction.csv';
        $content    = $this->getLayout()
                           ->createBlock('rewardpoints/adminhtml_transaction_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'rewardpoints_transaction.xml';
        $content    = $this->getLayout()
                           ->createBlock('rewardpoints/adminhtml_transaction_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('rewardpoints');
    }

    public function importAction() {
        $this->loadLayout();
        $this->_setActiveMenu('rewardpoints/transaction');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Transactions'), Mage::helper('adminhtml')->__('Import Transactions'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Transactions'), Mage::helper('adminhtml')->__('Import Transactions'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $editBlock = $this->getLayout()->createBlock('rewardpoints/adminhtml_transaction_import');
        $editBlock->removeButton('delete');
        $editBlock->removeButton('saveandcontinue');
        $editBlock->removeButton('reset');
        $editBlock->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/') . '\')');
        $editBlock->setData('form_action_url', $this->getUrl('*/*/importSave', array()));

        $this->_addContent($editBlock)
            ->_addLeft($this->getLayout()->createBlock('rewardpoints/adminhtml_transaction_import_tabs'));

        $this->renderLayout();
    }

    public function importSaveAction() {

        if (!empty($_FILES['csv_store']['tmp_name'])) {
            try {
                $number = Mage::helper('rewardpoints/transaction')->import();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rewardpoints')->__('You\'ve successfully imported ') . $number . Mage::helper('rewardpoints')->__(' new transaction(s)'));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardpoints')->__('Invalid file upload attempt'));
            }
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardpoints')->__('Invalid file upload attempt'));
            $this->_redirect('*/*/importstore');
        }

    }
}
