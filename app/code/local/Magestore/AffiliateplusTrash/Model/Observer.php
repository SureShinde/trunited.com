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
 * @package     Magestore_AffiliateplusTrash
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusTrash Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusTrash
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusTrash_Model_Observer
{
    /**
     * add massaction for transaction grid
     * @param type $observer
     */
    public function adminhtmlAddMassactionTransactionGrid($observer)
    {
        $grid = $observer['grid'];
        $grid->getMassactionBlock()->addItem('delete', array(
            'label'     => Mage::helper('affiliateplustrash')->__('Move to Trash'),
            'url'       => $grid->getUrl('adminhtml/affiliateplustrash_transaction/massDelete'),            //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
            'confirm'   => Mage::helper('affiliateplustrash')->__('Are you sure?'),
        ));
    }
    
    /**
     * update transaction edit form action button
     * 
     * @param type $observer
     */
    public function adminhtmlUpdateTransactionAction($observer)
    {
        $transaction = $observer['transaction'];
        $form = $observer['form'];
        
        if ($transaction->canRestore()) {
            $form->addButton('restore', array(
                'label'     => Mage::helper('affiliateplustrash')->__('Restore'),
                'onclick'   => 'deleteConfirm(\''
                            . Mage::helper('affiliateplustrash')->__('Restore deleted transaction. Are you sure?')
                            . '\', \''
                            . $form->getUrl('adminhtml/affiliateplustrash_transaction/restore', array('id' => $transaction->getId()))           //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
                            . '\')',
                'class'     => 'save'
            ), 0);
            // update back button to deleted transaction
            $form->updateButton('back', 'onclick', 'setLocation(\'' . $form->getUrl('adminhtml/affiliateplustrash_transaction/deleted') . '\')');              //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
        } else if ($transaction->getData('transaction_is_can_delete')) {
            $form->addButton('restore', array(
                'label'     => Mage::helper('adminhtml')->__('Move to Trash'),
                'onclick'   => 'deleteConfirm(\''
                            . Mage::helper('affiliateplustrash')->__('Are you sure?')
                            . '\', \''
                            . $form->getUrl('adminhtml/affiliateplustrash_transaction/delete', array('id' => $transaction->getId()))                               //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
                            . '\')',
                'class'     => 'delete'
            ), 0);
        }
    }
    
    /**
     * update mass action for Payment
     * 
     * @param type $observer
     */
    public function adminhtmlPaymentMassaction($observer)
    {
        $grid = $observer['grid'];
        $grid->setMassactionIdField('payment_id');
        $grid->getMassactionBlock()->setFormFieldName('payment');
        $grid->getMassactionBlock()->addItem('delete', array(
            'label'     => Mage::helper('affiliateplustrash')->__('Move to Trash'),
            'url'       => $grid->getUrl('adminhtml/affiliateplustrash_payment/massDelete'),                    //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
            'confirm'   => Mage::helper('affiliateplustrash')->__('Are you sure?'),
        ));
    }
    
    /**
     * update payment form action
     * 
     * @param type $observer
     */
    public function adminhtmlPaymentEditFormAction($observer)
    {
        $form = $observer['form'];
        $data = $observer['data'];
        if ($data->canRestore()) {
            $form->removeButton('cancel');
            $form->removeButton('complete');
            $form->removeButton('save_and_pay_manual');
            $data->setData(array());
        } else {
            $form->addButton('restore', array(
                'label'     => Mage::helper('adminhtml')->__('Move to Trash'),
                'onclick'   => 'deleteConfirm(\''
                            . Mage::helper('affiliateplustrash')->__('Are you sure?')
                            . '\', \''
                            . $form->getUrl('adminhtml/affiliateplustrash_payment/delete', array('id' => $data->getId()))                   //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
                            . '\')',
                'class'     => 'delete'
            ), 0);
        }
    }
}
