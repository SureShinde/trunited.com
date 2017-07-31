<?php

class Magestore_ManageApi_Model_Observer
{
    public function processCron()
    {
		if(date('H', time()) == 1 && date('i', time()) == 00)
		{
			Mage::log('Running API at '.date('Y-m-d H:i:s', time()), null, 'run_api.log');
		
			/* Run Link Share API */
			Mage::helper('manageapi/linkshare')->processCron();
			/* END Run Link Share API */

			/* Run Link Share API */
			Mage::helper('manageapi/hotel')->processCron();
			/* END Run Link Share API */

			/* Run Link Share API */
			Mage::helper('manageapi/flight')->processCron();
			/* END Run Link Share API */

			/* Run Link Share API */
			Mage::helper('manageapi/car')->processCron();
			/* END Run Link Share API */

			/* Run Link Share API */
			Mage::helper('manageapi/vacation')->processCron();
			/* END Run Link Share API */

			/* Run CJ API */
			Mage::helper('manageapi/cj')->processCron();
			/* END Run CJ API */

			/* Run Target API */
			Mage::helper('manageapi/target')->processCron();
			/* END Run Target API */
		}
    }
}
