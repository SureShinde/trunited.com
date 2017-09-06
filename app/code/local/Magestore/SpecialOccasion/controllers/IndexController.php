<?php

class Magestore_SpecialOccasion_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * check customer is logged in
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        $action = $this->getRequest()->getActionName();
        if ($action != 'policy' && $action != 'redirectLogin') {
            // Check customer authentication
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                Mage::getSingleton('customer/session')->setAfterAuthUrl(
                    Mage::getUrl($this->getFullActionName('/'))
                );
                $this->_redirectUrl('customer/account/login');
                $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            }
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('specialoccasion')->__('The Special Occasion'));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link"  => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link"  => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("interest", array(
            "label" => $this->__("Interest and Leisure"),
            "title" => $this->__("Interest and Leisure"),
        ));

        $this->renderLayout();
    }

    public function addAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('specialoccasion')->__('Create new Special Occasion'));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link"  => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link"  => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("interest", array(
            "label" => $this->__("Interest and Leisure"),
            "title" => $this->__("Interest and Leisure"),
            "link"  => Mage::getUrl('*/*/')
        ));

        $breadcrumbs->addCrumb("interest_create", array(
            "label" => $this->__("Create"),
            "title" => $this->__("Create"),
        ));
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('specialoccasion')->__('Details Special Occasion'));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link"  => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link"  => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("interest", array(
            "label" => $this->__("Interest and Leisure"),
            "title" => $this->__("Interest and Leisure"),
            "link"  => Mage::getUrl('*/*/')
        ));

        $breadcrumbs->addCrumb("interest_view", array(
            "label" => $this->__("Details"),
            "title" => $this->__("Details"),
        ));
        $this->renderLayout();
    }

    public function addPostAction()
    {
        $data = $this->getRequest()->getParams();
        if ($data != null) {
            $occasionId = Mage::helper('specialoccasion')->getCurrentOccasionId();
            $item = Mage::getModel('specialoccasion/item');

            $ship_date = date('Y-m-d', strtotime($data['occasion_date']));
            $compare_date = Mage::helper('specialoccasion/cron')->compareTime(
                time(),
                strtotime($data['occasion_date'])
            );

            if(time() < strtotime($data['occasion_date']) || time() > strtotime($data['occasion_date'])) {
                if ($compare_date < Mage::helper('specialoccasion')->getConfigData('general', 'day_send_email')) {
                    Mage::getSingleton('core/session')->addNotice(
                        Mage::helper('specialoccasion')->__(
                            Mage::helper('specialoccasion')->getConfigData('general', 'warning_message')
                        )
                    );
                }
            }

            $item_data = array(
                'specialoccasion_id' => $occasionId,
                'product_id' => $data['product_id'],
                'qty' => 1,
                'ship_date' => $ship_date,
                'message' => $data['message'],
                'occasion' => $data['occasion'],
                'created_at' => now(),
                'updated_at' => now(),
                'status' => Magestore_SpecialOccasion_Model_Status::STATUS_ITEM_PENDING,
                'state' => Magestore_SpecialOccasion_Model_Status::STATE_ACTIVE,
            );

            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();

                $item->setData($item_data)->save();

                if ($item != null && $item->getId()) {
                    $address = Mage::getModel('specialoccasion/address');
                    $data['region_id'] = Mage::helper('specialoccasion')->checkRegionId(
                        $data['country'],
                        $data['region'],
                        $data['region_id']
                    );
                    if ($data['region_id'] == null)
                        throw new Exception(
                            Mage::helper('specialoccasion')->__('Please enter the State in Shipping Address.')
                        );

                    $name = explode(' ', $data['name']);
                    if (!isset($name[1]))
                        $name[1] = $name[0];

                    $address_data = array(
                        'item_id' => $item->getId(),
                        'specialoccasion_id' => $occasionId,
                        'company' => '',
                        'telephone' => $data['telephone'],
                        'fax' => '',
                        'street' => $data['street'],
                        'state' => isset($data['state']) ? $data['state'] : '',
                        'city' => $data['city'],
                        'zipcode' => $data['zipcode'],
                        'country' => $data['country'],
                        'region' => $data['region'],
                        'region_id' => $data['region_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                        'firstname' => $name[0],
                        'lastname' => $name[1],
                    );

                    $address->setData($address_data)->save();


                    $connection->commit();

                    Mage::getSingleton('core/session')->addSuccess(
                        Mage::helper('specialoccasion')->__('You have just added the Special Occasion successfully!')
                    );
                }
            } catch (Exception $e) {
                $connection->rollback();
                Mage::getSingleton('core/session')->addError(
                    $e->getMessage()
                );
            }

        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function updateAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('specialoccasion')->__('Update The Special Occasion'));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link"  => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link"  => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("interest", array(
            "label" => $this->__("Interest and Leisure"),
            "title" => $this->__("Interest and Leisure"),
            "link"  => Mage::getUrl('*/*/')
        ));

        $breadcrumbs->addCrumb("interest_update", array(
            "label" => $this->__("Update"),
            "title" => $this->__("Update"),
        ));
        $this->renderLayout();
    }

    public function paymentAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('specialoccasion')->__('The Special Occasion Payment'));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link"  => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link"  => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("interest", array(
            "label" => $this->__("Interest and Leisure"),
            "title" => $this->__("Interest and Leisure"),
            "link"  => Mage::getUrl('*/*/')
        ));

        $breadcrumbs->addCrumb("interest_payment", array(
            "label" => $this->__("Payment"),
            "title" => $this->__("Payment"),
        ));
        $this->renderLayout();
    }

    public function paymentPostAction()
    {
        $current_credit_card = $this->getRequest()->getParam('current_credit_card');
        $use_trugiftcard = $this->getRequest()->getParam('use_trugiftcard');
        $occasion = Mage::helper('specialoccasion')->getCurrentOccasion();

        try {
            if ($current_credit_card > 0) {
                $cards = Mage::getModel('tokenbase/card')->getCollection()
                    ->addFieldToFilter('active', 1)
                    ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                    ->addFieldToFilter('method', 'authnetcim');

                if (sizeof($cards) > 0) {
                    foreach ($cards as $_card) {
                        $_card->setData('use_in_occasion', 0);
                        $_card->setData('updated_at', now());
                        $_card->save();
                    }

                }

                $card = Mage::getModel('tokenbase/card')->load($current_credit_card);
                if ($card != null && $card->getId()) {
                    $card->setData('use_in_occasion', 1);
                    $card->setData('updated_at', now());
                    $card->save();
                }
            }

            if ($occasion != null && $occasion->getId()) {
                $occasion->setData('use_trugiftcard', $use_trugiftcard != null && strcasecmp($use_trugiftcard, 'on') == 0 ? 1 : 0);
                $occasion->save();
            }

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('specialoccasion')->__('You have updated Payment Information successfully!')
            );
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/*/payment'));
    }

    public function updatePostAction()
    {
        $data = $this->getRequest()->getParams();
        if ($data != null) {
            $item = Mage::getModel('specialoccasion/item')->load($data['item_id']);

            $occasionId = Mage::helper('specialoccasion')->getCurrentOccasionId();

            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();

                $old_ship_date = $item->getShipDate();
                $ship_date = date('Y-m-d', strtotime($data['occasion_date']));
                $compare_date = Mage::helper('specialoccasion/cron')->compareTime(
                    time(),
                    strtotime($data['occasion_date'])
                );

                if(strtotime($old_ship_date) < strtotime($data['occasion_date']) || strtotime($old_ship_date) > strtotime($data['occasion_date'])) {

                    if ($compare_date < Mage::helper('specialoccasion')->getConfigData('general', 'day_send_email')) {
                        Mage::getSingleton('core/session')->addNotice(
                            Mage::helper('specialoccasion')->__(
                                Mage::helper('specialoccasion')->getConfigData('general', 'warning_message')
                            )
                        );
                    }
                    $item->setStatus(Magestore_SpecialOccasion_Model_Status::STATUS_ITEM_PENDING);
                }

                $item->setData('product_id', $data['product_id']);
                $item->setData('qty', 1);
                $item->setData('ship_date', $ship_date);
                $item->setData('message', $data['message']);
                $item->setData('occasion', $data['occasion']);
                $item->setData('specialoccasion_id', $occasionId);
                $item->setData('updated_at', now());
                $item->setData('state', $data['item_state']);
                $item->save();

                if ($item != null && $item->getId()) {
                    $address = Mage::getModel('specialoccasion/address')->load($data['address_id']);
                    $data['region_id'] = Mage::helper('specialoccasion')->checkRegionId(
                        $data['country'],
                        $data['region'],
                        $data['region_id']
                    );
                    if ($data['region_id'] == null)
                        throw new Exception(
                            Mage::helper('specialoccasion')->__('Please enter the State in Shipping Address.')
                        );

                    $name = explode(' ', $data['name']);
                    if (!isset($name[1]))
                        $name[1] = $name[0];

                    $address->setData('telephone', $data['telephone']);
                    $address->setData('street', $data['street']);
                    $address->setData('state', isset($data['state']) ? $data['state'] : '');
                    $address->setData('city', $data['city']);
                    $address->setData('zipcode', $data['zipcode']);
                    $address->setData('country', $data['country']);
                    $address->setData('region', $data['region']);
                    $address->setData('region_id', $data['region_id']);
                    $address->setData('updated_at', now());
                    $address->setData('firstname', $name[0]);
                    $address->setData('lastname', $name[1]);
                    $address->setData('specialoccasion_id', $occasionId);
                    $address->save();

                    $connection->commit();

                    Mage::getSingleton('core/session')->addSuccess(
                        Mage::helper('specialoccasion')->__('You have just updated the Special Occasion successfully!')
                    );
                }
            } catch (Exception $e) {
                $connection->rollback();
                Mage::getSingleton('core/session')->addError(
                    $e->getMessage()
                );
            }

        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function deleteAction()
    {
        $item_id = $this->getRequest()->getParam('id');
        $item = Mage::getModel('specialoccasion/item')->load($item_id);

        if ($item != null && $item->getId()) {
            try {
                $item->delete();

                $address = Mage::getModel('specialoccasion/address')->getCollection()
                    ->addFieldToFilter('item_id', $item_id);

                if ($address != null && sizeof($address) > 0) {
                    foreach ($address as $adr) {
                        $adr->delete();
                    }
                }

                Mage::getSingleton('core/session')->addSuccess(
                    Mage::helper('specialoccasion')->__('You have just deleted the Special Occasion successfully!')
                );

            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(
                    $e->getMessage()
                );
            }
        }

        $this->_redirectUrl(Mage::getUrl('*/*/'));
    }

    public function updateDbAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
