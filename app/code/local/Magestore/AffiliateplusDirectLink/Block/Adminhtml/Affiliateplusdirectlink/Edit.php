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
class Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'affiliateplusdirectlink';
        $this->_controller = 'adminhtml_affiliateplusdirectlink';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliateplusdirectlink')->__('Save Direct Link'));
        $this->_updateButton('delete', 'label', Mage::helper('affiliateplusdirectlink')->__('Delete Direct Link'));
        
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
        if (Mage::registry('affiliateplusdirectlink_data')
            && Mage::registry('affiliateplusdirectlink_data')->getId()
        ) {
            return Mage::helper('affiliateplusdirectlink')->__("Edit Direct Link '%s'",
                                                $this->htmlEscape(Mage::registry('affiliateplusdirectlink_data')->getLink())
            );
        }
        return Mage::helper('affiliateplusdirectlink')->__('Add Direct Link');
    }
}