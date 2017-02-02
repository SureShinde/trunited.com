<?php
/**
 * ImportOrders.php
 * CommerceThemes @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commercethemes.com/LICENSE-M1.txt
 *
 * @category   Orders
 * @package    Updateorders
 * @copyright  Copyright (c) 2003-2009 CommerceThemes @ InterSEC Solutions LLC. (http://www.commercethemes.com)
 * @license    http://www.commercethemes.com/LICENSE-M1.txt
 */ 

class CommerceExtensions_Orderimportexport_Model_Convert_Adapter_Updateorders
    extends Mage_Catalog_Model_Convert_Adapter_Product
{
		/**
     * Retrieve order create model
     *
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */

    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Retrieve session object
     *
     * @return  Mage_Adminhtml_Model_Session_Quote
     */

    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Initialize order creation session data
     *
     * @param   array $data
     * @return  Mage_Adminhtml_Sales_Order_CreateController
     */

    protected function _initSession($data)
    {
        /**
         * Identify customer
         */
        if (!empty($data['customer_id'])) {
            $this->_getSession()->setCustomerId((int) $data['customer_id']);
        }
        /**
         * Identify store
         */
        if (!empty($data['store_id'])) {
            $this->_getSession()->setStoreId((int) $data['store_id']);
        }
        return $this;
    }

    /**
     * Processing quote data
     *
     * @param   array $data
     * @return  Yournamespace_Yourmodule_IndexController
     */

    protected function _processQuote($data = array())
    {
        /**
         * Saving order data
         */

        if (!empty($data['order'])) {
            $this->_getOrderCreateModel()->importPostData($data['order']);
        }

        /**
         * init first billing address, need for virtual products
         */
        $this->_getOrderCreateModel()->getBillingAddress();
        $this->_getOrderCreateModel()->setShippingAsBilling(true);
        /**
         * Adding products to quote from special grid and
         */

        if (!empty($data['add_products'])) {
            $this->_getOrderCreateModel()->addProducts($data['add_products']);
        }

        /**
         * Collecting shipping rates
         */
        $this->_getOrderCreateModel()->collectShippingRates();

        /**
         * Adding payment data
         */

        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }
		
        $this->_getOrderCreateModel()
             ->initRuleData()
             ->saveQuote();

        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }
        return $this;

    }


	public function _getStoreById($storeId)

   	 {

        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true);
        }

        if (isset($this->_stores[$storeId])) {
            return $this->_stores[$storeId];
        }

        return false;

    }

    /**
     * Import Orders model
     *
     * @var Mage_Sales_Model_Convert_Adapter
     */

    	public function saveRow( array $importData )

		{

				if ($importData['order_id'] != "") {
				
					$resource = Mage::getSingleton('core/resource');
					$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
					$write = $resource->getConnection('core_write');
					$read = $resource->getConnection('core_read');
					
					$order = Mage::getModel('sales/order')->loadByIncrementId($importData['order_id']);
					$entity_id = $order->getId();
					$store_id = $order->getStoreId();
					
					if($importData['order_status']=="complete") {
							
						
						try {

							$itemQty = array();
							//set updated_at in sales_flat_order_item and update qty_shipped with whatever qty_ordered was
							$select_qry_flat_order_item = "SELECT item_id, qty_ordered FROM `".$prefix."sales_flat_order_item` WHERE order_id = '".$entity_id."'";
							$flat_order_item_rows = $read->fetchAll($select_qry_flat_order_item);
	
							foreach($flat_order_item_rows as $flat_order_item_data)
							{ 
								
								$item_id = $flat_order_item_data['item_id'];
								$itemQty[$item_id] = $flat_order_item_data['qty_ordered'];
							}	

							#$order = Mage::getModel('sales/order')->load($entity_id);
							if (!$order->hasShipments()) {
								
								try {
								
								#$shipment = $order->prepareShipment($itemQty);
								$shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($itemQty);
	 
				
								$shipmentCarrierCode = 'custom';
								$shipmentCarrierTitle = $importData['ship_service'];
								$shipmentTrackingNumber = $importData['tracking_id'];
								
								$arrTracking = array(
									'carrier_code' => isset($shipmentCarrierCode) ? $shipmentCarrierCode : $order->getShippingCarrier()->getCarrierCode(),
									'title' => isset($shipmentCarrierTitle) ? $shipmentCarrierTitle : $order->getShippingCarrier()->getConfigData('title'),
									'number' => $shipmentTrackingNumber,
								);
								
								$track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
								$shipment->addTrack($track);
								$shipment->register();
								
								$shipment->getOrder()->setIsInProcess(true);
								$transactionSave = Mage::getModel('core/resource_transaction')
									->addObject($shipment)
									->addObject($order)
									->save();
								
								/*	
								if($shipment){
									if(!$shipment->getEmailSent()){
										$shipment->sendEmail(true);
										$shipment->setEmailSent(true);
										$shipment->save();                          
									}
								}  
								*/
								#$sales_flat_shipment_item_insertID = $shipment->getId();
								
								} catch (Exception $e){
									echo "ERROR: " . $e->getMessage();
									Mage::log(sprintf('Order Update error: %s', $e->getMessage()), Zend_Log::ERR);
								}
							}
							

						} catch (Exception $e){
							echo "ERROR: " . $e->getMessage();
							Mage::log(sprintf('Order Update error: %s', $e->getMessage()), Zend_Log::ERR);
						}
						
						//set updated_at and status in sales_flat_order / sales_flat_order_grid
						if($importData['order_status']=="complete" || $importData['order_status']=="Complete") {
							
							try {
								$write_qry2 = $write->query("UPDATE `".$prefix."sales_flat_order` SET state = 'complete', status = 'complete' WHERE entity_id = '". $entity_id ."' AND store_id = '". $store_id ."'");
								
								$write_qry3 = $write->query("UPDATE `".$prefix."sales_flat_order_grid` SET status = 'complete' WHERE entity_id = '". $entity_id ."' AND store_id = '". $store_id ."'");
								
								$write_qry4 = $write->query("UPDATE `".$prefix."sales_flat_order_status_history` SET status = 'complete' WHERE parent_id = '". $entity_id ."'");
								
							} catch (Exception $e){
								echo "ERROR: " . $e->getMessage();
								Mage::log(sprintf('Order Update error: %s', $e->getMessage()), Zend_Log::ERR);
							}
						}
						
						Mage::log('Order Update Successfull', Zend_Log::INFO);

				} else if($importData['order_status']=="canceled" || $importData['order_status']=="closed") {
				
						if($importData['order_status']=="canceled") {
							#if ($order->canCancel()) {
								try {
									#$order->cancel();
									$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
									// remove status history set in _setState
									#$order->getStatusHistoryCollection(true);
									$order->save();
								} catch (Exception $e) {
									Mage::logException($e);
								}
							#}
						}
						if($importData['order_status']=="closed") {
							
							try {
								$write_qry2 = $write->query("UPDATE `".$prefix."sales_flat_order` SET state = 'closed', status = 'closed' WHERE entity_id = '". $entity_id ."' AND store_id = '". $store_id ."'");
								
								$write_qry3 = $write->query("UPDATE `".$prefix."sales_flat_order_grid` SET status = 'closed' WHERE entity_id = '". $entity_id ."' AND store_id = '". $store_id ."'");
								
								$write_qry4 = $write->query("UPDATE `".$prefix."sales_flat_order_status_history` SET status = 'closed' WHERE parent_id = '". $entity_id ."'");
								
							} catch (Exception $e){
								echo "ERROR: " . $e->getMessage();
								Mage::log(sprintf('Order Update error: %s', $e->getMessage()), Zend_Log::ERR);
							}
						}
			   }
		 }
	}
}