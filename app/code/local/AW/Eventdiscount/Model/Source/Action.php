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

class AW_Eventdiscount_Model_Source_Action extends AW_Eventdiscount_Model_Source_Abstract
{
    const FIXED='fixed';
    const PERCENT='percent';
    const CHANGE_GROUP='change_group';

    const FIXED_LABEL='Fixed discount';
    const PERCENT_LABEL='Percent discount';
    const CHANGE_GROUP_LABEL='Change customer group to';

    const AWARD_POINT_FIXED = 1;
    const AWARD_POINT_PERCENT = 2;

    public static function toOptionArray()
    {
        $helper = Mage::helper('eventdiscount');
        return array(
            self::FIXED        => $helper->__(self::FIXED_LABEL),
            self::PERCENT      => $helper->__(self::PERCENT_LABEL),
            self::CHANGE_GROUP => $helper->__(self::CHANGE_GROUP_LABEL)
        );
    }
}