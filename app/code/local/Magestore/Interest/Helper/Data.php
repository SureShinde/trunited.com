<?php

class Magestore_Interest_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function import()
    {
        $fileName = $_FILES['csv_store']['tmp_name'];
        $csvObject = new Varien_File_Csv();
        $csvData = $csvObject->getData($fileName);
        $import_count = 0;

        $transactionSave = Mage::getModel('core/resource_transaction');

        try {

            if (sizeof($csvData) > 0) {

                foreach ($csvData as $csv) {
                    if (isset($csv[0]) && isset($csv[1]) && !filter_var($csv[1], FILTER_VALIDATE_INT) === false) {
                        $obj = Mage::getModel('interest/interest');
                        $obj->setTitle($csv[0]);
                        $obj->setSortOrder($csv[1]);
                        $obj->setStatus(Magestore_Interest_Model_Status::STATUS_ENABLED);
                        $obj->setCreatedAt(now());
                        $obj->setUpdatedAt(now());
                        $transactionSave->addObject($obj);
                        $import_count++;
                    }
                }
            }

            $transactionSave->save();
        } catch (Exception $e) {
            zend_debug::dump($e->getMessage());
            exit;
        }
        return $import_count;
    }
	
}
