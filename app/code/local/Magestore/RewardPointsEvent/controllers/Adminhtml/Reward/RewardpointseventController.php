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
 * @package     Magestore_RewardPointsEvent
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsevent Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Adminhtml_Reward_RewardpointseventController extends Mage_Adminhtml_Controller_Action {

    /**
     * Dirty rules notice message
     *
     * @var string
     */
    protected $_dirtyRulesNoticeMessage;

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_RewardPointsEvent_Adminhtml_RewardpointseventController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('rewardpoints/earning')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('Event Manager'), Mage::helper('adminhtml')->__('Event Manager')
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
        $rewardpointseventId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('rewardpointsevent/rewardpointsevent')->load($rewardpointseventId);

        if ($model->getId() || $rewardpointseventId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            $model->getConditions()->setJsFormObject('event_conditions_fieldset');
            Mage::register('rewardpointsevent_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('rewardpointsevent/rewardpointsevent');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Event Manager'), Mage::helper('adminhtml')->__('Event Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );
            $this->getLayout()->getBlock('head')
                    ->setCanLoadExtJs(true)
                    ->setCanLoadRulesJs(true);
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true)
                    ->addItem('js', 'tiny_mce/tiny_mce.js')
                    ->addItem('js', 'mage/adminhtml/wysiwyg/tiny_mce/setup.js')
                    ->addJs('mage/adminhtml/browser.js')
                    ->addJs('prototype/window.js')
                    ->addJs('lib/flex.js')
                    ->addJs('mage/adminhtml/flexuploader.js');
            $this->_addContent($this->getLayout()->createBlock('rewardpointsevent/adminhtml_rewardpointsevent_edit'))
                    ->_addLeft($this->getLayout()->createBlock('rewardpointsevent/adminhtml_rewardpointsevent_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('rewardpointsevent')->__('Item does not exist')
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
            $id = $this->getRequest()->getParam('id');
            if ($data['customer_apply'] == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CSV) {
                if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                    try {
                        /* Starting upload */
                        $uploader = new Varien_File_Uploader('filename');

                        // Any extention would work
                        $uploader->setAllowedExtensions(array('csv'));
                        $uploader->setAllowRenameFiles(false);

                        // Set the file upload mode 
                        // false -> get the file directly in the specified folder
                        // true -> get the file in the product like folders 
                        //  
                        $uploader->setFilesDispersion(false);

                        // We set media as the upload dir
                        $path = Mage::getBaseDir('var') . DS . 'tmp' . DS;
                        $result = $uploader->save($path, $_FILES['filename']['name']);
                        $data['file_name'] = $result['file'];
                        $file_path = $path . $result['file'];
//                        $csvObject = new Varien_File_Csv();
//                        $dataFile = $csvObject->getData($file_path);
//                        $customerData = array();
//                        foreach ($dataFile as $row => $cols) {
//                            if ($row == 0) {
//                                $fields = $cols;
//                            } else {
//                                $customerData[] = array_combine($fields, $cols);
//                            }
//                        }
                    } catch (Exception $e) {
                        $data['file_name'] = $_FILES['filename']['name'];
                    }
                } else {
                    if (isset($data['file_name']) && $data['file_name'] != '') {
                        $dir_csv = Mage::getBaseDir('var') . DS . 'tmp' . DS . $data['file_name'];
                        if (file_exists($dir_csv)) {
                            $file_path = $dir_csv;
                        }
                    }
                }
                if (isset($file_path) && $file_path != '') {
                    $csvObject = new Varien_File_Csv();
                    $dataFile = $csvObject->getData($file_path);
                    $customerData = array();
                    foreach ($dataFile as $row => $cols) {
                        if ($row == 0) {
                            $fields = $cols;
                        } else {
                            $customerData[] = array_combine($fields, $cols);
                        }
                    }
                }
            }

            if (isset($data['rule'])) {
                $rules = $data['rule'];
                if (isset($rules['conditions'])) {
                    $data['conditions'] = $rules['conditions'];
                }
                unset($data['rule']);
            }
            $data = $this->_filterDates($data, array('apply_from', 'apply_to'));
            if (isset($data['apply_from']) && $data['apply_from'] == '')
                $data['apply_from'] = null;
            if (isset($data['apply_to']) && $data['apply_to'] == '')
                $data['apply_to'] = null;
            $model = Mage::getModel('rewardpointsevent/rewardpointsevent');

            $model->load($id);
            if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                $model->setCreatedTime(now())
                        ->setUpdateTime(now());
            } else {
                $model->setUpdateTime(now());
            }
            $model->loadPost($data);
            try {
                $model->save();
                Mage::getModel('rewardpointsevent/cron')->checkActiveEvent();
                if (isset($customerData) && count($customerData)) {
                    $this->_updateCustomer($model, $customerData);
                }
                $this->displayFlag($model);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('rewardpointsevent')->__('Event was successfully saved')
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
                Mage::helper('rewardpointsevent')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    public function displayFlag($model) {
        if ($model->getStatus() == Magestore_RewardPointsEvent_Model_Status::STATUS_ENABLED) {
            Mage::getModel('rewardpointsevent/flag')->loadSelf()
                    ->setState(0)
                    ->save();
        } else {
            Mage::getModel('rewardpointsevent/flag')->loadSelf()
                    ->setState(1)
                    ->save();
        }
        $dirtyRules = Mage::getModel('rewardpointsevent/flag')->loadSelf();
        if ($dirtyRules->getState()) {
            Mage::getSingleton('adminhtml/session')->addNotice($this->getDirtyRulesNoticeMessage());
        }
    }

    protected function _checkCustomer($email, $website_id = 1) {
        return Mage::getModel('customer/customer')->setWebsiteId($website_id)->loadByEmail($email);
    }

    protected function _updateCustomer($model, $customerData) {
        if ($model->getFileName() && count($customerData) && $model->getStatus() == Magestore_RewardPointsEvent_Model_Status::STATUS_ENABLED) {
            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
            $write->beginTransaction();
            $write->delete(
                    Mage::getModel('core/resource')->getTableName('rewardpoints_event_customer'), array(
                'event_id=?' => $model->getId(),
                    )
            );
            foreach ($customerData as $key => $value) {
                $email = $value['Email'];
                $customer = $this->_checkCustomer($email);
                if (!$customer->getId() && $model->getAllowCreate()) {
                    $this->createCustomer($value);
                }
//                recheck customer
                $customer = $this->_checkCustomer($email);
                if ($customer->getId()) {
                    $rows[] = array(
                        'customer_id' => $customer->getId(),
                        'email' => $value['Email'],
                        'name' => $value['Name'],
                        'event_id' => $model->getId(),
                        'store_id' => $customer->getStoreId(),
                    );
                    if (count($rows) == 1000) {
                        $write->insertMultiple(Mage::getModel('core/resource')->getTableName('rewardpoints_event_customer'), $rows);
                        $rows = array();
                    }
                }
            }
            if (!empty($rows)) {
                $write->insertMultiple(Mage::getModel('core/resource')->getTableName('rewardpoints_event_customer'), $rows);
            }
            try {
                $write->commit();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }

    public function createCustomer($data) {
        $website = Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray();
        $website_id = Mage::app()->getDefaultStoreView()->getWebsiteId();
        foreach ($website as $key => $value) {
            if ($value['label'] == $data['Website']) {
                $website_id = $value['value'];
                break;
            }
        }
        $store = Mage::app()->getStores();
        $store_id = Mage::app()->getDefaultStoreView()->getId();
        foreach ($store as $key => $value) {
            if ($data['Store'] == $value->getName()) {
                $store_id = $value->getId();
                break;
            }
        }
        $group = Mage::getResourceSingleton('customer/customer')->getAttribute('group_id')->getSource()->getAllOptions();
        $group_id = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, $store_id);
        foreach ($group as $key => $value) {
            if ($value == $data['Group']) {
                $group_id = $key;
            }
        }


        $customer = Mage::getModel('customer/customer');
        $isNewCustomer = $customer->isObjectNew();
        $customer->setEmail($data['Email']);
        $name = array();
        $name = explode(' ', $data['Name']);
        if (count($name)) {
            if (count($name) == 3) {
                $first_name = $name[0];
                $middle_name = $name[1];
                $last_name = $name[2];
            } else {
                $first_name = $name[0];
                $last_name = $name[1];
            }
            $customer->setFirstname($first_name);
            if ($middle_name != '') {
                $customer->setMiddlename($middle_name);
            }
            $customer->setLastname($last_name);
        }
        $customer->setStoreId($store_id);
        $customer->setWebsiteId($website_id);
        $customer->setGroupId($group_id);
        $sendPassToEmail = true;
        $customer->setPassword($customer->generatePassword());
        try {
            $customer->save();
            Mage::dispatchEvent('rewardpointsevent_create_customer_from_csv', array('account_controller' => $this, 'customer' => $customer));
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }

        if ($customer->getWebsiteId() && $sendPassToEmail) {
            $storeId = $customer->getSendemailStoreId();
            if ($isNewCustomer) {
                $customer->sendNewAccountEmail('registered', '', $storeId);
            } elseif ((!$customer->getConfirmation())) {
// Confirm not confirmed customer
                $customer->sendNewAccountEmail('confirmed', '', $storeId);
            }
        }
    }

    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('rewardpointsevent/rewardpointsevent');
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

    public function setDirtyRulesNoticeMessage($dirtyRulesNoticeMessage) {
        $this->_dirtyRulesNoticeMessage = $dirtyRulesNoticeMessage;
    }

    /**
     * Get dirty rules notice message
     *
     * @return string
     */
    public function getDirtyRulesNoticeMessage() {
        $defaultMessage = Mage::helper('rewardpointsevent')->__('You have not yet activated the event that you have edited. Please change its status to Enabled if you want to apply the event.');
        return $this->_dirtyRulesNoticeMessage ? $this->_dirtyRulesNoticeMessage : $defaultMessage;
    }

    public function downloadSampleAction() {
        $filename = Mage::getBaseDir('media') . DS . 'rewardpointsevent' . DS . 'import_customer_sample.csv';
        $this->_prepareDownloadResponse('import_customer_sample.csv', file_get_contents($filename));
    }

    public function downloadCurrentAction() {
        if ($this->getRequest()->getParam('name') != null) {
            $name = $this->getRequest()->getParam('name');
            $filename = Mage::getBaseDir('var') . DS . 'tmp' . DS . $name;
            $this->_prepareDownloadResponse($name, file_get_contents($filename));
        }
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $rewardpointseventIds = $this->getRequest()->getParam('rewardpointsevent');
        if (!is_array($rewardpointseventIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($rewardpointseventIds as $rewardpointseventId) {
                    $rewardpointsevent = Mage::getModel('rewardpointsevent/rewardpointsevent')->load($rewardpointseventId);
                    $rewardpointsevent->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($rewardpointseventIds))
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
        $rewardpointseventIds = $this->getRequest()->getParam('rewardpointsevent');
        if (!is_array($rewardpointseventIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($rewardpointseventIds as $rewardpointseventId) {
                    $event = Mage::getSingleton('rewardpointsevent/rewardpointsevent')
                            ->load($rewardpointseventId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true);
                    $event->save();
                    if ($event->getFileName() != '') {
                        $dir_csv = Mage::getBaseDir('var') . DS . 'tmp' . DS . $event->getFileName();
                        if (file_exists($dir_csv)) {
                            $file_path = $dir_csv;
                        }
                    }
                    if (isset($file_path) && $file_path != '' && $event->getCustomerApply() == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CSV) {
                        $csvObject = new Varien_File_Csv();
                        $dataFile = $csvObject->getData($file_path);
                        $customerData = array();
                        foreach ($dataFile as $row => $cols) {
                            if ($row == 0) {
                                $fields = $cols;
                            } else {
                                $customerData[] = array_combine($fields, $cols);
                            }
                        }
                    }
                    if (isset($customerData) && count($customerData)) {
                        $this->_updateCustomer($event, $customerData);
                    }
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($rewardpointseventIds))
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
        $fileName = 'rewardpointsevent.csv';
        $content = $this->getLayout()
                ->createBlock('rewardpointsevent/adminhtml_rewardpointsevent_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'rewardpointsevent.xml';
        $content = $this->getLayout()
                ->createBlock('rewardpointsevent/adminhtml_rewardpointsevent_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('rewardpoints/earning');
    }

}