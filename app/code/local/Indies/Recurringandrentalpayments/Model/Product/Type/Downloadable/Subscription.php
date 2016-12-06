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

class Indies_Recurringandrentalpayments_Model_Product_Type_Downloadable_Subscription extends Mage_Downloadable_Model_Product_Type
{

    const PRODUCT_TYPE_DOWLOADABLE = 'downloadable';
    public $_used;
    protected $_canConfigure = true;

    /**
     * Prepares product for cart according to buyRequest.
     *
     * @param Varien_Object $buyRequest
     * @param object        $product [optional]
     * @return
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        Mage::getModel('recurringandrentalpayments/product_type_default')->checkPeriod($product, $buyRequest);

        $Period = Mage::getModel('recurringandrentalpayments/terms');

        /* We should add custom options that doesnt exist */
        if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
            if ($Period->load($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType())->getId()) {
                $product->addCustomOption('indies_recurringandrentalpayments_subscription_type', $Period->getId());
            }
        }

        if ($this->requiresSubscriptionOptions($product) && !$Period->getId()) {
            throw new Mage_Core_Exception(Mage::helper('recurringandrentalpayments')->__("Selected product requires subscription options"));
        }

        if ($buyRequest->getIndiesRnrSubscriptionStart() && $Period->getId()) {
            $date = new Zend_Date($buyRequest->getIndiesRnrSubscriptionStart(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
            // Check date

            // Never check if start date

            //$performDateCompare = !!Mage::getSingleton('customer/session')->getCustomer()->getId();
            $performDateCompare = !Indies_Recurringandrentalpayments_Model_Recurringandrentalpaymentscron::$isCronSession;

            $today = new Zend_Date;
            if (!$this->isVirtual($product)) {
                $today->addDayOfYear($Period->getPaymentOffset());
            }

            if ($performDateCompare
                && ($date->compare($today, Zend_Date::DATE_SHORT) < 0
                    || !$Period->isAllowedDate($date, $product))
            ) {
                throw new Mage_Core_Exception(Mage::helper('recurringandrentalpaymentss')->__("Selected date is not valid for specified period"));
            }
        }
        else
        {
            $date = Mage::app()->getLocale()->date();
        }
        $product->addCustomOption('indies_recurringandrentalpayments_subscription_start', $date->toString('Y-MM-dd'));


        $result = Mage_Catalog_Model_Product_Type_Virtual::prepareForCart($buyRequest, $product);

        if (is_string($result)) {
            return $result;
        }

        $preparedLinks = array();
        if ($this->getProduct($product)->getLinksPurchasedSeparately()) {
            if ($links = $buyRequest->getLinks()) {
                foreach ($this->getLinks($product) as $link) {
                    if (in_array($link->getId(), $links)) {
                        $preparedLinks[] = $link->getId();
                    }
                }
            }
        } else 
		{
            foreach ($this->getLinks($product) as $link)
			{
                $preparedLinks[] = $link->getId();
            }
        }
        if ($preparedLinks) {
            $this->getProduct($product)->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            return $result;
        }

        return $result;
    }

    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null)
    {
        Mage::getModel('recurringandrentalpayments/product_type_default')->checkPeriod($product, $buyRequest);

        $Period = Mage::getModel('recurringandrentalpayments/terms');

        /* We should add custom options that doesnt exist */
        if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
            if ($Period->load($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType())->getId()) {
                $product->addCustomOption('indies_recurringandrentalpayments_subscription_type', $Period->getId());
            }
        }

        if ($this->requiresSubscriptionOptions($product) && !$Period->getId()) {
            throw new Mage_Core_Exception(Mage::helper('recurringandrentalpayments')->__("Selected product requires subscription options"));
        }

        if ($buyRequest->getIndiesRnrSubscriptionStart() && $Period->getId()) {
            $date = new Zend_Date($buyRequest->getIndiesRnrSubscriptionStart(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
            // Check date

            // Never check if start date

            //$performDateCompare = !!Mage::getSingleton('customer/session')->getCustomer()->getId();
            $performDateCompare = !Indies_Recurringandrentalpayments_Model_Recurringandrentalpaymentscron::$isCronSession;

            $today = new Zend_Date;
            if (!$this->isVirtual($product)) {
                $today->addDayOfYear($Period->getPaymentOffset());
            }

            if ($performDateCompare
                && ($date->compare($today, Zend_Date::DATE_SHORT) < 0
                    || !$Period->isAllowedDate($date, $product))
            ) {
                throw new Mage_Core_Exception(Mage::helper('recurringandrentalpayments')->__("Selected date is not valid for specified period"));
            }
        }
        else
        {
            $date = Mage::app()->getLocale()->date();
        }
        $product->addCustomOption('indies_recurringandrentalpayments_subscription_start', $date->toString('Y-MM-dd'));


        $result = Mage_Catalog_Model_Product_Type_Virtual::prepareForCartAdvanced($buyRequest, $product);

        if (is_string($result)) {
            return $result;
        }

        $preparedLinks = array();
        if ($this->getProduct($product)->getLinksPurchasedSeparately()) {
            if ($links = $buyRequest->getLinks()) {
                foreach ($this->getLinks($product) as $link) {
                    if (in_array($link->getId(), $links)) {
                        $preparedLinks[] = $link->getId();
                    }
                }
            }
        } else {
            foreach ($this->getLinks($product) as $link) {
                $preparedLinks[] = $link->getId();
            }
        }
        if ($preparedLinks) {
            $this->getProduct($product)->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            return $result;
        }

        return $result;
    }

    public function prepareOrderItemSave($product, $orderItem)
    {
        if (Mage::getModel('downloadable/link_purchased')->load($orderItem->getId(), 'order_item_id')->getId()) {
            return;
        }

        if ($product->getTypeId() == self::PRODUCT_TYPE_DOWLOADABLE) {
            $links = $product->getTypeInstance(true)->getLinks($product);
            if ($linkIds = $orderItem->getProductOptionByCode('links')) {
                $linkPurchased = Mage::getModel('downloadable/link_purchased');
                Mage::helper('core')->copyFieldset(
                    'downloadable_sales_copy_order',
                    'to_downloadable',
                    $orderItem->getOrder(),
                    $linkPurchased
                );
                Mage::helper('core')->copyFieldset(
                    'downloadable_sales_copy_order_item',
                    'to_downloadable',
                    $orderItem,
                    $linkPurchased
                );
                $linkSectionTitle = (
                $product->getLinksTitle() ?
                        $product->getLinksTitle()
                        : Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE)
                );
                $linkPurchased->setLinkSectionTitle($linkSectionTitle)
                        ->save();
                foreach ($linkIds as $linkId) {
                    if (isset($links[$linkId])) {
                        $linkPurchasedItem = Mage::getModel('downloadable/link_purchased_item')
                                ->setPurchasedId($linkPurchased->getId())
                                ->setOrderItemId($orderItem->getId());

                        Mage::helper('core')->copyFieldset(
                            'downloadable_sales_copy_link',
                            'to_purchased',
                            $links[$linkId],
                            $linkPurchasedItem
                        );
                        $linkHash = strtr(base64_encode(microtime() . $linkPurchased->getId() . $orderItem->getId() . $product->getId()), '+/=', '-_,');
                        $numberOfDownloads = $links[$linkId]->getNumberOfDownloads() * $orderItem->getQtyOrdered();
                        $linkPurchasedItem->setLinkHash($linkHash)
                                ->setNumberOfDownloadsBought($numberOfDownloads)
                                ->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING)
                                ->setCreatedAt($orderItem->getCreatedAt())
                                ->setUpdatedAt($orderItem->getUpdatedAt())
                                ->save();
                    }
                }
            }
        }
    }

    /**
     * @param $product
     * @return bool
     */
    public function hasRequiredOptions($product = null)
	{
		return true;
	}


    /**
     * Returns true if product has subscriptions options
     * @return bool
	 */
    public function hasSubscriptionOptions()
    {
         $plan = Mage::getModel('recurringandrentalpayments/plans')->load($this->getProduct()->getId(),'product_id');
		if(!$plan->getId()){
            return false;
        }
        return true;
    }

    /**
     * Returns true if product requires subscription options
     * @return bool
     */
    public function requiresSubscriptionOptions($product = null)
    {
        if (is_null($product)) $product = $this->getProduct();
        $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
		if (!$plan->getPlanStatus()) return false;
        
        if (!$plan->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Returns default period id. If none, returns -1
     * @return int
     */
   public function getDefaultSubscriptionPeriodId()
    {
        $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
        return isset($plan->geId()) ? $plan->geId() : Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE;
    }

    /**
     * Returns if product is "virtual", e.g. requires no shipping
     *
     * @param object $product [optional]
     * @return bool
     */
    public function isVirtual($product = null)
    {
        return true;
    }

    public function processBuyRequest($product, $buyRequest)
    {
        $toReturn = parent::processBuyRequest($product, $buyRequest);
        if ($buyRequest->getData('indies_recurringandrentalpayments_subscription_start')) $toReturn['indies_recurringandrentalpayments_subscription_start'] = $buyRequest->getData('indies_recurringandrentalpayments_subscription_start');
        if ($buyRequest->getData('indies_recurringandrentalpayments_subscription_type')) $toReturn['indies_recurringandrentalpayments_subscription_type'] = $buyRequest->getData('indies_recurringandrentalpayments_subscription_type');
        return $toReturn;
    }

    public function beforeSave($product = null)
    {
        parent::beforeSave($product);
        $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
		if ($plan->getPlanStatus() && $this->getProduct($product)->getSubscriptionPrice() == '')
            $this->getProduct($product)->setSubscriptionPrice($product->getData('price'));
    }

}