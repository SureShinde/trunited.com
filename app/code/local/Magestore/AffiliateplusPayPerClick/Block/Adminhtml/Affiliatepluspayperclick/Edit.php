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
 * @package     Magestore_AffiliateplusPayPerClick
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliatepluspayperclick Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_AffiliateplusPayPerClick
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusPayPerClick_Block_Adminhtml_Affiliatepluspayperclick_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'affiliatepluspayperclick';
        $this->_controller = 'adminhtml_affiliatepluspayperclick';
        
        $this->_updateButton('save', 'label', Mage::helper('affiliatepluspayperclick')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('affiliatepluspayperclick')->__('Delete Item'));
        
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('affiliatepluspayperclick_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'affiliatepluspayperclick_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'affiliatepluspayperclick_content');
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
        if (Mage::registry('affiliatepluspayperclick_data')
            && Mage::registry('affiliatepluspayperclick_data')->getId()
        ) {
            return Mage::helper('affiliatepluspayperclick')->__("Edit Item '%s'",
                                                $this->htmlEscape(Mage::registry('affiliatepluspayperclick_data')->getTitle())
            );
        }
        return Mage::helper('affiliatepluspayperclick')->__('Add Item');
    }
}