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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Country collection
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Mysql4_Region_Collection extends Mage_Directory_Model_Resource_Region_Collection
{
	$excludeRegions = array('AK','AS','AF','AA','AC','AE','AM','AP','DC','FM','GU','HI','MH','MP','PW','PR','VI');

foreach ($collection as $region) {

if (!$region->getRegionId()) {

continue;

}

//BOF Custom Logic Here

$regionCode = $region->getCode();

if (in_array($regionCode, $excludeRegions)) {

continue;

}

//EOF Custom Logic Here

$regions[$region->getCountryId()][$region->getRegionId()] = array(

'code' => $region->getCode(),

'name' => $this->__($region->getName())

);

}
}
