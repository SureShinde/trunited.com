<?php

class Magestore_ManageApi_Model_Observer
{
    public function processCron()
    {
        Mage::log('Running API at '.date('Y-m-d H:i:s', time()), null, 'run_api.log');
        /* Run Link Share API */
        Mage::helper('manageapi/linkshare')->processCron();
        /* END Run Link Share API */

        sleep(1);
        /* Run Link Share API */
        Mage::helper('manageapi/hotel')->processCron();
        /* END Run Link Share API */

        sleep(1);
        /* Run Link Share API */
        Mage::helper('manageapi/flight')->processCron();
        /* END Run Link Share API */

        sleep(1);
        /* Run Link Share API */
        Mage::helper('manageapi/car')->processCron();
        /* END Run Link Share API */

        sleep(1);
        /* Run Link Share API */
        Mage::helper('manageapi/vacation')->processCron();
        /* END Run Link Share API */

        sleep(1);
        /* Run CJ API */
        Mage::helper('manageapi/cj')->processCron();
        /* END Run CJ API */

        sleep(1);
        /* Run Target API */
        Mage::helper('manageapi/target')->processCron();
        /* END Run Target API */
    }
}
