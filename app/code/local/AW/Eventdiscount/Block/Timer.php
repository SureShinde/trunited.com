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

class AW_Eventdiscount_Block_Timer extends Mage_Core_Block_Template
{
    public function _construct()
    {
        $this->setTemplate('aw_eventdiscount/timer.phtml');
    }

    public function setTimer($item)
    {
        $this->addData($item->toArray());
        return $this;
    }

    public function getNewWindow()
    {
        if ($this->getUrlType()) {
            return 'target="_blank"';
        }
        return '';
    }
}