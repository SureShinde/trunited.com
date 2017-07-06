<?php

class Magestore_Other_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		zend_debug::dump(Mage::helper('other')->ytd());
		zend_debug::dump( Mage::helper('other')->mtd());
		$this->loadLayout();
		$this->renderLayout();
	}
}