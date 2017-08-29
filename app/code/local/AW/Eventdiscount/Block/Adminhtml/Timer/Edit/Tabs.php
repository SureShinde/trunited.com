<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Eventdiscount
 * @version    1.0.5
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Eventdiscount_Block_Adminhtml_Timer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('timer_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Timer'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'   => $this->__('General'),
            'title'   => $this->__('General'),
            'content' => $this->getLayout()->createBlock('eventdiscount/adminhtml_timer_edit_tab_general')->toHtml(),
            'active'  => ($this->getRequest()->getParam('tab') == 'timer_tabs_general') ? true : false,
        ));
        $this->addTab('rules', array(
            'label'   => $this->__('Rules'),
            'title'   => $this->__('Rules'),
            'content' => $this->getLayout()->createBlock('eventdiscount/adminhtml_timer_edit_tab_rules')->toHtml(),
            'active'  => ($this->getRequest()->getParam('tab') == 'timer_tabs_rules') ? true : false,
        ));
        /*$this->addTab('design', array(
            'label'   => $this->__('Design'),
            'title'   => $this->__('Design'),
            'content' => $this->getLayout()->createBlock('eventdiscount/adminhtml_timer_edit_tab_design')->toHtml(),
            'active'  => ($this->getRequest()->getParam('tab') == 'timer_tabs_design') ? true : false,
        ));*/
        $this->addTab('products', array(
            'label'   => $this->__('Products'),
            'title'   => $this->__('Products'),
            'url'     => $this->getUrl('*/*/products', array('_current' => true)),
            'class'   => 'ajax',
            'active'  => ($this->getRequest()->getParam('tab') == 'timer_tabs_products') ? true : false,
        ));
        if (!is_null($this->getRequest()->getParam('id'))) {
            $this->addTab('statistics', array(
                'label'   => $this->__('Statistics'),
                'title'   => $this->__('Statistics'),
                'content' => $this->getLayout()
                    ->createBlock('eventdiscount/adminhtml_timer_edit_tab_statistics')->toHtml(),
                'active'  => ($this->getRequest()->getParam('tab') == 'timer_tabs_statistics') ? true : false,
            ));
        }
        return parent::_beforeToHtml();
    }
}