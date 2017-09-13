<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SocialLogin
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_SocialLogin_Model_Observer
{

    public function controllerActionPredispatch()
    {
        $helper = Mage::helper('pslogin');
        if(!$helper->moduleEnabled()) {
            return;
        }

        // Check email.
        $request = Mage::app()->getRequest();
        $requestString = $request->getRequestString();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        $editUri = 'customer/account/edit';

        switch(true) {

            case (stripos($requestString, 'customer/account/logout') !== false):
                break;

            case $moduleName = (stripos($module, 'customer') !== false) ? 'customer' : null:
            // case $moduleName = (stripos($module, 'checkout') !== false && stripos($controller, 'onepage') !== false && stripos($action, 'index') !== false) ? 'checkout' : null:

                $session = Mage::getSingleton('customer/session');
                if($session->isLoggedIn() && $helper->isFakeMail()) {

                    $session->getMessages()->deleteMessageByIdentifier('fakeemail');
                    $message = $helper->__('Your account needs to be updated. The email address in your profile is invalid. Please indicate your valid email address by going to the <a href="%s">Account edit page</a>', Mage::getUrl($editUri));

                    switch($moduleName) {
                        case 'customer':
                            if(stripos($requestString, $editUri) !== false) {
                                // Set new message and red field.
                                $message = $helper->__('Your account needs to be updated. The email address in your profile is invalid. Please indicate your valid email address.');
                            }
                            $session->addUniqueMessages(Mage::getSingleton('core/message')->notice($message)->setIdentifier('fakeemail'));
                            break;

                        /*case 'checkout':
                            $session->addUniqueMessages(Mage::getSingleton('core/message')->notice($message)->setIdentifier('fakeemail'));
                            break;*/
                    }

                }
                break;
        }
    }

    public function customerLogin($observer)
    {
        $helper = Mage::helper('pslogin');
        if(!$helper->moduleEnabled()) {
            return;
        }

        // Set redirect url.
        $redirectUrl = $helper->getRedirectUrl('login');
        Mage::getSingleton('customer/session')->setBeforeAuthUrl($redirectUrl);
    }

    public function customerRegisterSuccess($observer)
    {
        $helper = Mage::helper('pslogin');
        if(!$helper->moduleEnabled()) {
            return;
        }

        $data = Mage::getSingleton('customer/session')->getData('pslogin');

        if(!empty($data['provider']) && !empty($data['timeout']) && $data['timeout'] > time()) {
            $model = Mage::getSingleton("pslogin/{$data['provider']}");

            $customerId = null;
            if($customer = $observer->getCustomer()) {
                $customerId = $customer->getId();
            }

            if($customerId) {
                $model->setUserData($data);

                // Remember customer.
                $model->setCustomerIdByUserId($customerId);

                // Load photo.
                if($helper->photoEnabled()) {
                    $model->setCustomerPhoto($customerId);
                }
            }

        }

        // Show share-popup.
        $helper->showPopup(true);

        // Set redirect url.
        $redirectUrl = $helper->getRedirectUrl('register');
        Mage::app()->getRequest()->setParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_SUCCESS_URL, $redirectUrl);
    }

    public function customerLogout()
    {
        $helper = Mage::helper('pslogin');
        if(!$helper->moduleEnabled()) {
            return;
        }

        Mage::getSingleton('customer/session')->unsLoginProvider();
    }

    /**
     * Add social login account field to customer grid
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function beforeBlockToHtml($observer)
    {
         $grid = $observer->getBlock();

        /**
         * Mage_Adminhtml_Block_Customer_Grid
         */
        if ($grid instanceof Mage_Adminhtml_Block_Customer_Grid) {

            $options = Mage::getModel('pslogin/account')->getCollection();
            $options->getSelect()->group('type');
            $options = $options->load()
                ->toOptionHash();

            foreach ($options as $key => $option) {
                $options[$key] = ucfirst($option);
            }

            $grid->addColumnAfter(
                'pslogin_account',
                array(
                    'header' => Mage::helper('pslogin')->__('Social Accounts'),
                    'index'  => 'pslogin_account',
                    'frame_callback' => array($this, 'decoratePsLogin'),
                    'type'      =>  'options',
                    'options'   => $options,
                    'filter_condition_callback' => array($this, 'filterPsAccount')
                ),
                'group'
            );
        }
    }

    public function filterPsAccount($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $resource = Mage::getSingleton('core/resource');
        $collection->getSelect()
            ->join(
                array('pslogin'  => $resource->getTableName('pslogin/account')), 'pslogin.customer_id = e.entity_id AND pslogin.type = "' . $value . '"'
            );
    }

    /**
     * Decorate Ps login links
     * @param  string $value
     * @param  object $row
     * @param  object $column
     * @param  boolean $isExport
     * @return string
     */
    public function decoratePsLogin($value, $row, $column, $isExport)
    {

        $psAccounts = Mage::getModel('pslogin/account')->getCollection()
            ->addFieldToFilter('customer_id', $row->getEntityId());

        $html = '';
        if ($psAccounts->count()) {

            // var_dump($psAccounts->getData());
            // exit;


            foreach ($psAccounts as $account) {
                $type = $account->getType();

                if ($type) {
                    $socialUrl = $account->getAccountUrl();

                    $html .= '<div class="pslogin-block">';
                    $html .= '<div class="ps-btn ' . $type . '">';
                    $icon = '<span class="soc-li-icon"></span>';
                    if ($socialUrl) {
                        $html .= '<a title="' . ucfirst($type) . '" target="_blank" href="' . $socialUrl .'">' . $icon . '</a>';
                    } else {
                        $html .= $icon;
                    }

                    $html .= '</div></div>';
                }
            }
        }

        if ($html) {
            $html = '<div class="pslogin-block-hld">' . $html . '</div>';
        }

        return $html;
    }

   /**
     * Add customer navigation link
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function addCustomerNavigationLink($observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Customer_Block_Account_Navigation) {
            if (Mage::helper('pslogin')->isModuleEnabled() && Mage::helper('pslogin')->isSocialLinkingEnabled()) {
                $block->addLink('pslogin', 'pslogin/account/view', Mage::helper('pslogin')->__('My Social Accounts'));
            }
        }
    }
}
