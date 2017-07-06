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

class AW_Eventdiscount_Model_Type
{
    public function toOptionArray()
    {
        return array(
            array('value'=> 'info', 'label'=>Mage::helper('eventdiscount')->__('info')),
            array('value'=> 'warning', 'label'=>Mage::helper('eventdiscount')->__('warning')),
            array('value'=> 'danger', 'label'=>Mage::helper('eventdiscount')->__('danger')),
            array('value'=> 'success', 'label'=>Mage::helper('eventdiscount')->__('success')),
        );
    }
}