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

class Indies_Recurringandrentalpayments_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
   	{	
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function termpriceAction()
    {
		 $data=Mage::app()->getRequest()->getPost();
		 try
		 {
		 	$id = $data['termid'];
		 	$update = Mage::getModel('recurringandrentalpayments/terms')->load($id);
		 	echo $update->getPrice();
		 }catch(Exception $e){Mage::log($e);}
	}
	public function termdiscriptionAction()
    {
		 $symbole=Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		 $data=Mage::app()->getRequest()->getPost();
		 
		 try
		 {
		 	$string = '';
			if( $data['termid'] > -1){
			$id = $data['termid'];
		 	$update = Mage::getModel('recurringandrentalpayments/terms')->load($id);
			$price = $update->getPrice();

			if(@$data['productPrice'] && $update->getPriceCalculationType() == 1)   // Term price calculatioin is percentage
			{
				$price = $data['productPrice'] * $update->getPrice() / 100 ;	
			}
		 	$string.= 'Name = '.$update->getLabel().' , ';
			$string.= 'Repeat Each = '.$update->getRepeateach().' '.$update->getTermsper().' , ';
			$string.= 'NoOfTerms = '.$update->getNoofterms().' , '; 
			$string.= 'Price per Term = '.$symbole.$price;
			}
			echo $string;
		 }catch(Exception $e){Mage::log($e);}
	}
	public function helptooltipAction()
	{
		 $help_tooltip = '';
		 $data=Mage::app()->getRequest()->getPost();
 		 $product = Mage::registry('product');
		 
		 try
		 {
			if( $data['termid'] > -1)
			{
				$id = $data['termid'];
				$update = Mage::getModel('recurringandrentalpayments/terms')->load($id);
				$price = $update->getPrice();
				$symbole=Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();



				if(@$data['productPrice'] && $update->getPriceCalculationType() == 1)   // Term price calculatioin is percentage
				{
					$price = $data['productPrice'] * $update->getPrice() / 100 ;	
				}
				if ($data['firstperiodprice'] > 0 )
				{
					$help_tooltip .= '<p>Initial price to start the subscription of this product will be '.$data['firstperiodprice'].'
After that Your subscription of this product for the term '.$update->getLabel().' will repeat ';
				}
				else
				{
					$help_tooltip .= '<p>Your subscription of this product for the term '.$update->getLabel().' will repeat ';
				}
				$repeat_each = 	$update->getTermsper() ;
				if($update->getRepeateach() > 1)
				{
					$repeat_each =  $update->getTermsper()	.'s' ;
				}

				if($update->getNoofterms() == 0)
				{
					if($data['productType'] == 'grouped')
					{
						if($update->getPriceCalculationType() == 1) //  %
						{
							$help_tooltip.= 'at every '.$update->getRepeateach().' '.$repeat_each.' with the '.$price.'% of product price. </p>' ;

						}
						else
						{
							$help_tooltip.= 'at every '.$update->getRepeateach().' '.$repeat_each.' with the '.$symbole.$update->getPrice().' of product price. </p>' ;
						}
					}
					else
					{
						if($update->getPriceCalculationType() == 1) //  %
						{
							$help_tooltip.= 'at every '.$update->getRepeateach().' '.$repeat_each.' with the price of '.$symbole.$price.'</p>' ;
						}
						else
						{
							$help_tooltip.= 'at every '.$update->getRepeateach().' '.$repeat_each.' with the price of '.$symbole.$update->getPrice().'</p>' ;
						}
					}
				}
				else
				{
					$noofterms =  $update->getNoofterms() -1 ;
					if($data['productType'] == 'grouped')
					{
						if($update->getPriceCalculationType() == 1) //  %
						{
							$help_tooltip.=  $noofterms.' times at every '.$update->getRepeateach().' '.$repeat_each.' with the '.$update->getPrice().'% of product price.</p>' ;	

						}
						else
						{
							$help_tooltip.=  $noofterms.' times at every '.$update->getRepeateach().' '.$repeat_each.' with the '.$symbole.$update->getPrice().' of product price.</p>' ;	
						}
					}
					else
					{
						if($update->getPriceCalculationType() == 1) //  %
						{
							$help_tooltip.=  $noofterms.' times at every '.$update->getRepeateach().' '.$repeat_each.' with the price of '.$symbole.$price.'</p>' ;

						}
						else{
							$help_tooltip.=  $noofterms.' times at every '.$update->getRepeateach().' '.$repeat_each.' with the price of '.$symbole.$update->getPrice().'</p>' ;

						}
					}
				}
				if(Mage::helper('recurringandrentalpayments')->isApplyDiscount())
				{
					$amount = Mage::helper('recurringandrentalpayments')->discountAmount() ;
					$calculation_type = Mage::helper('recurringandrentalpayments')->applyDiscountType();


					if($calculation_type == 1)  //Fixed
							$discountamount = $symbole.$amount;
					else
							$discountamount = $amount.'%';
					$help_tooltip .= '<p>You will get '.$discountamount.' dicount on your subscription.</p>';
				}
				 $help_tooltip .= '<p>* Final amount varies depending on shipping, tax and other charges.</p>';

				echo $help_tooltip ;
		   }
		}
		catch(Exception $e)
		{
			Mage::log($e);
		}
	}
	public function updateProductPriceAction()
	{
		$post_data = Mage::app()->getRequest()->getPost();
		$custom_option_price = $post_data['final_price'] - $post_data['product_price'];

		$update = Mage::getModel('recurringandrentalpayments/terms')->load($post_data['select_combo']);
		$current_term_price = $update->getPrice();
		$symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$product = Mage::getModel('catalog/product')->load($post_data['id']);
		if($post_data['select_combo'] == -1 || $product->getTypeId() == "grouped")
		{
			return;
		}
		elseif ($post_data['bundleSelectedOtionQty'] > 0)  // Bundle Product
		{
			$product_price = $product->getPrice();
			$total_qty = ($product_price <= 0)?0:1;
			
			if($update->getPriceCalculationType() == 0)  // Fixed
			{
				$price = $update->getPrice()  * ($post_data['bundleSelectedOtionQty'] + $total_qty);
			}
			else
			{
				$price = $update->getPrice() * $post_data['final_price'] / 100 ;
			}
			$price = '<span class="price-label" style="font-size:18px;">Subscription price </span>'.$symbol.number_format($price, 2, '.', '');
			echo $price;
		}
		else
		{
			if($update->getPriceCalculationType() == 0)  // Fixed
			{
				$price = $custom_option_price + $current_term_price;
			}
			else
			{
				$price = (($post_data['product_price'] * $update->getPrice())/100) +  $custom_option_price ;
			}
			$price = '<span class="price-label" style="font-size:18px;">Subscription price </span>'.$symbol.number_format($price, 2, '.', '');
			echo $price;
		}
		if($post_data['final_price'] == '' && $post_data['select_combo'] == -1 )   // This is for grouped product
		{
			$symbol = '';
		}
		header('Access-Control-Allow-Origin: *');
	}
	public function clauseacceptedAction()
	{
		Mage::getSingleton('core/session')->setIsaccepted(1);
		return;
		header('Access-Control-Allow-Origin: *');
	}
}