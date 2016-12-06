<?php
/**
 * Magebird.com
 *
 * @category   Magebird
 * @package    Magebird_Popup
 * @copyright  Copyright (c) 2014 Magebird (http://www.Magebird.com)
 * @license    http://www.magebird.com/licence
 * Any form of ditribution, sell, transfer forbidden see licence above 
 */
class Magebird_Popup_IndexController extends Mage_Core_Controller_Front_Action
{
    public function IndexAction() {
	  
    }
    
    public function showAction()
    {                                                               
      header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
      //it seems previous request to magebirdpopup.php wasn't successfull, switch to magebird_popup/index/show           
      if($this->getRequest()->getParam('switchRequestType') && Mage::getStoreConfig('magebird_popup/settings/requestswitched')!=1){
        Mage::getModel('core/config')->saveConfig('magebird_popup/settings/requesttype', 3);
        Mage::getModel('core/config')->saveConfig('magebird_popup/settings/requestswitched', 1);
        Mage::app()->getCacheInstance()->cleanType('config');
      }          
      Mage::getModel('magebird_popup/popup')->addNewView();
      $block = $this->getLayout()->createBlock('magebird_popup/popup')->setTemplate('magebird/popup/popup.phtml');
      $this->getResponse()->setBody($block->toHtml());                         
    }       
    
    public function previewAction()
    {                             
      header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");       
  	  $this->loadLayout();     
  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Popup Preview"));
      $this->renderLayout();
    }    
    
    public function templateAction()
    {    
      header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");       
  	  $this->loadLayout();        
  	  $this->getLayout()->getBlock("head")->setTitle($this->__("Template Preview"));
      $this->renderLayout();
    } 
    
  	public function statsAction(){     
      if(Mage::helper('magebird_popup')->getIsCrawler()) return;      
      if($this->getRequest()->getParam('mousetracking')){
        Mage::helper('magebird_popup/mousetracking')->handleMousetracking();
      }
      $popupIds = array();
      if($popupId = $this->getRequest()->getParam('popupId')){
        $popupIds[$popupId] = $this->getRequest()->getParam('time');
      }
      if($popupIds2 = $this->getRequest()->getParam('popupIds')){
        $popupIds2 = json_decode($popupIds2);
        foreach($popupIds2 as $id => $time){
          $popupIds[$id] = $time;
        }        
      }              
       
      foreach($popupIds as $popupId => $time){
        $_popup = Mage::getModel('magebird_popup/popup')->load($popupId);            
        if($_popup->getData('popup_id')){
          $views = $_popup->getData('views');
          if(
            ($_popup->getData('background_color')!=3 && $_popup->getData('background_color')!=4) 
            ||  
            (($_popup->getData('background_color')==3 || $_popup->getData('background_color')!=4) && $_popup->getData('show_when')!=1)
          ){  
            $_popup->setPopupData($popupId,'views',$views+1);   
            Mage::getModel('magebird_popup/popup')->uniqueViewStats($_popup->getData('popup_id'));      
          }          
          
          $totalTime = $_popup->getData('total_time');
          $currentViewSpent = $time;          
          if($currentViewSpent>($_popup->getData('max_count_time')*1000)){
            $currentViewSpent = $_popup->getData('max_count_time')*1000;
          }
          $_popup->setPopupData($popupId,'total_time',$totalTime+$currentViewSpent);   
          if($this->getRequest()->getParam('closed')==1){      
            $_popup->setPopupData($popupId,'popup_closed',$_popup->getData('popup_closed')+1);
          }elseif($this->getRequest()->getParam('windowClosed')==1){       
            if($_popup->getData('background_color')!=3 && $_popup->getData('background_color')!=4){
              //prever če ni to kaj fore s tem ker uporabm getter znotraj setterja
              $_popup->setPopupData($popupId,'window_closed',$_popup->getData('window_closed')+1);
              $_popup->setPopupData($popupId,'last_rand_id',$this->getRequest()->getParam('lastPageviewId'));
            } 
          }elseif($this->getRequest()->getParam('clickInside')==1){                    
            $_popup->setPopupData($popupId,'click_inside',$_popup->getData('click_inside')+1);
          }         
        }
      }
  	}
    
    public function popupCartsCountAction(){
      $popupId = $this->getRequest()->getParam('popupId');
      Mage::getModel('magebird_popup/popup')->uniqueViewStats($popupId);
    }         
      
} 