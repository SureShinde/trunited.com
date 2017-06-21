<?php

class Magestore_ManageApi_Adminhtml_ManageapiController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('manageapi/manageapi')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = null;
        if ($model != null && $model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data))
                $model->setData($data);

            Mage::register('manageapi_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('manageapi/manageapi');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('manageapi/adminhtml_manageapi_edit'))
                ->_addLeft($this->getLayout()->createBlock('manageapi/adminhtml_manageapi_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manageapi')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $start_date = date('Y-m-d', strtotime($data['start_date']));
            $end_date = date('Y-m-d', strtotime($data['end_date']));
            $select_apis = $data['select_api'];

            if (is_array($select_apis) && sizeof($select_apis) > 0) {
                $api_called = array();

                foreach ($select_apis as $api_name) {
                    if ($api_name == 1) {
                        // link share api
                        $enable = $this->getHelperData()->getDataConfig('enable', 'link_share');
                        if($enable)
                        {
                            $url = $this->getHelperData()->getDataConfig('link_share_api', 'link_share');
                            if($url != null)
                            {
                                $_url = str_replace(array('{{start_date}}','{{end_date}}'),array($start_date, $end_date), $url);
                                $api_called[$_url] = '<a href="'.$_url.'" target="_blank">LINK SHARE API</a> ';
                                $file = Mage::getBaseDir('media') . DS . Magestore_ManageApi_Helper_Data::LINK_SHARE_FILE;
                                Mage::helper('manageapi/linkshare')->processAPI($_url, $file);
                            }
                        }
                    } else if ($api_name == 2) {
                        // price line hotel api
                        $enable = $this->getHelperData()->getDataConfig('enable_hotel', 'price_line');
                        if ($enable) {
                            $url = $this->getHelperData()->getDataConfig('hotel_api', 'price_line');
                            if ($url != null) {
                                $format = $this->getHelperData()->getDataConfig('hotel_format', 'price_line');
                                $start_date_hotel = date('Y-m-d_00:00:00', strtotime($start_date));
                                $end_date_hotel = date('Y-m-d_23:59:59', strtotime($end_date));
                                $_url = str_replace(array('{{start_date}}', '{{end_date}}', '{{format}}'), array($start_date_hotel, $end_date_hotel, $format), $url);
                                $api_called[$_url] = '<a href="'.$_url.'" target="_blank">PRICE LINE HOTEL API</a> ';
                                Mage::helper('manageapi/hotel')->processAPI($_url);
                            }
                        }
                    } else if ($api_name == 3) {
                        // price line flight api
                        $enable = $this->getHelperData()->getDataConfig('enable_flight', 'price_line');
                        if ($enable) {
                            $url = $this->getHelperData()->getDataConfig('flight_api', 'price_line');
                            if ($url != null) {
                                $format = $this->getHelperData()->getDataConfig('flight_format', 'price_line');
                                $start_date_flight = date('Y-m-d_00:00:00', strtotime($start_date));
                                $end_date_flight = date('Y-m-d_23:59:59', strtotime($end_date));
                                $_url = str_replace(array('{{start_date}}', '{{end_date}}', '{{format}}'), array($start_date_flight, $end_date_flight, $format), $url);
                                $api_called[$_url] = '<a href="'.$_url.'" target="_blank">PRICE LINE FLIGHT API</a> ';
                                Mage::helper('manageapi/flight')->processAPI($_url);
                            }
                        }
                    } else if ($api_name == 4) {
                        // price line car api
                        $enable = $this->getHelperData()->getDataConfig('enable_car', 'price_line');
                        if ($enable) {
                            $url = $this->getHelperData()->getDataConfig('car_api', 'price_line');
                            if ($url != null) {
                                $format = $this->getHelperData()->getDataConfig('car_format', 'price_line');
                                $start_date_car = date('Y-m-d_00:00:00', strtotime($start_date));
                                $end_date_car = date('Y-m-d_23:59:59', strtotime($end_date));
                                $_url = str_replace(array('{{start_date}}', '{{end_date}}', '{{format}}'), array($start_date_car, $end_date_car, $format), $url);
                                $api_called[$_url] = '<a href="'.$_url.'" target="_blank">PRICE LINE CAR API</a> ';
                                Mage::helper('manageapi/car')->processAPI($_url);
                            }
                        }
                    } else if ($api_name == 5) {
                        // price line vacation api
                        $enable = $this->getHelperData()->getDataConfig('enable_vacation', 'price_line');
                        if ($enable) {
                            $url = $this->getHelperData()->getDataConfig('vacation_api', 'price_line');
                            if ($url != null) {
                                $format = $this->getHelperData()->getDataConfig('vacation_format', 'price_line');
                                $start_date_vacation = date('Y-m-d_00:00:00', strtotime($start_date));
                                $end_date_vacation = date('Y-m-d_23:59:59', strtotime($end_date));
                                $_url = str_replace(array('{{start_date}}', '{{end_date}}', '{{format}}'), array($start_date_vacation, $end_date_vacation, $format), $url);
                                $api_called[$_url] = '<a href="'.$_url.'" target="_blank">PRICE LINE VACATION API</a> ';
                                Mage::helper('manageapi/vacation')->processAPI($_url);
                            }
                        }
                    } else if ($api_name == 6) {
                        // cj api
                        $enable = $this->getHelperData()->getDataConfig('enable_cj', 'cj');
                        if ($enable) {
                            $url = $this->getHelperData()->getDataConfig('cj_api', 'cj');
                            if ($url != null) {
                                $data_type = $this->getHelperData()->getDataConfig('cj_data_type', 'cj');
                                $_url = str_replace(array('{{start_date}}', '{{end_date}}', '{{data_type}}'), array($start_date, $end_date, $data_type), $url);
                                $api_called[$_url] = '<a href="'.$_url.'" target="_blank">CJ API</a> ';
                                Mage::helper('manageapi/cj')->processAPI($_url);
                            }
                        }
                    } else if ($api_name == 7) {
                        // target api
                        $enable = $this->getHelperData()->getDataConfig('enable_target', 'target');
                        if ($enable) {
                            $url = $this->getHelperData()->getDataConfig('target_api', 'target');
                            if ($url != null) {
                                $_url = str_replace(array('{{start_date}}', '{{end_date}}'), array($start_date, $end_date), $url);
                                $api_called[$_url] = '<a href="'.$_url.'" target="_blank">TARGET API</a> ';
                                Mage::helper('manageapi/target')->processAPI($_url);
                            }
                        }
                    }
                }

                if($api_called != '')
                {
                    Mage::getSingleton('adminhtml/session')->setData('url_called', $api_called);
                    Mage::getSingleton('adminhtml/session')->setData('api_called', $select_apis);
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('manageapi')->__('APIs were successfully run'));
                } else {
                    Mage::getSingleton('adminhtml/session')->addError('Error');
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Unable to find API to run');
            }

            $this->_redirect('*/*/new',array('run' => true));
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manageapi')->__('Unable to find item to save'));
        $this->_redirect('*/*/new');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('manageapi/manageapi');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $manageapiIds = $this->getRequest()->getParam('manageapi');
        if (!is_array($manageapiIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($manageapiIds as $manageapiId) {
                    $manageapi = Mage::getModel('manageapi/manageapi')->load($manageapiId);
                    $manageapi->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($manageapiIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $manageapiIds = $this->getRequest()->getParam('manageapi');
        if (!is_array($manageapiIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($manageapiIds as $manageapiId) {
                    $manageapi = Mage::getSingleton('manageapi/manageapi')
                        ->load($manageapiId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($manageapiIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'manageapi.csv';
        $content = $this->getLayout()->createBlock('manageapi/adminhtml_manageapi_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'manageapi.xml';
        $content = $this->getLayout()->createBlock('manageapi/adminhtml_manageapi_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}