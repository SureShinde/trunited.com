<?php

class Magestore_Nationpassport_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * check customer is logged in
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        $action = $this->getRequest()->getActionName();
        if ($action != 'policy' && $action != 'redirectLogin') {
            // Check customer authentication
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                Mage::getSingleton('customer/session')->setAfterAuthUrl(
                    Mage::getUrl($this->getFullActionName('/'))
                );
                $this->_redirect('customer/account/login');
                $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            }
        }
    }

	public function indexAction(){
		$this->loadLayout();

        $this->_title(Mage::helper('nationpassport')->__('Trunited Nation Passport'));

        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link"  => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("passport", array(
            "label" => $this->__("Trunited Nation Passport"),
            "title" => $this->__("Trunited Nation Passport"),
        ));

		$this->renderLayout();
	}

    public function updateDbAction(){
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
              ALTER TABLE {$setup->getTable('rewardpoints/transaction')} MODIFY COLUMN point_amount FLOAT ;
              ALTER TABLE {$setup->getTable('rewardpoints/transaction')} MODIFY COLUMN point_used  FLOAT ;
              ALTER TABLE {$setup->getTable('rewardpoints/transaction')} MODIFY COLUMN real_point  FLOAT ;
              
              ALTER TABLE {$setup->getTable('rewardpoints/customer')} MODIFY COLUMN point_balance FLOAT ;
              ALTER TABLE {$setup->getTable('rewardpoints/customer')} MODIFY COLUMN holding_balance   FLOAT ;
              ALTER TABLE {$setup->getTable('rewardpoints/customer')} MODIFY COLUMN spent_balance   FLOAT ;
        ");
        $installer->endSetup();
        echo "success";
    }
}
