<?php

/**
 */
class Monyet_Easygiftcard_Adminhtml_EasygiftcardController extends Mage_Adminhtml_Controller_Action
{
    protected $_associated = array();
    protected $_existassociated = array();

    public function updateAction()
    {
        $groupedId = $this->getRequest()->getParam('id');
        $groupedProduct = Mage::getModel('catalog/product')->load($groupedId);
		$savedIds = $this->_getAssociatedProductIds($groupedId);
		foreach($savedIds as $id) {
			$this->_existassociated[$id] = $id;
		}
        $pdfPath = $groupedProduct->getPdfPath();
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
			//Output findings
			$giftcards = array();
			$totals = array();
			$actualFiles = array();
			foreach ($files_array as $filename) {
				$data = explode('_', $filename);
				if (count($data) > 1) { //Valid file name only
					$totals[] = $data[0];
				}
			}
			$giftcards = array_count_values($totals);

			$associated = array();
			foreach ($giftcards as $price=>$qty) {
				$this->_updateChildProduct($groupedProduct, $price, $qty);
			}
			//Add to grouped product
			$products_links = Mage::getModel('catalog/product_link_api');

			foreach ($this->_associated as $id) {
				$products_links->assign("grouped", $groupedId, $id);
			}
			//Set qty = 0 for out of stock item
			foreach($this->_existassociated as $id) {
				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
				if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {
					$stockItem->setQty(0);
					$stockItem->setIsInStock(0);
					try {
						$stockItem->save();
					} catch (Exception $ex) {
						echo $ex->getMessage();
					}
				}
				
			}
			$this->_getSession()->addSuccess($this->__('Inventory has been updated.'));

			$this->_redirect('adminhtml/catalog_product/edit', array(
				'id'    => $groupedId,
				'_current'=>true
			));
        } else {
			$this->_getSession()->addError($this->__('Please specify correct PDF path.'));
			$this->_redirect('adminhtml/catalog_product/edit', array(
				'id'    => $groupedId,
				'_current'=>true
			));
		}
        

    }

    protected function _updateChildProduct($parentProduct, $price, $qty)
    {
        try {
            $productType = 'virtual';
			$point = 0;
			if(is_numeric($parentProduct->getRwPointPercent()))
				$point = round($price * $parentProduct->getRwPointPercent()/100);
            $product = Mage::getModel('catalog/product');
            $product->setSku($parentProduct->getSku() . '-' . $price)//sku
            ->setName($parentProduct->getName() . ' - $' . $price)//name
            ->setUrlKey($parentProduct->getUrlKey() . '-' . $price)//url_key
            ->setTypeId($productType)//typeId
            ->setWebsiteIDs(array(1))
                ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)//
                ->setAttributeSetId($parentProduct->getAttributeSetId())
                ->setDescription($parentProduct->getDescription())
                ->setShortDescription($parentProduct->getShortDescription())
                ->setPrice($price)
                ->setRewardpointsEarn($point)
                ->setTaxClassId(0);

            $product->save();
			if(isset($this->_existassociated[$product->getId()])) unset($this->_existassociated[$product->getId()]);
            $this->_associated[$product->getId()] = $product->getId();
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $stockTable = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
            $write->query('delete from ' . $stockTable . ' where product_id=? ', array($product->getId()));
            $product->setStockData(array(
                'use_config_manage_stock' => 1, // use global config?
                'manage_stock' => 1, // should we manage stock or not?
                'is_in_stock' => 1,
                'qty' => $qty,
            ));
            try {
                $product->save();
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
            $this->_updateGalleryProduct($parentProduct->getId(), $product->getId());
            unset($product);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    protected function _updateGalleryProduct($parentId, $productId)
    {
        $galleryTable = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery');
        $eavAttrTable = Mage::getSingleton('core/resource')->getTableName('eav_attribute');
        $productTable = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $baseImageAttr = $read->fetchAll('select attribute_id from ' . $eavAttrTable . ' where entity_type_id=4 and attribute_code in ("thumbnail","image","small_image")');

        $gallery = $read->fetchAll('select * from ' . $galleryTable . ' where entity_id=?', array($parentId));
        if (count($gallery)) {
            $write->query('delete from ' . $galleryTable . ' where entity_id=?', array($productId));
            $k = 1;
            foreach ($gallery as $image) {
                if ($k == 1) {
                    $baseImage = $image;
                }
                $write->query('insert into ' . $galleryTable . '(attribute_id, entity_id, value) values(?,?,?)', array($image['attribute_id'], $productId, $image['value']));
                $k++;
            }
            foreach ($baseImageAttr as $attr) {
                $write->query('delete from ' . $productTable . ' where entity_id=? and attribute_id=?', array($productId, $attr['attribute_id']));
                $write->query('insert into ' . $productTable . '(entity_type_id, store_id, attribute_id, entity_id, value) values(?,?,?,?,?)', array(4, 0, $attr['attribute_id'], $productId, $image['value']));
            }
        }
        unset($galleryTable);
        unset($eavAttrTable);
        unset($productTable);
        unset($baseImageAttr);
        unset($gallery);
    }
	
	protected function _getAssociatedProductIds($groupedProductId)
	{
		$coreResource = Mage::getSingleton('core/resource');
		$conn = $coreResource->getConnection('core_read');
		$select = $conn->select()
			->from($coreResource->getTableName('catalog/product_relation'), array('child_id'))
			->where('parent_id = ?', $groupedProductId);

		return $conn->fetchCol($select);
	}
	
	public function sendstockAction()
	{
		try {
            Mage::getModel('productalert/observer')->process();
			$this->_getSession()->addSuccess($this->__('Stock Email Notification has been sent'));
			
		} catch (Exception $ex) {
			$this->_getSession()->addError($ex->getMessage());
		}
		$this->_redirect('adminhtml/dashboard/');
	}
}