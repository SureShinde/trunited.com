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
class Magestore_AffiliateplusDirectLink_Adminhtml_AffiliateplusdirectlinkController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_AffiliateplusDirectLink_Adminhtml_AffiliateplusdirectlinkController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('affiliateplus/directlink')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('Direct link Manager'), Mage::helper('adminhtml')->__('Direct link Manager')
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
        $affiliateplusdirectlinkId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('affiliateplusdirectlink/directlink')->load($affiliateplusdirectlinkId);

        if ($model->getId() || $affiliateplusdirectlinkId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('affiliateplusdirectlink_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('affiliateplus/directlink');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Direct link Manager'), Mage::helper('adminhtml')->__('Direct link Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Direct link News'), Mage::helper('adminhtml')->__('Direct link News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('affiliateplusdirectlink/adminhtml_affiliateplusdirectlink_edit'))
                    ->_addLeft($this->getLayout()->createBlock('affiliateplusdirectlink/adminhtml_affiliateplusdirectlink_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('affiliateplusdirectlink')->__('Direct link does not exist')
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
            if (!$data['link']) {
                $link = Mage::getModel('affiliateplus/banner')->load($data['banner_id'])->getLink();
                if (!$link)
                    $link = Mage::getUrl();
                $data['link'] = $link;
            }
            $id = $this->getRequest()->getParam('id');
            if (Mage::getModel('affiliateplusdirectlink/directlink')->isDirectLinkExited($id)) {
                Mage::getSingleton('adminhtml/session')->addError('Direct link to this page has already existed!');
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
            $model = Mage::getModel('affiliateplusdirectlink/directlink');
            $domain = $this->addDomain();
            $data['source_page'] = Mage::helper('affiliateplusdirectlink')->refineSourcePage($data['source_page']);
            $data['domain_id'] = $domain->getId();
            //if (!$this->getRequest()->getParam('id'))
            $data['status'] = $domain->getStatus();
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('affiliateplusdirectlink')->__('Direct link was successfully saved')
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
                Mage::helper('affiliateplusdirectlink')->__('Unable to find Direct link to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('affiliateplusdirectlink/directlink');
                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Direct link was successfully deleted')
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
        $affiliateplusdirectlinkIds = $this->getRequest()->getParam('affiliateplusdirectlink');
        if (!is_array($affiliateplusdirectlinkIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Direct link(s)'));
        } else {
            try {
                foreach ($affiliateplusdirectlinkIds as $affiliateplusdirectlinkId) {
                    $affiliateplusdirectlink = Mage::getModel('affiliateplusdirectlink/directlink')->load($affiliateplusdirectlinkId);
                    $affiliateplusdirectlink->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($affiliateplusdirectlinkIds))
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
        $affiliateplusdirectlinkIds = $this->getRequest()->getParam('affiliateplusdirectlink');
        if (!is_array($affiliateplusdirectlinkIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Direct link(s)'));
        } else {
            try {
                foreach ($affiliateplusdirectlinkIds as $affiliateplusdirectlinkId) {
                    Mage::getSingleton('affiliateplusdirectlink/directlink')
                            ->load($affiliateplusdirectlinkId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($affiliateplusdirectlinkIds))
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
        $fileName = 'affiliateplusdirectlink.csv';
        $content = $this->getLayout()
                ->createBlock('affiliateplusdirectlink/adminhtml_affiliateplusdirectlink_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'affiliateplusdirectlink.xml';
        $content = $this->getLayout()
                ->createBlock('affiliateplusdirectlink/adminhtml_affiliateplusdirectlink_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('affiliateplus/directlink/directlinks');
    }

    public function getDomainByAccountIdAction() {
        $AccountId = $this->getRequest()->getParam('accountId');
        $html = '';
        $domaincollection = Mage::getModel('affiliateplusdirectlink/domain')->getCollection()
                ->addFieldToFilter('account_id', $AccountId)
                ->addFieldToFilter('status', 2);
        foreach ($domaincollection as $value) {
            $html .='<option value="' . $value->getId() . '">' . $value->getDomain() . '</option>';
        }
        $this->getResponse()->setBody(json_encode($html));
    }

    public function addDomain() {
        $domaininput = $this->getRequest()->getParam('source_page');
        $domain = Mage::helper('affiliateplusdirectlink')->refineDomain($domaininput);
        $account = $this->getRequest()->getParam('account_id');
        $model = Mage::getModel('affiliateplusdirectlink/domain')->saveDomainAccount($domain, $account);
        return $model;
    }

}
