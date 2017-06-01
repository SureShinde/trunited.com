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

class AW_Eventdiscount_TimerController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $request = $this->getRequest();
        AW_Eventdiscount_Model_Cron::run();
        $this->_redirectUrl(str_replace('___', '/', $request->getParam('returnurl')));
    }

    public function ajaxAction()
    {
        $response = new Varien_Object();
        $responseTimer = array();
        $response->setError(0);
        try {
            if ($customerId = Mage::getModel('customer/session')->getCustomerId()) {
                $response->setCustomerId($customerId);
            } else throw new Exception($this->__('Customer not Login'));

            /** @var $triggerCollection  AW_Eventdiscount_Model_Resource_Trigger_Collection*/
            $triggerCollection = Mage::getModel('aweventdiscount/trigger')->getCollection();
            $triggerCollection->joinWithTimer();
            $triggerCollection->addCustomerIdFilter($customerId);
            $triggerCollection->addStatusFilter();
            $triggerCollection->addTimeLimitFilter();
             $triggerCollection->addNotLoadIdFilter(Mage::getModel('customer/session')->getClosedTimerId());
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            if (!$triggerCollection->getSize()) throw new Exception($this->__('Trigger not found for this customer'));
            foreach ($triggerCollection as $item) {
                //Check quote triggers
                if (($item->getData('trigger_event') == AW_Eventdiscount_Model_Event::CARTUPDATE)
                    && (!$quote->hasItems())
                ) {
                    $item->setTriggerStatus(AW_Eventdiscount_Model_Source_Trigger_Status::MISSED);
                    $item->save();
                    continue;
                }
                $item->setData('html_block', Mage::getBlockSingleton('eventdiscount/timer')->setTimer($item)->toHtml());
                $item->setData('count_down', $item->getActiveToTimestamp() - gmdate('U'));

                $responseTimer[] = $item->toArray();

            }
        } catch (Exception $e) {
            $response->setErrorMessage($e->getMessage());
        }

        $response->setData('timers', $responseTimer);
        $this->getResponse()->setBody($response->toJson());
        return;
    }

    public function ajaxCloseAction()
    {
        $request = $this->getRequest()->get('tid');
        if (empty($request)) {
            $this->getResponse()->setBody(0);
            return;
        }
        $closedTimerId = array();
        $customerId = Mage::getModel('customer/session');
        if (!is_null($customerId->getClosedTimerId())) {
            $closedTimerId = $customerId->getClosedTimerId();
        }
        $closedTimerId[] = $request;
        $closedTimerId = array_unique($closedTimerId);
        $customerId->setClosedTimerId($closedTimerId);
        $this->getResponse()->setBody(1);
        return;
    }

    public function updateDbAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
 CREATE TABLE IF NOT EXISTS `{$setup->getTable('aweventdiscount/product')}` (
              `timer_product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `timer_id` int(10) unsigned NOT NULL,
              `product_id` int(10) unsigned NOT NULL,
              PRIMARY KEY (`timer_product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

		");
        $installer->endSetup();
        echo "success";
    }
}