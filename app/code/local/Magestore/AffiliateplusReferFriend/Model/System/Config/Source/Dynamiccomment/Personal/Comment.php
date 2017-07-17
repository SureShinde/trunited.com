<?php

class Magestore_AffiliateplusReferFriend_Model_System_Config_Source_Dynamiccomment_Personal_Comment extends Mage_Core_Model_Config_Data
{

    public function getCommentText(Mage_Core_Model_Config_Element $element, $currentValue)
    {
        $result = "<p class='note' id='dynamic_personal_comment'></p>";
        $result .= "<script type='text/javascript'>
            function update_personal_commment_content()
            {
             var comment = $('dynamic_personal_comment');
             var param = $('affiliateplus_general_personal_param').getValue();
             var param_value = $('affiliateplus_general_personal_value').getValue();
             comment.innerHTML = 'E.g: " . Mage::getUrl() . "?'+ param +'='+param_value;
            }

            function init_personal_comment()
            {
              update_personal_commment_content();
                $('affiliateplus_general_personal_param').observe('change', function(){
                update_personal_commment_content();
                });

                $('affiliateplus_general_personal_value').observe('change', function(){
                update_personal_commment_content();
                });
            }
            document.observe('dom:loaded', function(){init_personal_comment();});
            </script>";
        return $result;
    }

}
