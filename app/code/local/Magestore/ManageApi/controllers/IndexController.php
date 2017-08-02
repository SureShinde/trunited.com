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
 * @package     Magestore_TruBox
 * @module     TruBox
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruBox Index Controller
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @author      Magestore Developer
 */
class Magestore_ManageApi_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {
        Mage::helper('manageapi/linkshare')->processCron();
        Mage::helper('manageapi/hotel')->processCron();
        Mage::helper('manageapi/flight')->processCron();
        Mage::helper('manageapi/car')->processCron();
        Mage::helper('manageapi/vacation')->processCron();
        Mage::helper('manageapi/cj')->processCron();
        Mage::helper('manageapi/target')->processCron();

        $this->loadLayout();
        $this->_title(Mage::helper('manageapi')->__('Manage API'));
        $this->renderLayout();
    }

    public function renameDbAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            RENAME TABLE trpriceline_cj_actions to trcj_actions;
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
        ALTER TABLE {$setup->getTable('manageapi/linkshare')} MODIFY `order_id` VARCHAR(255) ;
      ");
      $installer->endSetup();
      echo "success";
    }
        

    //ALTER TABLE {$setup->getTable('trubox/address')} ADD `address_type` int(10) DEFAULT 2;
    public function updateDbAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            DROP TABLE IF EXISTS {$setup->getTable('manageapi/linkshare')};
            CREATE TABLE {$setup->getTable('manageapi/linkshare')} (
              `linkshare_id` int(11) unsigned NOT NULL auto_increment,
              `member_id` VARCHAR(255) NOT NULL,
              `mid` VARCHAR(255) NOT NULL,
              `advertiser_name` varchar(255) NOT NULL,
              `order_id` VARCHAR(255)  NOT NULL,
              `transaction_date` datetime NULL,
              `sku` VARCHAR(255) NULL,
              `sales` FLOAT unsigned,
              `items` VARCHAR(255) NULL,
              `total_commission` FLOAT unsigned,
              `process_date` datetime NULL,
              `created_at` datetime NULL,
              PRIMARY KEY (`linkshare_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            DROP TABLE IF EXISTS {$setup->getTable('manageapi/hotelactions')};
            CREATE TABLE {$setup->getTable('manageapi/hotelactions')} (
              `hotel_actions_id` int(10) unsigned NOT NULL auto_increment,
              `confirmed_subtotal` text NULL DEFAULT '',
              `confirmed_commission` text NULL DEFAULT '',
              `confirmed_fee` text NULL DEFAULT '',
              `confirmed_total_earnings` text NULL DEFAULT '',
              `reconciled_status` text NULL DEFAULT '',
              `invoice_number` text NULL DEFAULT '',
              `confirmed_insurance_commission` text NULL DEFAULT '',
              `insurance_reconciled_status` text NULL DEFAULT '',
              `insurance_invoice_number` text NULL DEFAULT '',
              `reservation_date_time` datetime NULL,
              `refid` VARCHAR(255) NULL,
              `site_name` VARCHAR(255) NULL,
              `member_id` text NULL,
              `accountid` VARCHAR(255) NULL,
              `refclickid` VARCHAR(255) NULL,
              `hotelid` VARCHAR(255) NULL,
              `cityid` VARCHAR(255) NULL,
              `tripid` VARCHAR(255) NULL,
              `ratecat` VARCHAR(255) NULL,
              `portal` VARCHAR(255) NULL,
              `total` FLOAT NULL,
              `sub_total` FLOAT NULL,
              `commission` FLOAT NULL,
              `fee` FLOAT NULL,
              `revenue` FLOAT NULL,
              `booked_currency` VARCHAR(255) NULL,
              `rooms` VARCHAR(255) NULL,
              `city` VARCHAR(255) NULL,
              `hotel_name` VARCHAR(255) NULL,
              `state` VARCHAR(255) NULL,
              `country` VARCHAR(255) NULL,
              `number_of_days` INT(10) NULL,
              `promo` VARCHAR(255) NULL,
              `check_in_date_time` datetime NULL,
              `check_out_date_time` datetime NULL,
              `user_name` VARCHAR(255) NULL,
              `user_middlename` text NULL,
              `user_lastname` VARCHAR(255) NULL,
              `user_email` VARCHAR(255) NULL,
              `user_phone` VARCHAR(255) NULL,
              `user_phone_extension` text NULL,
              `user_address` text NULL,
              `user_location_city` VARCHAR(255) NULL,
              `user_location_state` VARCHAR(255) NULL,
              `user_country` VARCHAR(255) NULL,
              `user_zip` VARCHAR(255) NULL,
              `phn_sale` VARCHAR(255) NULL,
              `mobile_sale` VARCHAR(255) NULL,
              `device` VARCHAR(255) NULL,
              `rate_type` text NULL,
              `chain_code` VARCHAR(255) NULL,
              `insurance_flag` VARCHAR(255) NULL,
              `insured_days` VARCHAR(255) NULL,
              `insurance_commission` VARCHAR(255) NULL,
              `est_insurance_subtotal` VARCHAR(255) NULL,
              `invoice_date` text NULL,
              `pending_commission` text NULL,
              `status` VARCHAR(255) NULL,
              `payment_commission` text NULL,
              `other` text NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`hotel_actions_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            DROP TABLE IF EXISTS {$setup->getTable('manageapi/flightactions')};
            CREATE TABLE {$setup->getTable('manageapi/flightactions')} (
              `flight_actions_id` int(10) unsigned NOT NULL auto_increment,
              `air_offer_id` VARCHAR(255) NULL ,
              `reservation_date_time` datetime NULL ,
              `session_id` VARCHAR(255) NULL ,
              `accountid` VARCHAR(255) NULL ,
              `refid` VARCHAR(255) NULL,
              `site_name` VARCHAR(255) NULL,
              `refclickid` VARCHAR(255) NULL,
              `insurance_flag` VARCHAR(255) NULL,
              `total` FLOAT NULL,
              `sub_total` FLOAT NULL,
              `fee` FLOAT NULL,
              `commission` FLOAT NULL,
              `insurance_commission` VARCHAR(255) NULL,
              `ratecat` VARCHAR(255) NULL,
              `passengers` VARCHAR(255) NULL,
              `insured_passengers` VARCHAR(255) NULL,
              `air_search_type` VARCHAR (255) NULL,
              `start_date_time` datetime NULL ,
              `end_date_time` datetime NULL ,
              `user_name` VARCHAR(255) NULL,
              `user_middlename` text NULL,
              `user_lastname` VARCHAR(255) NULL,
              `user_email` VARCHAR(255) NULL,
              `user_phone` VARCHAR(255) NULL,
              `user_phone_extension` text NULL,
              `user_address` text NULL,
              `user_location_city` VARCHAR(255) NULL,
              `user_location_state` VARCHAR(255) NULL,
              `user_country` VARCHAR(255) NULL,
              `user_zip` VARCHAR(255) NULL,
              `status` VARCHAR(255) NULL,
              `revenue` FLOAT NULL,
              `flights` text NULL,
              `total_insurance` FLOAT NULL,
              `ins_subtotal` FLOAT NULL,
              `affiliate_cut` FLOAT NULL,
              `accounting_sub_total` FLOAT NULL,
              `origin_Airport_name` text NULL,
              `dest_Airport_name` text NULL,
              `origin_City` text NULL,
              `dest_City` text NULL,
              `device` VARCHAR(255) NULL,
              `confirmed_subtotal` text NULL DEFAULT '',
              `confirmed_commission` text NULL DEFAULT '',
              `confirmed_fee` text NULL DEFAULT '',
              `confirmed_total_earnings` text NULL DEFAULT '',
              `reconciled_status` text NULL DEFAULT '',   
              `invoice_number` text NULL DEFAULT '',
              `confirmed_insurance_commission` text NULL DEFAULT '',
              `insurance_reconciled_status` text NULL DEFAULT '',
              `insurance_invoice_number` text NULL DEFAULT '',
              `other` text NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`flight_actions_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            DROP TABLE IF EXISTS {$setup->getTable('manageapi/caractions')};
            CREATE TABLE {$setup->getTable('manageapi/caractions')} (
              `car_actions_id` int(10) unsigned NOT NULL auto_increment,
              `driver_fname` VARCHAR (255) NULL ,
              `driver_lname` VARCHAR (255) NULL ,
              `pickup_locationid` VARCHAR (255) NULL ,
              `dropoff_locationid` VARCHAR (255) NULL ,
              `pickup_time` datetime NULL ,
              `dropoff_time` datetime NULL ,
              `pickup_location_city` text NULL ,
              `pickup_location_state` text NULL ,
              `pickup_location_country` text NULL,
              `dropoff_location_city` text NULL,
              `dropoff_location_state` text NULL,
              `dropoff_location_country` text NULL,
              `affiliate_cut` VARCHAR(255) NULL,
              `date` datetime NULL,
              `revenue` FLOAT NULL,
              `promo_coupon_code` text NULL,
              `confirmed_subtotal` text NULL,
              `confirmed_commission` text NULL,
              `confirmed_fee` text NULL,
              `confirmed_total_earnings` text NULL,
              `reconciled_status` text NULL,
              `invoice_number` text NULL,
              `confirmed_insurance_commission` text NULL,
              `insurance_reconciled_status` text NULL,
              `insurance_invoice_number` text NULL,
              `accountid` VARCHAR(255) NULL,
              `refid` VARCHAR(255) NULL,
              `ratecat` VARCHAR (255) NULL,
              `site_name` VARCHAR (255) NULL,
              `portal` VARCHAR(255) NULL,
              `refclickid` text NULL,
              `requestid` VARCHAR (255) NULL,
              `insurance_flag` VARCHAR (255) NULL,
              `total` FLOAT NULL,
              `sub_total` FLOAT NULL,
              `tax` FLOAT NULL,
              `insured_days` VARCHAR(255) NULL,
              `currency` VARCHAR (255) NULL,
              `user_name` VARCHAR(255) NULL,
              `user_middlename` text NULL,
              `user_lastname` VARCHAR(255) NULL,
              `user_email` VARCHAR(255) NULL,
              `user_phone` VARCHAR(255) NULL,
              `user_phone_extension` text NULL,
              `user_address` text NULL,
              `user_location_city` VARCHAR(255) NULL,
              `user_location_state` VARCHAR(255) NULL,
              `user_country` VARCHAR(255) NULL,
              `user_zip` VARCHAR(255) NULL,
              `car_type` VARCHAR(255) NULL,
              `company_name` VARCHAR(255) NULL,
              `company_code` VARCHAR(255) NULL,
              `num_days` VARCHAR(255) NULL,
              `pickup_location` VARCHAR(255) NULL,
              `dropoff_location` VARCHAR(255) NULL,
              `bookingid` VARCHAR(255) NULL,
              `tripid` VARCHAR(255) NULL,
              `newsletter_optin` VARCHAR(255) NULL,
              `device` VARCHAR (255) NULL,
              `ins_subtotal` text NULL,
              `insurance_commission` text NULL,
              `accounting_aux_value` text NULL,
              `fee` FLOAT NULL,
              `commission` FLOAT NULL,
              `rate_type` VARCHAR (255) NULL,
              `phn_sale` VARCHAR(255) NULL,
              `member_id` text NULL,
              `invoice_date` text NULL,
              `pending_commission` text NULL,
              `status` VARCHAR (255) NULL,
              `payment_commission` text NULL,
              `other` text NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`car_actions_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            DROP TABLE IF EXISTS {$setup->getTable('manageapi/vacationactions')};
            CREATE TABLE {$setup->getTable('manageapi/vacationactions')} (
              `vacation_actions_id` int(10) unsigned NOT NULL auto_increment,
              `date` datetime NULL ,
              `car_driver_fname` text NULL ,
              `car_driver_lname` text NULL ,
              `car_pickup_locationid` text NULL ,
              `car_dropoff_locationid` text NULL ,
              `car_pickup_datetime` text NULL ,
              `car_dropoff_datetime` text NULL ,
              `car_pickup_location_city` text NULL ,
              `car_pickup_location_state` text NULL ,
              `car_pickup_location_country` text NULL ,
              `car_dropoff_location_city` text NULL ,
              `car_dropoff_location_state` text NULL ,
              `car_dropoff_location_country` text NULL ,
              `flights` text NULL ,
              `insurance_fee` VARCHAR(255) NULL ,
              `orig_airport_code` VARCHAR (255) NULL ,
              `dest_airport_code` VARCHAR (255) NULL ,
              `hotel_city` text NULL ,
              `hotel_name` VARCHAR (255) NULL ,
              `hotel_state` text NULL ,
              `hotel_country` text NULL ,
              `confirmed_subtotal` text NULL,
              `confirmed_commission` text NULL,
              `confirmed_fee` text NULL,
              `confirmed_total_earnings` text NULL,
              `reconciled_status` text NULL,
              `invoice_number` text NULL,
              `confirmed_insurance_commission` text NULL,
              `insurance_reconciled_status` text NULL,
              `insurance_invoice_number` text NULL,
              `accountid` VARCHAR(255) NULL,
              `refid` VARCHAR(255) NULL,
              `site_name` VARCHAR (255) NULL,
              `portal` VARCHAR(255) NULL,
              `refclickid` text NULL,
              `total` FLOAT NULL,
              `sub_total` FLOAT NULL,
              `process_fee` FLOAT NULL,
              `commission` FLOAT NULL,
              `commission_fee` FLOAT NULL,
              `currency` VARCHAR (255) NULL,
              `accounting_currency` VARCHAR (255) NULL,
              `user_name` VARCHAR(255) NULL,
              `user_middlename` text NULL,
              `user_lastname` VARCHAR(255) NULL,
              `user_email` VARCHAR(255) NULL,
              `user_phone` VARCHAR(255) NULL,
              `user_phone_extension` text NULL,
              `user_address` text NULL,
              `user_location_city` VARCHAR(255) NULL,
              `user_location_state` VARCHAR(255) NULL,
              `user_country` VARCHAR(255) NULL,
              `user_zip` VARCHAR(255) NULL,
              `tripid` VARCHAR(255) NULL,
              `depart_date_time` datetime NULL,
              `return_date_time` datetime NULL,
              `check_in_date_time` datetime NULL,
              `check_out_date_time` datetime NULL,
              `air_city_id` VARCHAR(255) NULL,
              `car` VARCHAR(255) NULL,
              `device` VARCHAR(255) NULL,
              `rate_type` text NULL,
              `member_id` text NULL,
              `rooms` VARCHAR(255) NULL,
              `hotelid` VARCHAR(255) NULL,
              `cityid` VARCHAR(255) NULL,
              `invoice_date` text NULL,
              `pending_commission` text NULL,
              `status` VARCHAR(255) NULL,
              `payment_commission` text NULL,
              `other` text NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`vacation_actions_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            DROP TABLE IF EXISTS {$setup->getTable('manageapi/cjactions')};
            CREATE TABLE {$setup->getTable('manageapi/cjactions')} (
              `cj_actions_id` int(10) unsigned NOT NULL auto_increment,
              `action_status` VARCHAR(255) NULL,
              `action_type` VARCHAR(255) NULL,
              `aid` VARCHAR(255) NULL,
              `commission_id` VARCHAR(255) NULL,
              `country` text NULL,
              `event_date` datetime NULL,
              `locking_date` datetime NULL,
              `order_id` VARCHAR(255) NULL,
              `original` VARCHAR(255) NULL,
              `original_action_id` VARCHAR(255) NULL,
              `posting_date` datetime NULL,
              `website_id` VARCHAR(255) NULL,
              `action_tracker_id` VARCHAR(255) NULL,
              `action_tracker_name` VARCHAR(255) NULL,
              `cid` VARCHAR(255) NULL,
              `advertiser_name` VARCHAR(255) NULL,
              `commission_amount` FLOAT NULL,
              `order_discount` FLOAT NULL,
              `sid` VARCHAR(255) NULL,
              `sale_amount` FLOAT NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`cj_actions_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            DROP TABLE IF EXISTS {$setup->getTable('manageapi/targetactions')};
            CREATE TABLE {$setup->getTable('manageapi/targetactions')} (
              `target_actions_id` int(10) unsigned NOT NULL auto_increment,
              `referral_date` text NULL,
              `action_date` datetime NULL,
              `locking_date` text NULL,
              `adj_date` text NULL,
              `scheduled_clearing_date` text NULL,
              `action_id` VARCHAR(255) NULL,
              `campaign` VARCHAR(255) NULL,
              `action_tracker` VARCHAR(255) NULL,
              `status` VARCHAR(255) NULL,
              `status_detail` text NULL,
              `category_list` text NULL,
              `sku` text NULL,
              `item_name` text NULL,
              `category` text NULL,
              `quantity` text NULL,
              `sale_amount` FLOAT NULL,
              `original_sale_amount` text NULL,
              `payout` FLOAT NULL,
              `original_payout` FLOAT NULL,
              `vat` FLOAT NULL,
              `promo_code` text NULL,
              `ad` text NULL,
              `referring_url` text NULL,
              `referring_type` text NULL,
              `ip_address` text NULL,
              `geo_location` text NULL,
              `subid1` VARCHAR(255) NULL,
              `subid2` text NULL,
              `subid3` text NULL,
              `sharedid` text NULL,
              `date1` text NULL,
              `date2` text NULL,
              `paystub` text NULL,
              `device` text NULL,
              `os` text NULL,
              `user_agent` text NULL,
              `created_time` datetime NULL,
              PRIMARY KEY (`target_actions_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            
		");
        $installer->endSetup();
        echo "success";
    }


}
