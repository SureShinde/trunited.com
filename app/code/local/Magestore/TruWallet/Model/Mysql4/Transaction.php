<?php

class Magestore_TruWallet_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('truwallet/transaction', 'transaction_id');
	}

	public function import() {
		$write = $this->_getWriteAdapter();
		$write->beginTransaction();
		$fileName = $_FILES['csv_store']['tmp_name'];
		$csvObject = new Varien_File_Csv();
		$csvData = $csvObject->getData($fileName);

		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$transactionSave = Mage::getModel('core/resource_transaction');
		try {
			$connection->beginTransaction();

			if(sizeof($csvData) > 0)
			{
				$current_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
				$type_data = Magestore_TruWallet_Model_Type::getDataType();
				foreach ($csvData as $csv)
				{
					$amount = str_replace($current_symbol,'',$csv[2]);
					if(isset($csv[0]) && !filter_var($csv[0], FILTER_VALIDATE_INT) === false
						&& 	isset($csv[1]) && !filter_var($csv[1], FILTER_VALIDATE_EMAIL) === false
						&& isset($csv[3]) && !filter_var($csv[3], FILTER_VALIDATE_INT) === false
						&& isset($amount) && !filter_var($amount, FILTER_VALIDATE_FLOAT) === false
						&& in_array($csv[3], $type_data)
					){
						$customer = Mage::getModel('customer/customer')->load($csv[0]);
						if($customer->getId() && strcasecmp($customer->getEmail(), $csv[1]) == 0)
						{
							$obj = Mage::getModel('truwallet/transaction');
							$_data = array(
								'customer_id' => $customer->getId(),
								'customer_email' => $customer->getEmail(),
								'title' => isset($csv[4]) ? $csv[4] : '',
								'action_type' => isset($csv[3]) ? $csv[3] : '',
								'store_id' => Mage::app()->getStore()->getId(),
								'created_time' => Mage::app()->getStore()->getId(),
								'updated_time' => Mage::app()->getStore()->getId(),
								'current_credit' => Mage::app()->getStore()->getId(),
								'changed_credit' => $csv[2],
							);
							$transactionSave->addObject($obj);
						}

					}

				}

			}


			$transactionSave->save();
			$connection->commit();
		} catch (Exception $e) {
			$connection->rollback();
		}
		//	Mage::throwException(Mage::helper('truwallet')->__('Please follow the sample file\'s format to import transactions properly.'));
	}

	public function csvGetArrName($csvData) {
		$array = array();
		foreach ($csvData as $k => $v) {
			if ($k == 0) {
				continue;
			}
			$array[] = $v[0];
		}
		$collections = Mage::getModel('truwallet/transaction')
			->getCollection()
			->addFieldToFilter('name', array('in' => $array))
			->getAllField('name');
		return $collections;
	}
}