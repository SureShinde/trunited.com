<?php
require_once Mage::getBaseDir('lib') . '/Twilio/autoload.php';
use Twilio\Rest\Client;

class Magestore_Custompromotions_Helper_Sms extends Mage_Core_Helper_Abstract
{
    public function import()
    {
        $fileName = $_FILES['csv_store']['tmp_name'];
        $csvObject = new Varien_File_Csv();
        $csvData = $csvObject->getData($fileName);
        $import_count = 0;
        $verify_helper = Mage::helper('custompromotions/verify');
        $mobile_prefix = $verify_helper->getMobileCode();
        $transactionSave = Mage::getModel('core/resource_transaction');

        if (sizeof($csvData) > 0) {
            $line = 0;
            foreach ($csvData as $csv) {
                if ($line > 0) {
                    $phone = $verify_helper->getPhoneNumberFormat($mobile_prefix, $csv[1]);
                    /* sending code to customer */
                    $sid = $verify_helper->getAccountSID();
                    $token = $verify_helper->getAuthToken();
                    $from = $csv[0];
                    $message = $csv[2] . " \n\n Text STOP to quit ";
                    try {
                        $client = new Client($sid, $token);
                        $client->messages->create(
                            $phone,
                            array(
                                'from' => $from,
                                'body' => $message
                            )
                        );
                        $import_count++;
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError(
                            Mage::helper('custompromotions')->__('Line %s: The phone number - %s with %s', $line, $csv[1], $e->getMessage())
                        );
                    }
                    /* end sending sms */
                }
                $line++;
            }
        }
        $transactionSave->save();

        return $import_count;
    }
}
