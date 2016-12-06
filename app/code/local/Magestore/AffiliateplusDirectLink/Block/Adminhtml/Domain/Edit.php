<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusdirectlink Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Adminhtml_Domain_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'affiliateplusdirectlink';
        $this->_controller = 'adminhtml_domain';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliateplusdirectlink')->__('Save Domain'));
        $this->_updateButton('delete', 'label', Mage::helper('affiliateplusdirectlink')->__('Delete Domain'));
        
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('affiliateplusdirectlink_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'affiliateplusdirectlink_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'affiliateplusdirectlink_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('domain_data')
            && Mage::registry('domain_data')->getId()
        ) {
            return Mage::helper('affiliateplusdirectlink')->__("Edit Domain '%s'",
                                                $this->htmlEscape(Mage::registry('domain_data')->getDomain())
            );
        }
        return Mage::helper('affiliateplusdirectlink')->__('Add Domain');
    }
}