DROP TABLE IF EXISTS {$setup->getTable('specialoccasion/specialoccasion')};
CREATE TABLE {$setup->getTable('specialoccasion/specialoccasion')} (
  `specialoccasion_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(10) NOT NULL,
  `status` varchar(255) NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  `use_trugiftcard` tinyint(4) NULL DEFAULT 1,
  PRIMARY KEY (`specialoccasion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$setup->getTable('specialoccasion/address')};
CREATE TABLE {$setup->getTable('specialoccasion/address')} (
  `address_id` int(10) unsigned NOT NULL auto_increment,
  `specialoccasion_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `firstname` text NULL,
  `lastname` text NULL,
  `company` text NULL,
  `telephone` text NULL,
  `fax` text NULL,
  `street` text NULL,
  `state` text NULL,
  `city` text NULL,
  `zipcode` text NULL,
  `country` text NULL,
  `address_type` int(10) DEFAULT 2,
  `region` text DEFAULT NULL,
  `region_id` int(10),
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$setup->getTable('specialoccasion/item')};
CREATE TABLE {$setup->getTable('specialoccasion/item')} (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `specialoccasion_id` int(10) NULL,
  `product_id` varchar(255) NOT NULL,
  `qty` int(10) NULL,
  `origin_params` text DEFAULT NULL,
  `option_params` text DEFAULT NULL,
  `occasion` VARCHAR(255) DEFAULT NULL,
  `ship_date` datetime NULL,
  `message` VARCHAR(255) NULL,
  `created_at` datetime NULL,
  `updated_at` datetime NULL,
  `status` tinyint NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$setup->getTable('specialoccasion/history')};
CREATE TABLE {$setup->getTable('specialoccasion/history')} (
  `history_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) NOT NULL,
  `customer_name` VARCHAR (255) NOT NULL,
  `customer_email` VARCHAR (255) NOT NULL,
  `order_id` int(10) NULL,
  `order_increment_id` int(10) NULL,
  `products` text NULL,
  `points` FLOAT NULL,
  `cost` FLOAT NULL,
  `updated_at` datetime NULL,
  `created_at` datetime NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
        $installer->endSetup();
        echo "success";
    }

    public function updateDb2Action()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
          ALTER TABLE {$setup->getTable('specialoccasion/item')} ADD `state` tinyint DEFAULT 1;
		");
        $installer->endSetup();
        echo "success";
    }
}
