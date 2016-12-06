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
class Indies_Recurringandrentalpayments_Block_Authorizenet_Info_Cc extends Mage_Paygate_Block_Authorizenet_Info_Cc
{
    /**
     * Retrieve credit cards info
     *
     * @return array
     */
    public function getCards()
    {
	/* Start : To solv issue of next seq capture amont is incorrect display in 'capture amount' field **/	
		$order_amount = '';

		
		if(Mage::helper('recurringandrentalpayments')->canRun())
		{
		   if(Mage::helper('recurringandrentalpayments')->isEnabled())
		   {
			 if($this->getRequest()->getParam('order_id')!='')
			 {
			    $order_amount =Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'))->getGrandTotal(); 
			 }	
		   }
		}

	/* End : To solv issue of next seq capture amont is incorrect display in 'capture amount' field **/		
	    $cardsData = $this->getMethod()->getCardsStorage()->getCards();

		$cards = array();
	
        if (is_array($cardsData)) {
            foreach ($cardsData as $cardInfo) {
                $data = array();
                if ($cardInfo->getProcessedAmount()) {
               /* start : Apply above set amount here */     
					if($order_amount =='')
					{
						$amount = Mage::helper('core')->currency($cardInfo->getProcessedAmount(), true, false);
						$data[Mage::helper('paygate')->__('Processed Amount')] = $amount;
					}
				
					else
					{
						$amount = Mage::helper('core')->currency($order_amount, true, false);
						$data[Mage::helper('paygate')->__('Processed Amount')] = $amount;

					}
			  /* End : Apply above set amount here */
				}
                if ($cardInfo->getBalanceOnCard() && is_numeric($cardInfo->getBalanceOnCard())) {
                    $balance = Mage::helper('core')->currency($cardInfo->getBalanceOnCard(), true, false);
                    $data[Mage::helper('paygate')->__('Remaining Balance')] = $balance;
                }
                $this->setCardInfoObject($cardInfo);
                $cards[] = array_merge($this->getSpecificInformation(), $data);
                $this->unsCardInfoObject();
                $this->_paymentSpecificInformation = null;
            }
        }
        if ($this->getInfo()->getCcType() && $this->_isCheckoutProgressBlockFlag) {
            $cards[] = $this->getSpecificInformation();
        }
        return $cards;
    }
}
?>