<?php 
class Magestore_Onestepcheckout_Model_AddCondition extends Mage_Rule_Model_Condition_Abstract {

    public function loadAttributeOptions() {
        $attributes = array(
            'real_subtotal' => Mage::helper('onestepcheckout')->__('Subtotal without Virtual Product value')
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement() {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType() {

        switch ($this->getAttribute()) {
            case 'real_subtotal':
                return 'numeric';
        }
        return 'string';
    }
    public function getValueElementType() {
        return 'text';
    }

    public function validate(Varien_Object $object) {
        $address = $object;
		$items = $address->getAllItems();
		$virtualValue = 0;
        foreach ($items as $item){
			if ($item->getProduct()->isVirtual()){
				$virtualValue += ($item->getProduct()->getPrice())*($item->getQty());
			}
		}
        $address->setSubtotal($address->getSubtotal() - $virtualValue);
        if (!$address instanceof Mage_Sales_Model_Quote_Address) {
            if ($object->getQuote()->isVirtual()) {
                $address = $object->getQuote()->getBillingAddress();
            }
            else {
                $address = $object->getQuote()->getShippingAddress();
            }
        }
        return $this->validateAttribute(trim($address->getSubtotal()));
    }

}