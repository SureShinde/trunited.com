<?php

class Magestore_ManageApi_Model_Observer
{
    public function processLinkShareCron()
    {
        Mage::helper('manageapi')->processLinkShareCron();
    }
}
