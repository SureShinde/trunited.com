<?php

/**
 * Sales order history block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Monyet_Easygiftcard_Block_History extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('monyet/easygiftcard/history.phtml');
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $orders = Mage::getModel('sales/order_item')->getCollection()
            ->addFieldToSelect('*')
            //->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            //->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;
		$orders->getSelect()->join( array('order'=>  $orders->getTable('sales/order')), 'order.entity_id = main_table.order_id AND main_table.product_type = "grouped" AND order.customer_id = "'.$customerId.'"', array('order.increment_id', 'order.entity_id'));
        $this->setOrders($orders);

    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'easygifcard.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getDownloadFile($_groupProduct, $sku, $qty, $orderId, $productId)
    {
        $pdfPath = $_groupProduct->getPdfPath();
		$path = Mage::getBaseDir() . DIRECTORY_SEPARATOR . $pdfPath . DIRECTORY_SEPARATOR . 'sold' . DIRECTORY_SEPARATOR . $orderId . DIRECTORY_SEPARATOR . $productId;
		$file = array();
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
			foreach ($files_array as $filename) {
				$data = explode('_', $filename);
				if (count($data) > 1 && $i < $qty) { //Valid file name only
					$prefile = str_replace($_groupProduct->getSku().'-', '', $sku);
					if($prefile == $data[0]){
						$file[] = $filename;
					}
				}
			}
			
		}
		if($qty==1) {
			return $file[0];
		} else {
			return $this->createZip($file, 'dpf.zip', $path);
			
		}
    }

    public function createZip($files = array(), $zipfile, $destination = '',$overwrite = false) {
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination . DIRECTORY_SEPARATOR .$zipfile) && !$overwrite) { return false; }
		//vars
		$valid_files = array();
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($destination . DIRECTORY_SEPARATOR .$file)) {
					$valid_files[] = $file;
				}
			}
		}
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination . DIRECTORY_SEPARATOR .$zipfile,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return '';
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($destination . DIRECTORY_SEPARATOR .$file,$file);
			}
			$zip->close();
			
			return $zipfile;
		}
		else
		{
			return '';
		}
	}

    public function getGropuedProduct($id)
    {
        $_groupedParentsId = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($id);
		$_groupProduct = Mage::getModel('catalog/product');
		foreach($_groupedParentsId as $_groupedId) {
			$_groupProduct->load($_groupedId);
			break;
		}
		return $_groupProduct;
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
