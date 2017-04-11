<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 *
 * @package     Skrill
 * @copyright   Copyright (c) 2014 Skrill
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order success observer
 *
 */
class Skrill_Model_Success_Observer
{
    /**
     * Reactivate the cart because the order isn't finished
     *
     * @param Varien_Event_Observer $observer
     */
    public function activateQuote(Varien_Event_Observer $observer)
    {
    	$quote = $observer->getEvent()->getQuote();
        $paymentMethod = $quote->getPayment()->getMethod();

        if (strpos($paymentMethod, 'skrill') !== false) {
        	$quote->setIsActive(true)->save();
        }
    }
}

