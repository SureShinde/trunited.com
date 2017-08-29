<?php

class Magestore_TruWallet_Helper_Db extends Mage_Core_Helper_Abstract
{
    public function getCoreResource()
    {
        return Mage::getSingleton('core/resource');
    }

    public function getCronTableData()
    {
        $resource = $this->getCoreResource();
        /**
         * Retrieve the read connection
         */
        $readConnection = $resource->getConnection('core_read');

        $query = 'SELECT * FROM ' . $resource->getTableName('cron/schedule').' ORDER BY schedule_id DESC';

        /**
         * Execute the query and store the results in $results
         */
        $results = $readConnection->fetchAll($query);

        /**
         * Print out the results
         */
        zend_debug::dump($results);
    }
}