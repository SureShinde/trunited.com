<?php

$model=Mage::getModel('eav/entity_setup','core_setup');

$model->addAttribute('catalog_product', 'first_period_price', array(
                                                                           'backend' => '',
                                                                           'source' => '',
                                                                           'group' => 'Prices',
                                                                           'label' => 'First Term Price',
                                                                           'input' => 'text',
                                                                           'class' => 'validate-float',
                                                                           'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                                                                           'visible' => 1,
                                                                           'default_value' => 1,
                                                                           'required' => 0,
                                                                           'user_defined' => false,
                                                                           'apply_to' => 'simple',
                                                                           'visible_on_front' => false
                                                                      ));
?>