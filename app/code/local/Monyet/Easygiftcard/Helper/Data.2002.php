<?php

class Monyet_Easygiftcard_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * attach file to email
     * supported types: pdf
     *
     * @param        $file
     * @param        $mailObj
     *
     * @return mixed
     */
    public function addFileAttachment($_groupProduct, $item, $qty, $mailObj, $order)
    {
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {

            if (!($mailObj instanceof Zend_Mail) && !($mailObj instanceof Mandrill_Message)) {
                $mailObj = $mailObj->getMail();
            }
            if (method_exists($mailObj, 'setType')) {
                $mailObj->setType(Zend_Mime::MULTIPART_MIXED);
            }
			$pdfPath = $_groupProduct->getPdfPath();
			$path = Mage::getBaseDir() . DIRECTORY_SEPARATOR . $pdfPath;

			$files_array = array();
			if (is_dir($path) && $pdfPath) {
				if ($handle = opendir($path)) {
					//Notice the parentheses I added:
					while (($file = readdir($handle)) !== FALSE) {
						$files_array[] = $file;
					}
					closedir($handle);
				}
				$i = 0;
				$files = array();
				foreach ($files_array as $filename) {
					$data = explode('_', $filename);
					$file = "";
					if (count($data) > 1 && $i < $qty) { //Valid file name only
						$prefile = str_replace($_groupProduct->getSku().'-', '', $item->getSku());
						if($prefile == $data[0]){
							$file = $filename;
							$filePath = $path . DIRECTORY_SEPARATOR . $file;
							$soldFilePath = $path . DIRECTORY_SEPARATOR . 'sold' . DIRECTORY_SEPARATOR . $order->getId() . DIRECTORY_SEPARATOR . $item->getProductId() . DIRECTORY_SEPARATOR . $file;
							@mkdir($path . DIRECTORY_SEPARATOR . 'sold');
							@mkdir($path . DIRECTORY_SEPARATOR . 'sold' . DIRECTORY_SEPARATOR . $order->getId());
							@mkdir($path . DIRECTORY_SEPARATOR . 'sold' . DIRECTORY_SEPARATOR . $order->getId() . DIRECTORY_SEPARATOR . $item->getProductId());
							if (file_exists($filePath)) {
								$files[] = $file;
								//Mage::log($filePath);
								/*
								$mailObj->createAttachment(
									file_get_contents($filePath),
									'application/pdf',
									Zend_Mime::DISPOSITION_ATTACHMENT,
									Zend_Mime::ENCODING_BASE64,
									basename($filePath)
								);
								*/
								if (copy($filePath,$soldFilePath)) {
								    unlink($filePath);
									
								}
								$i++;
								$orderTable = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
								$write->query('UPDATE ' . $orderTable . ' SET pdf_sent = 1 WHERE entity_id=?', array($order->getId()));
								
							}
						}
					}
				}
				$i = 1;
				$giftcardPdf = array();
				foreach($files as $file){
					$giftcardPdf[$i] = $file;
					$i++;
				}
				$data = Mage::helper('core')->jsonEncode($giftcardPdf);
				$orderItemTable = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
				$write->query("UPDATE " . $orderItemTable . " SET pdf_report = '".$data."' WHERE item_id=".$item->getId()."");
				
			}
            

        } catch (Exception $e) {
            Mage::log('Caught error while attaching pdf:' . $e->getMessage());
        }
        return $mailObj;
    }
}
