<?php 
class Magestore_Affiliatepluspayperlead_Block_Adminhtml_Affiliatepluspayperlead_Renderer_Account
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 public function render(Varien_Object $row){
  
   return sprintf('
    <a href="%s" title="%s">%s</a>',
    $this->getUrl('affiliateplus/adminhtml_account/edit/', array('_current'=>true, 'id' => $row->getAccountId())),
    Mage::helper('affiliatepluspayperlead')->__('View Account Detail'),
    Mage::helper('core/string')->truncate($row->getAccountName(),30)
   ); 
 }
}