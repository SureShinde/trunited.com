<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Impressiontype
 *
 * 
 * @author blanka
 */
class Magestore_AffiliateplusCostPerMille_Model_System_Config_Source_Impressiontype {
    //put your code here
     public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Unique')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Raw')),
        );
    }
}
