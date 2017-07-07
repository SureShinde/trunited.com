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
 * @package     Magestore_RewardPointsCoupon
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointscoupon Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Adminhtml_RewardpointscouponController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_RewardPointsCoupon_Adminhtml_RewardpointscouponController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('rewardpointscoupon/rewardpointscoupon')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction() {
        $rewardpointscouponId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('rewardpointscoupon/template')->load($rewardpointscouponId);

        if ($model->getId() || $rewardpointscouponId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('current_rewardpoints_coupon_template', $model);

            $this->loadLayout();
            $this->_setActiveMenu('rewardpointscoupon/rewardpointscoupon');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
//            $this->_addContent($this->getLayout()->createBlock('rewardpointscoupon/adminhtml_rewardpointscoupon_edit'))
//                    ->_addLeft($this->getLayout()->createBlock('rewardpointscoupon/adminhtml_rewardpointscoupon_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('rewardpointscoupon')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('rewardpointscoupon/template');
//            filter date
            $data = $this->_filterDates($data, array('from_date', 'to_date'));
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $useAutoGeneration = (int) !empty($data['use_auto_generation']);
                $model->setUseAutoGeneration($useAutoGeneration);
                $model->save();

                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                if ($this->getRequest()->getParam('print')) {
                    $template_id = $this->getRequest()->getParam('id');
                    $collection = Mage::getModel('rewardpointscoupon/coupon')->getCollection()
                            ->addFieldToFilter('template_id', $template_id);
                    if (!$useAutoGeneration)
                        $collection->addFieldToFilter('is_primary', 1);
                    $coupon_ids = array();
                    foreach ($collection as $coupon) {
                        $coupon_ids[] = $coupon->getId();
                    }
                    if (count($coupon_ids)) {
                        $pdf = Mage::getModel('rewardpointscoupon/pdf_couponcode')->getPdf($coupon_ids);
                        $this->_prepareDownloadResponse('couponcode_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf');
                    }
                    return;
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('rewardpointscoupon')->__('Item was successfully saved')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('rewardpointscoupon')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('rewardpointscoupon/template');
                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _initTemp() {
        $this->_title($this->__('Template'))->_title($this->__('RewardPoints Coupon'));

        Mage::register('current_rewardpoints_coupon_template', Mage::getModel('rewardpointscoupon/template'));
        $id = (int) $this->getRequest()->getParam('template_id');
        if ($id) {
            Mage::registry('current_rewardpoints_coupon_template')->load($id);
        }
    }

    public function generateAction() {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noRoute');
            return;
        }
        $result = array();
        $this->_initTemp();

        $temp = Mage::registry('current_rewardpoints_coupon_template');
        if (!$temp->getId()) {
            $result['error'] = Mage::helper('rewardpointscoupon')->__('Template is not defined');
        } else {
            try {
                $data = $this->getRequest()->getParams();
                if (!empty($data['to_date'])) {
                    $data = array_merge($data, $this->_filterDates($data, array('to_date')));
                }

                /** @var $generator Mage_SalesRule_Model_Coupon_Massgenerator */
                $generator = $temp->getCouponMassGenerator();
                if (!$generator->validateData($data)) {
                    $result['error'] = Mage::helper('rewardpointscoupon')->__('Not valid data provided');
                } else {
                    $generator->setData($data);
                    $generator->generatePool();
                    $generated = $generator->getGeneratedCount();
                    $this->_getSession()->addSuccess(Mage::helper('rewardpointscoupon')->__('%s Coupon(s) have been generated', $generated));
                    $this->_initLayoutMessages('adminhtml/session');
                    $result['messages'] = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
                }
            } catch (Mage_Core_Exception $e) {
                $result['error'] = $e->getMessage();
            } catch (Exception $e) {
                $result['error'] = Mage::helper('rewardpointscoupon')->__('An error occurred while generating coupons. Please review the log and try again.');
                Mage::logException($e);
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function couponsGridAction() {
        $this->_initTemp();
        $this->loadLayout()->renderLayout();
    }

    /**
     * Coupons mass delete action
     */
    public function couponsMassDeleteAction() {
        $this->_initTemp();
        $template_id = $this->getRequest()->getParam('id');

        if (!$template_id) {
            $this->_forward('noRoute');
        }

        $codesIds = $this->getRequest()->getParam('ids');

        if (is_array($codesIds)) {

            $couponsCollection = Mage::getResourceModel('rewardpointscoupon/coupon_collection')
                    ->addFieldToFilter('coupon_id', array('in' => $codesIds));

            foreach ($couponsCollection as $coupon) {
                $coupon->delete();
            }
        }
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $rewardpointscouponIds = $this->getRequest()->getParam('rewardpointscoupon');
        if (!is_array($rewardpointscouponIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($rewardpointscouponIds as $rewardpointscouponId) {
                    $rewardpointscoupon = Mage::getModel('rewardpointscoupon/template')->load($rewardpointscouponId);
                    $rewardpointscoupon->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($rewardpointscouponIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction() {
        $rewardpointscouponIds = $this->getRequest()->getParam('rewardpointscoupon');
        if (!is_array($rewardpointscouponIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($rewardpointscouponIds as $rewardpointscouponId) {
                    Mage::getSingleton('rewardpointscoupon/template')
                            ->load($rewardpointscouponId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($rewardpointscouponIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'rewardpointscoupon.csv';
        $content = $this->getLayout()
                ->createBlock('rewardpointscoupon/adminhtml_rewardpointscoupon_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportCouponsXmlAction() {
        $template_id = $this->getRequest()->getParam('id');

        if ($template_id) {
            $fileName = 'coupon_codes.xml';
            $content = $this->getLayout()
                    ->createBlock('rewardpointscoupon/adminhtml_rewardpointscoupon_edit_tab_coupons_grid')
                    ->getExcelFile($fileName);
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/*', array('_current' => true));
            return;
        }
    }

    /**
     * Export coupon codes as CSV file
     *
     * @return void
     */
    public function exportCouponsCsvAction() {
        $template_id = $this->getRequest()->getParam('id');
        if ($template_id) {
            $fileName = 'coupon_codes.csv';
            $content = $this->getLayout()
                    ->createBlock('rewardpointscoupon/adminhtml_rewardpointscoupon_edit_tab_coupons_grid')
                    ->getCsvFile();
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/*', array('_current' => true));
            return;
        }
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'rewardpointscoupon.xml';
        $content = $this->getLayout()
                ->createBlock('rewardpointscoupon/adminhtml_rewardpointscoupon_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('rewardpoints/rewardpointscoupon');
    }

    public function massCouponPrintAction() {
        $ids = $this->getRequest()->getParam('ids');
        if ($ids && is_string($ids))
            $ids = explode(',', $ids);
        if (!is_array($ids) || !count($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Coupon(s)'));
            $this->_redirect('*/*/*');
        } else {
            $pdf = Mage::getModel('rewardpointscoupon/pdf_couponcode')->getPdf($ids);
            $this->_prepareDownloadResponse('couponcode_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf');
        }
    }

    public function massPrintAction() {
        $ids = $this->getRequest()->getParam('rewardpointscoupon');
        if ($ids && is_string($ids))
            $ids = explode(',', $ids);
        if (!is_array($ids) || !count($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select template(s)'));
            $this->_redirect('*/*/*');
        } else {
            //Get all coupon ids
            $couponIds = array();
            foreach ($ids as $id) {
                $couponIds = array_merge($couponIds, Mage::getModel('rewardpointscoupon/coupon')->loadByTemplateId($id));
            }

            $pdf = Mage::getModel('rewardpointscoupon/pdf_couponcode')->getPdf($couponIds);
            $this->_prepareDownloadResponse('couponcode_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf');
        }
    }

}
