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
 * @package     Magestore_AffiliateplusPayPerClick
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusPayPerClick Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusPayPerClick
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusPayPerClick_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_AffiliateplusPayPerClick_Model_Observer
     */
    public function getStoreId() {
        $store = Mage::app()->getRequest()->getParam('store');
        if (!$store) {
            $store = Mage::app()->getStore()->getId();
        }
        return $store;
    }

    protected function _getConfigHelper() {
        return Mage::helper('affiliatepluspayperclick');
    }

    public function saveActionBefore($observer) {
        $action = $observer['action'];
        $isUnique = $observer['is_unique'];
        if (!$isUnique)
            return $this;
        $storeId = Mage::app()->getStore()->getId();
        $account = Mage::getModel('affiliateplus/account')->setStoreId($storeId)->load($action->getAccountId());
        $banner = Mage::getModel('affiliateplus/banner')->load($action->getBannerId());

        $default = 1;
        $isprogramEnabled = $this->_getConfigHelper()->isModuleOutputEnabled('Magestore_Affiliateplusprogram');
        if ($banner->getProgramId() && $isprogramEnabled) {
            $program = Mage::getModel('affiliateplusprogram/program')->load($banner->getProgramId());
            $program->loadStoreValue($storeId);
            $checkprogram = Mage::getModel('affiliateplusprogram/account')->getCollection()
                    ->addFieldToFilter('program_id', $program->getProgramId())
                    ->addFieldToFilter('account_id', $account->getId());
            $programavailable = $program->isAvailable();
            if ($programavailable && $checkprogram->getSize() && ($program->getStatus() == 1)) {
                if (!$program->getIsClickCommissionDefault()) {
                    $default = 0;
                    $commission = $program->getClickCommission();
                }
            }
        }
        if ($default)
            $commission = $this->_getConfigHelper()->getPayPerClickConfig('clickcommission');

        if ($commission !=0 ) {
            $account_id = $account->getId();
            $account_name = $account->getName();
            $account_email = $account->getEmail();

            if ($isUnique) {
                $account->setBalance(floatval($account->getBalance()) + floatval($commission));
                try {
                    $account->save();
                } catch (Exception $e) {
                    
                }
                $this->_getConfigHelper()->addTransaction($account_id, $account_name, $account_email, $commission, $storeId, $banner->getId(), $banner->getProgramId());
                $action->setIsCommission(1)->save();
            }
        }
        return $this;
    }

    public function affiliatepluspayperclick_add_program_tab($observer) {
        if (!$this->_getConfigHelper()->isPluginEnabled())
            return $this;
        $fieldset = $observer->getFieldset();
        $dataform = $observer->getFormData();
        $store = $observer->getInStore();
        $defaultLabel = Mage::helper('affiliatepluspayperclick')->__('Use Default');
        $defaultTitle = Mage::helper('affiliatepluspayperclick')->__('-- Please Select --');
        $scopeLabel = Mage::helper('affiliatepluspayperclick')->__('STORE VIEW');
        $fieldset->addField('payperclick_separator', 'text', array(
            'label'     => Mage::helper('affiliatepluspayperclick')->__('Pay per click'),
            'comment'   => '10px',
        ))->setRenderer(Mage::app()->getLayout()->createBlock('affiliateplus/adminhtml_field_separator'));
        $fieldset->addField('is_click_commission_default', 'select', array(
            'name' => 'is_click_commission_default',
            'label' => Mage::helper('affiliateplusprogram')->__('Pay per click Commission') . ' (' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol() . ')',
            'values' => Mage::getSingleton('affiliatepluspayperclick/system_config_source_ppccommissiontype')->toOptionArray(),
            'onchange' => 'onchangeOptionClick()',
            'disabled' => ($store && !$dataform['is_click_commission_default_in_store']),
            'after_element_html' => $store ? '</td><td class="use-default">
            <input id="is_click_commission_default_default" name="is_click_commission_default_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($dataform['is_click_commission_default_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
            <label for="is_click_commission_default_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
            </td><td class="scope-label">
            <script type="text/javascript">
                function onchangeOptionClick(){
                    var tag = $("affiliateplusprogram_is_click_commission_default");
                    var des = $("affiliateplusprogram_click_commission").parentNode.parentNode;
                    if(tag.value==1){
                        if(des) des.style.display = "none";
                    }else{
                        if(des) des.style.display = "";
                    }
                }
                Event.observe(window,\'load\',onchangeOptionClick);
            </script>
            [' . $scopeLabel . ']
            ' : '</td><td class="scope-label">
            <script type="text/javascript">
                function onchangeOptionClick(){
                    var tag = $("affiliateplusprogram_is_click_commission_default");
                    var des = $("affiliateplusprogram_click_commission").parentNode.parentNode;
                    if(tag.value==1){
                        if(des) des.style.display = "none";
                    }else{
                        if(des) des.style.display = "";
                    }
                }
                Event.observe(window,\'load\',onchangeOptionClick);
            </script>
            [' . $scopeLabel . ']',
        ));
        $fieldset->addField('click_commission', 'text', array(
            'label' => Mage::helper('affiliateplusprogram')->__(''),
            'name' => 'click_commission',
            'disabled' => ($store && !$dataform['click_commission_in_store']),
            'after_element_html' => $store ? '</td><td class="use-default">
			<input id="click_commission_default" name="click_commission_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($dataform['click_commission_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="click_commission_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label">
			[' . $scopeLabel . ']
          ' : '</td><td class="scope-label">
			[' . $scopeLabel . ']',
        ));
    }

    public function addProgramClickCommissionProgramLoadAfter($observer) {
        if (!$this->_getConfigHelper()->isPluginEnabled())
            return $this;
        $program = $observer->getEvent()->getAffiliateplusProgram();
        if ($program->getId()) {
            $collection = Mage::getModel('affiliatepluspayperclick/clickprogramcommission')->getCollection()
                    ->addFieldToFilter('program_id', $program->getId());
            $click = $collection->getFirstItem();
            if ($click->getId()) {
                $program->setClickCommission($click->getCommission());
                $program->setIsClickCommissionDefault($click->getIsCommissionDefault());
            }
        }
    }

    public function saveProgramClickCommissionProgramSaveAfter($observer) {
        if (!$this->_getConfigHelper()->isPluginEnabled())
            return $this;
        $program = $observer->getAffiliateplusProgram();
        $collection = Mage::getModel('affiliatepluspayperclick/clickprogramcommission')->getCollection()
                ->addFieldToFilter('program_id', $program->getId());
        if ($collection->getSize()) {
            $click = $collection->getFirstItem();
        } else {
            $click = Mage::getModel('affiliatepluspayperclick/clickprogramcommission');
        }
        $click->setData('program_id', $program->getId())
                ->setData('commission', $program->getClickCommission())
                ->setData('is_commission_default', $program->getIsClickCommissionDefault());
        $click->save();
    }

    public function addProgramClickCommissionAttributes($observer) {
        if (!$this->_getConfigHelper()->isPluginEnabled())
            return $this;
        $obj = $observer->getAttributes();
        $storeAttribute = $obj->getStoreAttribute();
        $storeAttribute[] = 'is_click_commission_default';
        $storeAttribute[] = 'click_commission';
        $obj->setStoreAttribute($storeAttribute);
    }

    public function addClickTrafficsStatistic($observer) {
        if (!$this->_getConfigHelper()->isPluginEnabled())
            return $this;
        $obj = $observer->getTraffics();
        $info = Mage::getModel('affiliateplus/action')->getTrafficInfo(2, 'clicks', 'Clicks (unique/raw)');
        $obj[] = $info;
    }

    public function addClickProgramDetail($observer) {
        if (!$this->_getConfigHelper()->isPluginEnabled())
            return $this;
        $row = $observer->getInfo();
        $object = $observer->getObj();
        if ($object) {
            $html = $object->getHtmlView();
            $storeId = Mage::app()->getStore()->getId();
            if ($row['program_id']) {
                $program = Mage::getModel('affiliateplusprogram/program')->load($row['program_id']);
                $program->loadStoreValue($storeId);
                if ($program->getProgramId() || 1) {
                    if ($program->getIsClickCommissionDefault()) {
                        $commission = $this->_getConfigHelper()->getPayPerClickConfig('clickcommission');
                    } else {
                        $commission = $program->getClickCommission();
                    }
                }
            }

            if ($commission != 0) {
                $html .= Mage::helper('affiliatepluspayperclick')->__('<br>Pay per click: ') . '<strong>' . Mage::helper('core')->currency($commission) . '</strong>';
                $object->setHtmlView($html);
            }
        }
        return $this;
    }

    public function affiliateplus_get_action_types($observer) {
        $types = $observer->getTypes();
        $actions = $types->getActions();
        $actions[] = array('value' => '2', 'label' => Mage::helper('affiliateplus')->__('PPC'));
        $types->setActions($actions);
    }

    public function addColumnTransactionGrid($observer) {
        $grid = $observer->getGrid();
        if ($grid->getColumn('type'))
            return $this;
        $grid->addColumn('type', array(
            'header' => Mage::helper('affiliateplus')->__('Type'),
            'width' => '80px',
            'index' => 'type',
            'type' => 'options',
            'options' => Mage::getSingleton('affiliateplus/system_config_source_actiontype')->getOptionList()
        ));
    }

    public function addClickWelcomeProgram($observer) {
        if (!$this->_getConfigHelper()->isPluginEnabled())
            return $this;
        $obj = $observer->getTextObject();
        $program = $observer->getProgram();
        if ($this->programIsActive($program)) {
            $storeId = Mage::app()->getStore()->getId();
            $program = Mage::getModel('affiliateplusprogram/program')->load($program->getId());
            $program->loadStoreValue($storeId);
            if ($program->getIsClickCommissionDefault()) {
                $commission = $this->_getConfigHelper()->getPayPerClickConfig('clickcommission');
            } else {
                $commission = $program->getClickCommission();
            }
        } else {
            $commission = $this->_getConfigHelper()->getPayPerClickConfig('clickcommission');
        }
        if ($commission != 0)
            $obj->setText($obj->getText() . '<br>Pay per click ' . Mage::helper('core')->currency($commission) . ' for each click you generate.');

        return $this;
    }

    public function addColumnAccountTransactionGrid($observer) {
        $grid = $observer->getGrid();
        if ($grid->getColumn('type'))
            return $this;
        $grid->addColumn('type', array(
            'header' => Mage::helper('affiliateplus')->__('Type'),
            'width' => '80px',
            'index' => 'type',
            'type' => 'options',
            'options' => Mage::getSingleton('affiliateplus/system_config_source_actiontype')->getOptionList()
        ));
    }

    public function programIsActive($program) {
        if ($program->getId()) {
            if ($program->isAvailable())
                return true;
        }
        return false;
    }
    public function addProgramWelcome($observer){
        if (!$this->_getConfigHelper()->isPluginEnabled())
            return $this;
        $programListObj = $observer->getProgramListObject();
        $programList = $programListObj->getProgramList();
        $payperclickProgram = new Varien_Object(array(
                    'code'  =>  'payperclick',
                    'name'  =>  Mage::helper('affiliatepluspayperclick')->__('Pay per click'),
                    'custom_style'  =>  '1',
                    'block' =>  'affiliatepluspayperclick/affiliatepluspayperclick'
        ));
        $programList['pay-per-click'] = $payperclickProgram;
        $programListObj->setProgramList($programList);
    }

}