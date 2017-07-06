<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Adminhtml_RecurringandrentalpaymentsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('recurringandrentalpayments/plans')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Recurring &amp; Subscription Payments'), Mage::helper('adminhtml')->__('Recurring &amp; Subscription Payments'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model  =  Mage::getModel('recurringandrentalpayments/plans')->load($id);
		
		$data_detail  =Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()
				  ->addFieldToFilter('plan_id',$id);
	   
		if (sizeof($model)> 0 || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
		
			Mage::register('recurringandrentalpayments_data', $model);    // here product_plan table data is pass
			Mage::register('recurringandrentalpayments_data_detail',$data_detail);  // here plan details is passed
			
			
			$this->loadLayout();
			$this->_setActiveMenu('recurringandrentalpayments/plans');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Plan Manager'), Mage::helper('adminhtml')->__('Plan Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Plan News'), Mage::helper('adminhtml')->__('Plan News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_recurringandrentalpayments_edit'))
				->_addLeft($this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_recurringandrentalpayments_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('recurringandrentalpayments')->__('Plan does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function newAction() {
		$this->_forward('edit');
	}
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			$data = Mage::helper('recurringandrentalpayments')->getFilter($data);
			
			unset($data['name']);
			$model = Mage::getModel('recurringandrentalpayments/plans');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
				
			try
			{
				if($this->getRequest()->getParam('id'))
				{
					$model->setUpdateTime(now());
				}
				else
				{
					$model->setCreationTime(now());
					$model->setUpdateTime(now());
				}
				if(!isset($data['plan']))
				{
					Mage::getSingleton('adminhtml/session')->addError('Please add term for this plan.');
				 	$this->_redirect('*/*/edit', array('active_tab' => 'term_section','id' => $this->getRequest()->getParam('id')));
				 	return;	
				}
				else
				{
					$ableToDeleteTerm=true;
					foreach($data['plan']['terms'] as $term)
					{
						if($term['delete']==null)
						{
							$ableToDeleteTerm=false;
							break;
						}
					}
					if($ableToDeleteTerm)
					{
						Mage::getSingleton('adminhtml/session')->addError('Plan must have atleast one term.');
						$this->_redirect('*/*/edit', array('active_tab' => 'term_section','id' => $this->getRequest()->getParam('id')));
						return;
					} 
					$col = $data['plan']['terms'];
				}
				// Added on  22_4_2015: For Duplication Plan's terms title   By Jitendra /
				foreach($col as $term)
				{
				 $termArray[] = strtolower($term['label']);
				 //$termArray[] = $term['label'];
				}
				$termsMessage =array();
				foreach(array_count_values($termArray) as $key=>$term)
				{
				 if($term>1)
				 {       
				  $termsMessage[] = $key;
				 }
				}
				if(!empty($termsMessage))
				{
				  throw new Exception(implode(", ",$termsMessage) ." term title is already exists, please use a unique term title.");
				}
				
				$existing_plan = 0 ;
				$plan_id = $model->getId();
				if(isset($plan_id) && $plan_id > 0 && $plan_id != '')
				{
					$existing_plan = 1 ;	
				}
		
		
				// Added on  22_4_2015: For Duplication Plan name   By Jitendra /
				$checkName = Mage::getModel('recurringandrentalpayments/plans')->getCollection()->addFieldToFilter('plan_name',$data['plan_name'])->getFirstItem();
			
				
				if($checkName->getId()>0 && $checkName->getId() != $this->getRequest()->getParam('id'))
				{
				 
				 throw new Exception($data['plan_name']." Plan name already exists, please use a unique plan name.");
				}
				else
				{
				 $model->save(); 
				}
 			 // Added on 22_4_2015: For Duplication Plan name   By Jitendra /
				$model->save();
				if (isset($data['products_area_type']) && $data['products_area_type']>1 && isset($data['products_area'])) {
                    if ($data['in_products']) $productIds = explode(',', $data['products_area']); else $productIds = array();                    
		   	  		  switch ($data['products_area_type']) {
                        case 2: // by product ids
                            $productArea = explode(',', $data['products_area']);
                            foreach($productArea as $productId) {
                                $productId = intval($productId);
                                if ($productId && !in_array($productId, $productIds)) {
                                    $product = Mage::getSingleton('catalog/product')->load($productId);
                                    if ($product && $product->getId() > 0) $productIds[] = $productId;
                                }
                            }
                            $data['in_products'] = implode(',', $productIds);
                            break;
                        case 3: // by SKUs     
                            $productArea = explode(',', $data['products_area_sku']);
							$fetchProduct = Mage::getSingleton('catalog/product');
                            foreach($productArea as $sku) {
								
                                $sku = trim($sku);
								
                                $productId = $fetchProduct->getIdBySku($sku);
                                //if ($productId && !in_array($productId, $productIds))
								$productSku[] = $productId;
                            }
                            $data['in_products'] = implode(',', $productSku);
                            break;
                    }
                }

	/* Start : 2014-09-23 : Here all selected product's id are saved in table with respective plan id */		
			
				$total_products = explode(",", $data['in_products']);

				$group_product_id = array();
			/* This will check if product is of group type 
			   Then ids of its associated products are also needs to add  */
				foreach ($total_products as $product_id)
				{
					$product = Mage::getModel('catalog/product')->load($product_id); 
					$productType = $product->getTypeID();	
					
					if ($productType == "grouped")
					{
						$group_product = Mage::getModel('catalog/product')->load($product_id);  
        				$associated_products = $group_product->getTypeInstance(true)->getAssociatedProducts($group_product);
						foreach ($associated_products as $child_product)
						{
							$child_product_id[] = $child_product->getId();
						}
						$total_products = array_merge($total_products,$child_product_id);
					}
				}
				
				$count = sizeof($total_products);
				$plan_id = $model->getId();
				if($data['products_area_type']==2  || $data['products_area_type']==3)
				{
					if($existing_plan == 1)   // already exist
					{
						$collection =  Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()
								->addFieldToFilter('plan_id',$plan_id)
								->addFieldToSelect('product_id')
								->getData();                     // create an array of this plan's all product id
						$collectId = array();		
						foreach ($collection as $collect)
						{
							$collectId[] = $collect['product_id'];	
						}
						
						$array2 = array_diff($total_products,$collectId);	   // find which new products are already in this plan
							if(sizeof($array2))
							{
							$isAlreadyExist = Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()   // find new products in whole table
											->addFieldToFilter('product_id',array('in' => array($array2)));
							
							}
							else
							{
								$isAlreadyExist = 0;    // when change is not related to prodct bt related to plan name,disc amt,term dt..etc
								
							}
					}
					else   // Check for new add plan has already exist product or not
					{
						$isAlreadyExist = Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()
								 ->addFieldToFilter('product_id',array('in' => array($total_products)));
					}
					
				if(count($isAlreadyExist) == 0 || $isAlreadyExist == 0)
				{
					//save plan because all selected products are new
					for($i=0; $i<$count; $i++)
					{
						
						$productofPlans = array('plan_id' => $plan_id, 'product_id' => $total_products[$i]);
						$productPlanModel = Mage::getModel('recurringandrentalpayments/plans_product')->setData($productofPlans);
						
						try {
							$productPlanModel->save();
						} catch (Exception $e) {
							Mage::log("Exception". $e);
						}
					}
		/* End : 2014-09-23 : Here all selected product's id are saved in table with respective plan id */
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('recurringandrentalpayments')->__('Plan was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);
				}
				else
				{
						// give error message	
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('recurringandrentalpayments')->__('You cannot add same product, which is already added in other plan.'));

				}
			}
			else
			{					
				//save plan because all selected products are new
					for($i=0; $i<$count; $i++)
					{
						$productofPlans = array('plan_id' => $plan_id, 'product_id' => $total_products[$i]);
						$productPlanModel = Mage::getModel('recurringandrentalpayments/plans_product')->setData($productofPlans);
						
						try {
							$productPlanModel->save();
						} catch (Exception $e) {
							Mage::log("Exception". $e);
						}
					}
		/* End : 2014-09-23 : Here all selected product's id are saved in table with respective plan id */
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('recurringandrentalpayments')->__('Plan was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);
			}
				//$deleteTemrIds=array();
				//$ableToDeleteTerm=false;
				
				foreach($col as $c)
				{
						$model_terms = Mage::getModel('recurringandrentalpayments/terms');	
						
						if(isset($c['delete']) && $c['delete'])
						{
							$model_terms->setId($c['id']);
							$model_terms->delete();
						}
						else
						{
							if(isset($c['id']) && $c['id']) $model_terms->setId($c['id']);
							/* bh change 19-2-15 */
							$paymentbeforedays=0;
							if(isset($c['paymentbeforedays']))
							$paymentbeforedays=$c['paymentbeforedays'];
							$model_terms->setLabel($c['label']);
							$model_terms->setRepeateach($c['repeateach']);
							$model_terms->setTermsper($c['termsper']);
							$model_terms->setPaymentBeforeDays($paymentbeforedays);
							$model_terms->setPrice($c['price']);
							$model_terms->setPriceCalculationType($c['pricecalculationtype']);
							$model_terms->setNoofterms($c['noofterms']);
							$model_terms->setSortorder($c['sortorder']);
							$model_terms->setPlanId($model->getId());
							$model_terms->save();
						}
				}
				$this->deleteProduct($total_products,$plan_id);
				/* end */
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('_current' => true,'id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('_current' => true,'id' => $this->getRequest()->getParam('id')));
                return;
            }
		}
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('recurringandrentalpayments')->__('Unable to find Plan to save'));
        $this->_redirect('*/*/');
	}
	
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('recurringandrentalpayments/plans');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				$model_terms = Mage::getModel('recurringandrentalpayments/terms')->getCollection()
				->addFieldToFilter('plan_id',$this->getRequest()->getParam('id'));
				foreach($model_terms as $col)
				{
					$col->delete();
				}
				
				$model_plans_product = 	Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()
				->addFieldToFilter('plan_id',$this->getRequest()->getParam('id'));
				foreach($model_plans_product as $col)
				{
					$col->delete();
				} 
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Plan was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $recurringandrentalpaymentsIds = $this->getRequest()->getParam('recurringandrentalpayments');
        if(!is_array($recurringandrentalpaymentsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select plan(s)'));
        } else {
            try {
                foreach ($recurringandrentalpaymentsIds as $recurringandrentalpaymentsId) {
                    $recurringandrentalpayments = Mage::getModel('recurringandrentalpayments/plans')->load($recurringandrentalpaymentsId);
                    $recurringandrentalpayments->delete();
					
					$model_terms = Mage::getModel('recurringandrentalpayments/terms')->getCollection()
				->addFieldToFilter('plan_id',$recurringandrentalpaymentsId);
				foreach($model_terms as $col)
				{
					$col->delete();
				}
			
				
				$plan_product = Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()
				  ->addFieldToFilter('plan_id',$recurringandrentalpaymentsId);
	   			foreach($plan_product as $product)
				{
					$product->delete();
				}
					
			
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d plan(s) were successfully deleted', count($recurringandrentalpaymentsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $recurringandrentalpaymentsIds = $this->getRequest()->getParam('recurringandrentalpayments');
        if(!is_array($recurringandrentalpaymentsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Plan(s)'));
        } else {
            try {
                foreach ($recurringandrentalpaymentsIds as $recurringandrentalpaymentsId) {
                    $recurringandrentalpayments = Mage::getSingleton('recurringandrentalpayments/plans')
                        ->load($recurringandrentalpaymentsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($recurringandrentalpaymentsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'recurringandrentalpayments.csv';
        $content    = $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_recurringandrentalpayments_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'recurringandrentalpayments.xml';
        $content    = $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_recurringandrentalpayments_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	public function gridAction()
    {
		$this->_redirect('*/*/edit',array('active_tab' => 'Product_section'));
	}
	 public function productGridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_recurringandrentalpayments_edit_tab_grid')->toHtml()
        );
    }
	public function deleteProduct($selected_products,$plan_id)
	{
		
		$collection =  Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()
						->addFieldToFilter('plan_id',$plan_id)
						->addFieldToSelect('product_id')
						->getData();                     // fetch product id of plan_id

		$collectId = array();		
		foreach ($collection as $collect)
		{
			$collectId[] = $collect['product_id'];	//$collect->getProductId();//
		}
		
		$idsToDelete = array_diff($collectId,$selected_products);	   // find which new products are already in this plan
		
		if(sizeof($idsToDelete))
		{
			$items = Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()   // find new products in whole table
							->addFieldToFilter('product_id',array('in' => array($idsToDelete)));
			
			foreach($items as $item)
			{
				$item->delete();
			}
			
		}
	}
	protected function _isAllowed()
    {
      return true; 
    }
}