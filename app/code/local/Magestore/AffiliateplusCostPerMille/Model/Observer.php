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
 * @package     Magestore_AffiliateplusCostPerMille
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusCostPerMille Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCostPerMille
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCostPerMille_Model_Observer {
    
    protected function _getConfigHelper(){
		return Mage::helper('affiliatepluscostpermille/config');
	}
    
    public function isActive()
    {
        return Mage::helper('affiliatepluscostpermille')->isDisplayed();
    }
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_AffiliateplusCostPerMille_Model_Observer
     */
    public function affiliateplus_action_prepare_create_transaction($observer) {
        if(!$this->isActive())
            return $this;
        $actionModel = $observer->getAffiliateplusAction();
        $account_id = $actionModel->getData('account_id');
        $banner_id = $actionModel->getData('banner_id');
        $store_id = $actionModel->getData('store_id');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $account = Mage::getModel('affiliateplus/account')->setStoreId($store_id)->load($account_id);
        
        if(($account->getStatus()==2) || ($account->getCustomerId()== $customer->getId()))
            return $this;
        
        $last = Mage::getModel('affiliateplus/action')->getCollection()
                        ->addFieldToFilter('action_id',array('neq'=>$actionModel->getId()))
                        ->addFieldToFilter('account_id',$account_id)
                        ->addFieldToFilter('is_commission',1)
                        ->addFieldToFilter('banner_id',$banner_id)
                        ->addFieldToFilter('store_id',$store_id)
                        ->addFieldToFilter('type',1)
                        ->getLastItem();
        
        if($last->getId()){
             $collection = Mage::getModel('affiliateplus/action')->getCollection()
                        ->addFieldToFilter('account_id',$account_id)
                        ->addFieldToFilter('banner_id',$banner_id)
                        ->addFieldToFilter('action_id',array('gt'=>$last->getId()))
                        ->addFieldToFilter('is_unique',1)
                        ->addFieldToFilter('store_id',$store_id)
                        ->addFieldToFilter('action_id',array('neq'=>$last
                            ->getId()))
                        ->addFieldToFilter('type',1)
                        ;
        }else{
             $collection = Mage::getModel('affiliateplus/action')->getCollection()
                        ->addFieldToFilter('account_id',$account_id)
                        ->addFieldToFilter('banner_id',$banner_id)
                        ->addFieldToFilter('is_unique',1)
                        ->addFieldToFilter('store_id',$store_id)
                        ->addFieldToFilter('type',1)
                        ;
        }
        if(count($collection) >= 2){
            $actionModel->setData('is_commission','1');
            $actionModel->save();
            $account = Mage::getModel('affiliateplus/account')->load($actionModel->getAccountId());
            $commission = $this->_getConfigHelper()->getCostpermilleConfig('commission_per_thousand_impressions');
            $banner = Mage::getModel('affiliateplus/banner')->setStoreId($store_id)->load($banner_id);
            if($banner->getStatus()==2)
                return $this;
            if($banner->getProgramId()){
                /*check program module*/
                $program = Mage::getModel('affiliateplusprogram/program')->setStoreId($store_id)->load($banner->getProgramId());
                $joinedColection = Mage::getResourceModel('affiliateplusprogram/account_collection')
                                ->addFieldToFilter('account_id',$account_id)
                                ->addFieldToFilter('program_id',$banner->getProgramId());
                        ;
                if($joinedColection->getSize())
                {
                    if($this->programIsActive($program)){
                        if(!$program->getIsImpressionCommissionDefault())
                            $commission = $program->getImpressionCommission();
                    }
                }
            }
            if($commission){
                $account->setBalance(floatval($account->getBalance()) + floatval($commission));
                try{
                    $account->save();
                }catch(Exception $e){
                }
                $transaction = Mage::getModel('affiliateplus/transaction');
                $transaction->setData('account_id',$account->getId());
                $transaction->setData('account_name',$account->getName());
                $transaction->setData('account_email',$account->getEmail());
                $transaction->setData('commission',$commission);
                $transaction->setData('banner_id',$banner_id);
                $transaction->setData('created_time',date("Y-m-d H:i:s"));
                $transaction->setData('type',1);
                $transaction->setData('store_id',$store_id);
                try{
                    $transaction->save();
                    if($program && $program->getId())
                        $this->createProgramTransaction($transaction, $program);
                }  catch (Exception $e){
                    
                }
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param type $transaction
     * @param type $program
     */
    public function createProgramTransaction($transaction, $program)
    {
        $transactionModel = Mage::getModel('affiliateplusprogram/transaction')
            ->setTransactionId($transaction->getId())
            ->setAccountId($transaction->getAccountId())
            ->setAccountName($transaction->getAccountName())
            ->setType($transaction->getType())
            ;
        //$transactionModel->addData($programData);
        $transactionModel
            ->setProgramId($program->getId())
            ->setProgramName($program->getName())
            ->setCommission($program->getImpressionCommission())
            ->setId(null)->save();
    }

    /**
     * process affiliateplus_get_list_program_welcome event
     *
     * @return Magestore_AffiliateplusCostPerMille_Model_Observer
     */
    public function addProgramWelcome($observer) {
        if(!$this->isActive())
            return $this;
        $commision = Mage::helper('affiliatepluscostpermille/config')->getCostpermilleConfig('commission_per_thousand_impressions');
        if(!$commision) return $this;
        
        $programListObj = $observer->getProgramListObject();
        $programList = $programListObj->getProgramList();
        if(count($programList)==0){
            $costpermilleProgram = new Varien_Object(array(
                        'code'  =>  'costpermille',
                        'name'  =>  Mage::helper('affiliatepluscostpermille')->__('Pay Per Mille'),
                        'custom_style'  =>  '1',
                        'block' =>  'affiliatepluscostpermille/affiliatepluscostpermille'
            ));
            $programList['cost-per-mille'] = $costpermilleProgram;
            $programListObj->setProgramList($programList);
        }
    }
    
    public function getStoreId(){
        $store = Mage::app()->getRequest()->getParam('store');
        if(!$store){
            $store = Mage::app()->getStore()->getId();
        }
        return $store;
    }

    /**
     * process affiliateplus_program_before_load event
     *
     * @return Magestore_AffiliateplusCostPerMille_Model_Observer
     */
    
    public function affiliateplus_program_load_after($observer)
    {
        if(!$this->isActive())
            return $this;
        $cpmCommission = $this->_getConfigHelper()->getCostpermilleConfig('commission_per_thousand_impressions');
        $program = $observer->getEvent()->getAffiliateplusProgram();
        $impression = Mage::getModel('affiliatepluscostpermille/impression')->loadByProgram($program->getId());
        if($impression->getId()){
            $program->setIsImpressionCommissionDefault($impression->getIsCommissionDefault());
            $program->setImpressionCommission($impression->getCommission());
        }
        
    }
    
     /**
     * process affiliateplus_program_before_load event
     *
     * @return Magestore_AffiliateplusCostPerMille_Model_Observer
     */
    
    public function affiliateplus_program_save_after($observer)
    {
        if(!$this->isActive())
            return $this;
        $program = $observer->getAffiliateplusProgram();
        if($program->getId()){
            $impression = Mage::getModel('affiliatepluscostpermille/impression')->loadByProgram($program->getId());
            $impression->setData('program_id',$program->getId());
            $impression->setData('is_commission_default',$program->getIsImpressionCommissionDefault());
            $impression->setData('commission',$program->getImpressionCommission());
            $impression->save();
        }
    }
    
    
     /**
     * process affiliateplus_program_get_store_attributes event
     *
     * @return Magestore_AffiliateplusCostPerMille_Model_Observer
     */
    public function affiliateplus_program_get_store_attributes($observer)
    {
        if(!$this->isActive())
            return $this;
        $obj = $observer->getAttributes();
        $storeAttribute = $obj->getStoreAttribute();
        $storeAttribute[] = 'is_impression_commission_default';
        $storeAttribute[] = 'impression_commission';
        $obj->setStoreAttribute($storeAttribute);
    }
    
    
     /**
     * process affiliateplus_adminhtml_add_column_transaction_grid event
     *
     * @return Magestore_AffiliateplusCostPerMille_Model_Observer
     */
    public function affiliateplus_adminhtml_add_column_transaction_grid($observer)
    {
        $grid = $observer->getGrid();
        if($grid->getColumn('type'))
            return $this;
        $grid->addColumn('type', array(
			'header'    => Mage::helper('affiliateplus')->__('Type'),
			'width'     => '80px',
			'index'     => 'type',
			'type'      => 'options',
			'options'   => Mage::getSingleton('affiliateplus/system_config_source_actiontype')->getOptionList()
		));
    }
    
    /**
     * 
     * @param type $observer
     */
    public function affiliateplus_get_action_types($observer)
    {
        $types = $observer->getTypes();
        $actions = $types->getActions();
        $actions[]=array('value' => '1', 'label'=>Mage::helper('affiliateplus')->__('CPM'));
        $types->setActions($actions);
    }
    
    /**
     * 
     * @param type $observer
     */
    public function affiliateplusprogram_adminhtml_edit_form($observer)
    {
        if(!$this->isActive())
            return $this;
        $fieldset = $observer->getFieldset();
        $dataform = $observer->getFormData();
        $store = $observer->getInStore();
        $defaultLabel = Mage::helper('affiliatepluscostpermille')->__('Use Default');
        $defaultTitle = Mage::helper('affiliatepluscostpermille')->__('-- Please Select --');
        $scopeLabel = Mage::helper('affiliatepluscostpermille')->__('STORE VIEW');
        $fieldset->addField('costpermille_separator', 'text', array(
            'label'     => Mage::helper('affiliatepluscostpermille')->__('Pay Per Mille'),
            'comment'   => '10px',
        ))->setRenderer(Mage::app()->getLayout()->createBlock('affiliateplus/adminhtml_field_separator'));
        $fieldset->addField('is_impression_commission_default','select',array(
            'name'      => 'is_impression_commission_default',
            'label'     => Mage::helper('affiliateplusprogram')->__('Pay Per Mille Commission ('.Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().')'),
            'values'	  => Mage::getSingleton('affiliatepluscostpermille/system_config_source_cpmcommissiontype')->toOptionArray(),
            'onchange'	=> 'onchangeOption()',
            'disabled'  => ($store && !$dataform['is_impression_commission_default_in_store']),
            'after_element_html' => $store ? '</td><td class="use-default">
            <input id="is_impression_commission_default_default" name="is_impression_commission_default_default" type="checkbox" value="1" class="checkbox config-inherit" '.($dataform['is_impression_commission_default_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
            <label for="is_impression_commission_default_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
            </td><td class="scope-label">
            <script type="text/javascript">
                function onchangeOption(){
                    var tag = $("affiliateplusprogram_is_impression_commission_default");
                    var des = $("affiliateplusprogram_impression_commission").parentNode.parentNode;
                    if(tag.value==1){
                        if(des) des.style.display = "none";
                    }else{
                        if(des) des.style.display = "";
                    }
                }
                Event.observe(window,\'load\',onchangeOption);
            </script>
            ['.$scopeLabel.']
            ' : '</td><td class="scope-label">
            <script type="text/javascript">
                function onchangeOption(){
                    var tag = $("affiliateplusprogram_is_impression_commission_default");
                    var des = $("affiliateplusprogram_impression_commission").parentNode.parentNode;
                    if(tag.value==1){
                        if(des) des.style.display = "none";
                    }else{
                        if(des) des.style.display = "";
                    }
                }
                Event.observe(window,\'load\',onchangeOption);
            </script>
            ['.$scopeLabel.']',
        ));
        $fieldset->addField('impression_commission', 'text', array(
          'label'     => Mage::helper('affiliateplusprogram')->__(''),
          'name'      => 'impression_commission',
          'disabled'  => ($store && !$dataform['impression_commission_in_store']),
          'after_element_html' => $store ? '</td><td class="use-default">
			<input id="impression_commission_default" name="impression_commission_default" type="checkbox" value="1" class="checkbox config-inherit" '.($dataform['impression_commission_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="impression_commission_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label">
			['.$scopeLabel.']
          ' : '</td><td class="scope-label">
			['.$scopeLabel.']',
	  ));
    }
    
    /**
     * 
     * @param type $observer
     * @return \Magestore_AffiliateplusCostPerMille_Model_Observer
     */
    public function affiliateplus_prepare_program($observer)
    {
        if(!$this->isActive())
            return $this;
        $row = $observer->getInfo();
        $store_id = Mage::app()->getStore()->getId();
        if(Mage::helper('affiliatepluscostpermille')->programPluginIsActive()){
            $program = Mage::getModel('affiliateplusprogram/program')->setStoreId($store_id)->load($row['program_id']);
            
            if($this->programIsActive($program)){
                $cpmCommission = $this->_getConfigHelper()->getCostpermilleConfig('commission_per_thousand_impressions');
                if(!$program->getIsImpressionCommissionDefault())
                    $cpmCommission = $program->getImpressionCommission();
                $object = $observer->getObj();
                if($object){
                    $html = $object->getHtmlView();
                    if($cpmCommission){
                        $html .= Mage::helper('affiliatepluscostpermille')->__('<br/>Pay Per Mille: '). '<strong>'.Mage::helper('core')->currency($cpmCommission).'</strong>';
                        $object->setHtmlView($html);
                    }
                }
            }
        }
    }
    
    public function affiliateplus_traffics_statistic($observer)
    {
        if(!$this->isActive())
            return $this;
        $traffics = $observer->getTraffics();
        $data = $traffics->getData();
        $cpminfo = Mage::getModel('affiliateplus/action')->getTrafficInfo(1,'impression','Impressions (unique/ raw)');
        $data[] = $cpminfo;
        $traffics->setData($data);
    }
    
    /**
     * 
     * @param type $observer
     * @return \Magestore_AffiliateplusCostPerMille_Model_Observer
     */
    public function affiliateplus_show_program_on_welcome($observer)
    {
        
        if(!$this->isActive())
            return $this;
        
        $textObject = $observer->getTextObject();
        $program = $observer ->getProgram();
        
        $cpmCommission = $this->_getConfigHelper()->getCostpermilleConfig('commission_per_thousand_impressions');
        if($this->programIsActive($program)){
			// Changed By Adam: 22/10/2014
            $program->setStoreId($this->getStoreId())->load($program->getId());
            if(!$program->getIsImpressionCommissionDefault())
                $cpmCommission = $program->getImpressionCommission();
        }
        if($cpmCommission){
            $text = $textObject->getText();
            $text.='<br/>Pay Per Mille: '.Mage::helper('core')->currency($cpmCommission);
            $textObject->setText($text);
        }
    }
    
    /**
     * 
     * @param type $program
     * @return boolean
     */
    
    public function programIsActive($program)
    {
        if($program->getId()){
            if($program->isAvailable())
                return true;
        }
        return false;
    }
}