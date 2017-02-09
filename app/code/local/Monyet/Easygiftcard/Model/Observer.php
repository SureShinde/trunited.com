<?php
/**
 *Easy Gift Card Extension
 *
 * PHP versions 4 and 5
 *

 *

 */

class Monyet_Easygiftcard_Model_Observer
{	
	/**
     * listen to order email send event to attach pdfs and agreements
     *
     * @param $observer
     */
    public function beforeSendOrder($observer)
    {
		//Mage::log('send order');
        $mailTemplate = $observer->getEvent()->getTemplate();
        $order = $observer->getEvent()->getObject();
        $storeId = $order->getStoreId();
		if(!$order->getPdfSent()) {
			foreach ($order->getAllItems() as $item) {
				$_groupedParentsId = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($item->getProductId());
				foreach($_groupedParentsId as $_groupedId) {
					$_groupProduct = Mage::getModel('catalog/product')->load($_groupedId);
					Mage::helper('monyet_easygiftcard')->addFileAttachment($_groupProduct, $item->getSku(), $item->getQtyOrdered(), $mailTemplate, $order);
					break;
				}
			}
		}

    }
	
    protected function _processSendQueueEvent($mailer, $message)
    {
        if ($message->getEntityType() == 'order' && !$message->getProcessedAt()) {
            $order = Mage::getModel('sales/order')->load($message->getEntityId());
            $storeId = $order->getStoreId();    
			if(!$order->getPdfSent()) {
				Mage::log('send queue');
				foreach ($order->getAllItems() as $item) {
					$_groupedParentsId = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($item->getProductId());
					foreach($_groupedParentsId as $_groupedId) {
						$_groupProduct = Mage::getModel('catalog/product')->load($_groupedId);
						Mage::helper('monyet_easygiftcard')->addFileAttachment($_groupProduct, $item->getSku(), $item->getQtyOrdered(), $mailer, $order);
						break;
					}
				}
			}
        }
    }
	/**
     * listen to order email send event from queue to attach pdfs and agreements
     *
     * @param $observer
     */
    public function beforeSendQueuedOrder($observer)
    {
		//Mage::log('send queue');
        $mailer = $observer->getEvent()->getMailer()
            ? $observer->getEvent()->getMailer()
            : $observer->getEvent()->getMail();
        $message = $observer->getEvent()->getMessage();

        $this->_processSendQueueEvent($mailer, $message);
    }
    /**
     * Add a new button next to the existing "Save and Continue Edit" button
     *
     * @return void
     */
    public function addButton()
    {
		$product = Mage::registry('product');
		if($product->getTypeId() =='grouped') {
			// Retrieve layout
			$layout = Mage::app()->getLayout();

			// Retrieve product_edit block
			$productEditBlock = $layout->getBlock('product_edit');

			// Retrieve original "Save and Continue Edit" button
			$saveAndContinueButton = $productEditBlock->getChild('save_and_edit_button');
			
			// Create new button
			$myButton = $layout->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('monyet_easygiftcard')->__('Update Inventory'),
					'onclick'   => 'setLocation(\'' . $this->getButtonUrl() . '\')',
					'class'  => 'save'
				));

			// Create a container that will gather existing "Save and Continue Edit" button and the new button
			$container = $layout->createBlock('core/text_list', 'button_container');

			// Append existing "Save and Continue Edit" button and the new button to the container
			$container->append($saveAndContinueButton);
			$container->append($myButton);

			// Replace the existing "Save and Continue Edit" button with our container
			$productEditBlock->setChild('save_and_edit_button', $container);
		}
    }

    /**
     * Retrieve the URL for button click
     *
     * @return string
     */
    public function getButtonUrl()
    {
        // The URL called fits to the controller of our module: Herve_ProductEditButton_Adminhtml_ButtonController
        return Mage::getModel('adminhtml/url')->getUrl('easygiftcard_admin/adminhtml_easygiftcard/update', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }
	
}
