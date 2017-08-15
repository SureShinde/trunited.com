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
        $cookie = Mage::getModel('core/cookie');
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
            $items = $quote->getAllItems();

//            echo $triggerCollection->getSelect();

            if (!$triggerCollection->getSize()) throw new Exception($this->__('Trigger not found for this customer'));

            foreach ($triggerCollection as $item) {

                $timer = Mage::getModel('aweventdiscount/timer')->load($item->getData('timer_id'));
                if(!$timer->getId() || ($timer->getId() && strcasecmp($timer->getEvent(), AW_Eventdiscount_Model_Event::PROMOTION) == 0 &&
                        !Mage::helper('eventdiscount')->checkShowPromotion($customerId)))
                    continue;

                if (($item->getData('trigger_event') == AW_Eventdiscount_Model_Event::CARTUPDATE)
                    && (!$quote->hasItems() || !Mage::helper('eventdiscount')->checkProductCondition($item->getData('timer_id'), $items))
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
            $response->setError(1);
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

    public function updateDb2Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE `{$setup->getTable('aweventdiscount/timer')}` ADD COLUMN `point_type` TINYINT;
            ALTER TABLE `{$setup->getTable('aweventdiscount/timer')}` ADD COLUMN `point_amount` FLOAT;

		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb3Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE `{$setup->getTable('aweventdiscount/timer')}` ADD COLUMN `text_promotion` VARCHAR(255) ;

		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb4Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE `{$setup->getTable('aweventdiscount/trigger')}` ADD COLUMN `cookie` TINYINT DEFAULT 0;
            ALTER TABLE `{$setup->getTable('aweventdiscount/trigger')}` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ;
            ALTER TABLE `{$setup->getTable('aweventdiscount/trigger')}` ADD COLUMN `referrer_id` int(10) unsigned NULL ;
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb5Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            CREATE TABLE IF NOT EXISTS `{$setup->getTable('aweventdiscount/giftcard')}` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `timer_id` int(10) unsigned NOT NULL,
              `amount_from` FLOAT unsigned NOT NULL,
              `amount_to` FLOAT unsigned NOT NULL,
              `reward_new_customer` FLOAT unsigned NOT NULL,
              `reward_referrer` FLOAT unsigned NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb6Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            DROP TABLE `{$setup->getTable('aweventdiscount/trigger')}`;
            CREATE TABLE IF NOT EXISTS `{$setup->getTable('aweventdiscount/trigger')}` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `timer_id` int(10) unsigned NOT NULL,
              `customer_id` int(10) unsigned NOT NULL,
              `created_at`  TIMESTAMP NULL DEFAULT NULL,
              `duration` bigint(20) NOT NULL COMMENT 'in second',
              `active_to` TIMESTAMP NULL DEFAULT NULL,
              `trigger_status` enum('in_progress','missed','used') NOT NULL DEFAULT 'in_progress',
              `action` text NOT NULL,
              `quote_hash` text NOT NULL,
              `amount_serialized`  text NOT NULL,
              `trigger_event` tinytext NOT NULL,
              `cookie` TINYINT DEFAULT 0,
              `updated_at` TIMESTAMP NULL DEFAULT NULL,
              `referrer_id` int(10) unsigned NULL,
              PRIMARY KEY (`id`),
              KEY `status` (`trigger_status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb7Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE `{$setup->getTable('aweventdiscount/timer')}` ADD COLUMN `cms_page` VARCHAR(255) ;
            ALTER TABLE `{$setup->getTable('aweventdiscount/timer')}` ADD COLUMN `english_cms_page` VARCHAR(255) ;
            ALTER TABLE `{$setup->getTable('aweventdiscount/timer')}` ADD COLUMN `spanish_cms_page` VARCHAR(255) ;
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb8Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE `{$setup->getTable('aweventdiscount/action')}` ADD COLUMN `subtotal_from` FLOAT unsigned NOT NULL;
            ALTER TABLE `{$setup->getTable('aweventdiscount/action')}` ADD COLUMN `subtotal_to` FLOAT unsigned NOT NULL;
            ALTER TABLE `{$setup->getTable('aweventdiscount/timer')}` ADD COLUMN `amount_text` VARCHAR(255);
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb9Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE `{$setup->getTable('aweventdiscount/timer')}` ADD COLUMN `is_end_month` smallint(5) NULL DEFAULT 0;
		");
        $installer->endSetup();
        echo "success";
    }
}