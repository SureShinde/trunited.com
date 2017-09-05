<?php
/**

 */
 
class Monyet_Easygiftcard_HistoryController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	
		$this->loadLayout();  
		if($head = $this->getLayout()->getBlock('head'))
            $head->setTitle(Mage::helper('sales')->__('My Gift Cards'));
		$this->renderLayout();
    }
	
   
}