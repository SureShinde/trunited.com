<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 6/19/17
 * Time: 3:07 PM
 */

class Magestore_ManageApi_Model_Format
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage::helper('manageapi')->__('json'),
                'label' => Mage::helper('manageapi')->__('json'),
            ),
            array(
                'value' => Mage::helper('manageapi')->__('xml'),
                'label' => Mage::helper('manageapi')->__('xml'),
            )
        );
    }
}