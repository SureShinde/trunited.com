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

class Indies_Recurringandrentalpayments_Block_Html_Date extends Mage_Core_Block_Html_Date
{

    public function getExcludedWeekDays()
    {
        return $this->getPeriod()->getExcludedWeekdays();
    }

    public function getPeriodsExcludedData()
    {
        $out = array('excluded_weekdays' => array(), 'first_allowed_day' => array());

        foreach (Mage::getModel('recurringandrentalpayments/terms')->getCollection() as $Period) {
            $zDate = new Zend_Date($this->formatDate($Period->getNearestAvailableDay(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), null, Mage::app()->getLocale()->getLocaleCode());
            $date = $zDate->toString(preg_replace(array('/M+/', '/d+/'), array('MM', 'dd'), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)));

            $out['excluded_weekdays'][$Period->getId()] = $Period->getExcludedWeekdays();
            $out['first_allowed_day'][$Period->getId()] = $date;

        }
        return $out;
    }

    protected function _toHtml()
    {
        $periodData = ($this->getPeriodsExcludedData());

        $html = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->getValue() . '" class="' . $this->getClass() . '" style="width:115px" ' . $this->getExtraParams() . '/> ';

        $html .= '<img src="' . $this->getImage() . '" alt="" class="v-middle" ';
        $html .= 'title="' . $this->helper('core')->__('Select Date') . '" id="' . $this->getId() . '_trig" />';

        $html .=

              '<script type="text/javascript">
            IndiesRecurringandrentalpaymentsDisabledWeekdays = ' . (Zend_Json::encode(@$periodData['excluded_weekdays'])) . ';
            IndiesRecurringandrentalpaymentsFirstAvailDays = ' . (Zend_Json::encode(@$periodData['first_allowed_day'])) . ';

            Calendar.setup({
                inputField  : "' . $this->getId() . '",
                ifFormat    : "' . $this->getFormat() . '",
                button      : "' . $this->getId() . '_trig",
                align       : "Bl",
                disableFunc : function(){
					
                    var els = document.getElementsByName("indies_recurringandrentalpayments_subscription_type");

				    for(var n=els.length-1;n>=0;n--){
                        if($F(els[n])){
                            var periodId = $F(els[n]);
                            break;
                        }
                    }
					
                    if(!periodId){
                        throw("Cannt detect subscription type")
                    }
                    if(periodId == ' . Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE . '){
                        return true;
                    }

                    var D = new Date();
					
                    minDate = (Date.parseDate(IndiesRecurringandrentalpaymentsFirstAvailDays[periodId], "' . $this->getFormat() . '"))
			
					if(minDate.getTime() < D.getTime()){
                        D = minDate;
                    }
					
                    var seedToday = D.getFullYear()*10000 + (D.getMonth()+1)*100 + D.getDate();
                    var seedArgument =   arguments[0].getFullYear()*10000 +  (arguments[0].getMonth()+1)*100 +  arguments[0].getDate();

                    var wd = arguments[0].getDay();
					var i = -1;
					if(i< 0 && seedToday > seedArgument){
                        return true
                    }
                   
                },
                singleClick : true
            });
        </script>  ' ;

        return $html;
    }

}
