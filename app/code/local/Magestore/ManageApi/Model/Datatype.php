<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 6/19/17
 * Time: 3:07 PM
 */

class Magestore_ManageApi_Model_Datatype
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage::helper('manageapi')->__('event'),
                'label' => Mage::helper('manageapi')->__('event'),
            ),
            array(
                'value' => Mage::helper('manageapi')->__('posting'),
                'label' => Mage::helper('manageapi')->__('posting'),
            )
        );
    }
}