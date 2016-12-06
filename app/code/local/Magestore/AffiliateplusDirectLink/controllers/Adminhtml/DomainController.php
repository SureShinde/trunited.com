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
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusdirectlink Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Adminhtml_DomainController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_AffiliateplusDirectLink_Adminhtml_AffiliateplusdirectlinkController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('affiliateplus/domain')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('Domains Manager'), Mage::helper('adminhtml')->__('Domain Manager')
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
        $domainId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('affiliateplusdirectlink/domain')->load($domainId);

        if ($model->getId() || $domainId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('domain_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('affiliateplus/domain');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Domain Manager'), Mage::helper('adminhtml')->__('Domain Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Domain News'), Mage::helper('adminhtml')->__('Domain News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('affiliateplusdirectlink/adminhtml_domain_edit'))
                    ->_addLeft($this->getLayout()->createBlock('affiliateplusdirectlink/adminhtml_domain_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('affiliateplusdirectlink')->__('Domain does not exist')
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
            $data['domain'] = Mage::helper('affiliateplusdirectlink')->refineDomain($data['domain']);

            $id = $this->getRequest()->getParam('id');
            if (Mage::getModel('affiliateplusdirectlink/domain')->isDomainExited($id)) {
                Mage::getSingleton('adminhtml/session')->addError('This domain has already existed!');
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }

            $model = Mage::getModel('affiliateplusdirectlink/domain');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                if ($model->getStatus() == 2) {
                    $collection = $model->getDomainOtherAccountByName($model->getDomain(), $model->getAccountId());
                    //gưi mail thông báo
                    $model->sendMailDomainDisable($model->getDomain(), $model->getAccountId());
                    foreach ($collection as $value) {
                        $value->setStatus(1)->save();
                        Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($value->getId(), 1);
                    }
                    Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($model->getId(), 2);
                } else {
                    Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($model->getId(), $model->getStatus());
                }
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('affiliateplusdirectlink')->__('Domain was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
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
                Mage::helper('affiliateplusdirectlink')->__('Unable to find domain to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('affiliateplusdirectlink/domain');
                $model->setId($this->getRequest()->getParam('id'));
                Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($model->getId(), 3);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Domain was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $domainIds = $this->getRequest()->getParam('domain');
        if (!is_array($domainIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select domain(s)'));
        } else {
            try {
                foreach ($domainIds as $domainId) {
                    $domain = Mage::getModel('affiliateplusdirectlink/domain')->load($domainId);
                    Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($domain->getId(), 3);
                    $domain->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($domainIds))
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
        $domainIds = $this->getRequest()->getParam('domain');
        if (!is_array($domainIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select domain(s)'));
        } else {
            try {
                foreach ($domainIds as $domainId) {
                    $model = Mage::getSingleton('affiliateplusdirectlink/domain');
                    $model->load($domainId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                    if ($model->getStatus() == 2) {
                        $collection = $model->getDomainOtherAccountByName($model->getDomain(), $model->getAccountId());
                        //gưi mail thông báo
                        $model->sendMailDomainDisable($model->getDomain(), $model->getAccountId());
                        foreach ($collection as $value) {
                            $value->setStatus(1)->save();
                            Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($value->getId(), 1);
                        }

                        Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($model->getId(), 2);
                    } else {
                        Mage::getModel('affiliateplusdirectlink/directlink')->DomainChangeStatus($model->getId(), $model->getStatus());
                    }
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($domainIds))
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
        $fileName = 'affiliateplusdirectlinkdomain.csv';
        $content = $this->getLayout()
                ->createBlock('affiliateplusdirectlink/adminhtml_domain_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'affiliateplusdirectlinkdomain.xml';
        $content = $this->getLayout()
                ->createBlock('affiliateplusdirectlink/adminhtml_domain_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('affiliateplus/directlink/domains');
    }

    public function directlinkAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
