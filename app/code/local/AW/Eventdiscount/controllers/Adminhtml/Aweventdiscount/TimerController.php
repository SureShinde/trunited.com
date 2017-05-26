<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Eventdiscount
 * @version    1.0.5
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Eventdiscount_Adminhtml_Aweventdiscount_TimerController extends Mage_Adminhtml_Controller_Action
{
    protected function _initTimer()
    {
        $timerModel = Mage::getModel('aweventdiscount/timer');
        $timerId  = (int) $this->getRequest()->getParam('id');
        if ($timerId) {
            try {
                $timerModel->load($timerId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $timerModel->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $timerModel->setOrigData('conditions_serialized', $timerModel->getData('conditions'));
        if (null !== Mage::getSingleton('adminhtml/session')->getTimerData()) {
            $timerModel->addData(Mage::getSingleton('adminhtml/session')->getTimerData());
            Mage::getSingleton('adminhtml/session')->setTimerData(null);
        }
        Mage::register('timer_data', $timerModel);
        return $timerModel;
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('promo/eventdiscount')
            ->_addBreadcrumb(
                Mage::helper('eventdiscount')->__('Event Based Discounts'),
                Mage::helper('adminhtml')->__('Event Based Discounts')
            )
        ;
        $this->_setTitle($this->__('Timer List'));
        return $this;
    }

    public function indexAction()
    {
        return $this->_redirect('*/*/list');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function listAction()
    {
        $this
            ->_initAction()
            ->renderLayout()
        ;
    }

    public function massDeleteAction()
    {
        $timerIds = $this->getRequest()->getParam('id', null);
        if (is_array($timerIds)) {
            try {
                foreach ($timerIds as $id) {
                    Mage::getModel('aweventdiscount/timer')
                        ->load($id)
                        ->delete()
                    ;
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($timerIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function massStatusAction()
    {
        $timerIds = $this->getRequest()->getParam('id', null);
        if (is_array($timerIds)) {
            try {
                foreach ($timerIds as $id) {
                    $model = Mage::getModel('aweventdiscount/timer')->load($id);
                    $model
                        ->setData('status', $this->getRequest()->getParam('status'))
                        ->save()
                    ;
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully updated', count($timerIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initTimer();
        $act = Mage::helper('eventdiscount')->__('New Timer');
        if ($id) {
            $act = Mage::helper('eventdiscount')->__('Edit Timer');
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('eventdiscount')->__('This timer no longer exists')
                );
                return $this->_redirect('*/*');
            }
        }
        $this->_setTitle($this->__($act));
        $add = new Mage_SalesRule_Model_Rule_Condition_Address();
        $add->setData('conditions', unserialize($model->getData('conditions')));

        $this->loadLayout();
        $this->_setActiveMenu('promo');

        $block = $this->getLayout()
            ->createBlock('eventdiscount/adminhtml_timer_edit')
            ->setData('action', $this->getUrl('*/save'))
        ;
        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true)
        ;
        $this
            ->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('eventdiscount/adminhtml_timer_edit_tabs'))
            ->renderLayout();
        return $this;
    }

    public function deleteAction()
    {
        $model = $this->_initTimer();
        if ($model->getId()) {
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('eventdiscount')->__('Timer was successfully deleted')
                );
                return $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return $this->_redirect('*/*/');
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('eventdiscount')->__('Delete error'));
        return $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getParams();
        $product_ids = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['products']);
        zend_debug::dump($product_ids);
        exit;
        if ($data = $this->getRequest()->getPost()) {
            $data['duration'] = $data['duration'][0] * 3600 + $data['duration'][1] * 60 + $data['duration'][2];
            try {
                if ($data['duration'] == 0) {
                    throw new Exception($this->__('Duration should be greater than 1 second.'));
                }

                $model = $this->_initTimer();
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                // Store ids
                if (is_array($data['store_ids'])) {
                    $_storesString = implode(',', $data['store_ids']);
                    if (in_array('0', $data['store_ids'])) {
                        $_storesString = '0';
                    }
                    $data['store_ids'] = $_storesString;
                }

                //Group ids
                if (is_array($data['customer_group_ids'])) {
                    $customerGroupIds = implode(',', $data['customer_group_ids']);
                    unset($data['customer_group_ids']);
                    $data['customer_group_ids'] = $customerGroupIds;
                }

                $data = $this->_filterDateTime($data, array('active_from', 'active_to'));

                $now = Mage::app()->getLocale()->date();
                if ($data['active_from'] == '') {
                    $data['active_from'] = $now->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                }
                if ($data['active_to'] == '') {
                    $data['active_to'] = $now->addDay(1)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                }
                $data['active_from'] = Mage::app()->getLocale()
                    ->utcDate(null, $data['active_from'], true, Varien_Date::DATETIME_INTERNAL_FORMAT)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                ;
                $data['active_to'] = Mage::app()->getLocale()
                    ->utcDate(null, $data['active_to'], true, Varien_Date::DATETIME_INTERNAL_FORMAT)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                ;

                //Check date
                if (strtotime($data['active_to']) < strtotime($data['active_from'])) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('eventdiscount')->__('\'Active to\' date should be greater than start date')
                    );
                    Mage::getSingleton('adminhtml/session')->setTimerData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return $this;
                }

                //check set flag url_type
                $_urlType = 0;
                if (isset($data['url_type'])) {
                    $_urlType = 1;
                }
                $data['url_type'] = $_urlType;

                $model->setData('actions_to_save', $data['actions']);
                unset($data['actions']);
                $model->loadPost($data)->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('eventdiscount')->__('Timer was successfully saved')
                );

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit',
                        array('id' => $model->getId(), 'tab' => Mage::app()->getRequest()->getParam('tab'))
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTimerData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return $this;
            }
        }
        $this->_redirect('*/*/');
        return $this;
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('aweventdiscount/timer'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Returns true when admin session contain error messages
     */
    protected function _hasErrors()
    {
        return (bool)count($this->_getSession()->getMessages()->getItemsByType('error'));
    }

    /**
     * Set title of page
     */
    protected function _setTitle($action)
    {
        if (method_exists($this, '_title')) {
            $this->_title($this->__(' Event based discount timer'))->_title($this->__($action));
        }
        return $this;
    }

    public function productsAction()
    {
        $product_ids = array();

        $this->loadLayout()
            ->getLayout()
            ->getBlock('eventdiscount.timer.edit.tab.products')
            ->setProducts($product_ids)
        ;

        $this->renderLayout();
    }

    public function productsGridAction()
    {
        $this->loadLayout()
            ->getLayout()
            ->getBlock('eventdiscount.timer.edit.tab.products')
            ->setProducts($this->getRequest()->getPost('products', null));

        $this->renderLayout();
    }
}