<?php

class Trunited_AdvertiserProductImport_Model_Import
{
    /* @var $resource Mage_Core_Model_Resource */
    protected $resource;
    protected $connection;
    protected $magentoConnection;
    protected $table_map;

    const ADVERTISER_TABLE = 'tr_linkshare_advertisers';
    const PRODUCT_FEED_TABLE_PREFIX = 'tr_linkshare_feed_';
    const FTP_CRON_OFF = 0;
    const FTP_CRON_ON = 1;
    const FTP_CRON_UNAVAILABLE = 2;
    const PRODUCT_FEED_LOOP_LIMIT = 10;
    const DEFAULT_CATEGORY_ID = 76;
    const PRODUCT_TYPE = "globalbrand";

    protected $mapping = array(
        // magento          => ftp file
        'name'              => 'product_name',
        'description'       => 'long_product_description',
        'short_description' => 'short_product_description',
        'external_url'      => 'product_url',
        'external_image_url'=> 'product_image_url',
        'mpn'               => 'manufacturer_part_number'
    );
    protected $website_ids = array(1);
    protected $attribute_set_id = 4;


    public function run()
    {
        $connection = $this->getConnection();
        if(!$connection){
            return false;
        }
        if($this->isFtpFileCronRun() != self::FTP_CRON_OFF){
            return false;
        }
        $advertisers = $this->getAdvertisers();
        if(!$advertisers){
            return false;
        }
        foreach($advertisers as $advertiser){
            $advertiser_id = $advertiser['id'];
            // set for test
            if($advertiser_id != 1110){
                continue;
            }
            $this->importAdvertiser($advertiser_id);
        }
    }

    // TODO: INT

    public function getConnection()
    {
        $resource = $this->getResource();
        if(!$resource){
            return ;
        }
        if(!$this->connection){
            $this->connection = $resource->getConnection('truinted_api');
        }
        return $this->connection;
    }

    public function getResource()
    {
        if(!$this->resource){
            $this->resource = Mage::getSingleton('core/resource');
        }
        return $this->resource;
    }

    public function getMagentoConnection()
    {
        $resource = $this->getResource();
        if(!$resource){
            return ;
        }
        if(!$this->magentoConnection){
            $this->magentoConnection = $resource->getConnection(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
        }
        return $this->magentoConnection;
    }

    public function getTableMap()
    {
        if(!$this->table_map){
            $resource = $this->getResource();
            $this->table_map = $resource->getTableName('truinted_api/map');
        }
        return $this->table_map;
    }

    // TODO: ADVERTISER DATABASE

    public function geAdvertiserFeedTableName($advertiser_id)
    {
        return self::PRODUCT_FEED_TABLE_PREFIX . $advertiser_id;
    }

    public function isFtpFileCronRun()
    {
        $connection = $this->getConnection();
        $row = $connection->fetchRow("SELECT * FROM `" . self::ADVERTISER_TABLE . "` LIMIT 1");
        if(!$row){
            return self::FTP_CRON_UNAVAILABLE;
        }
        $is_cron_run = $row['is_cron_run'];
        return $is_cron_run ? self::FTP_CRON_ON : self::FTP_CRON_OFF;
    }

    public function getAdvertisers()
    {
        $connection = $this->getConnection();
        $rows = $connection->fetchAll("SELECT * FROM `" . self::ADVERTISER_TABLE . "`");
        return $rows;
    }

    // TODO: IMPORT

    public function importAdvertiser($advertiser_id)
    {
        $connection = $this->getConnection();
        $advertiser_feed_table = $this->geAdvertiserFeedTableName($advertiser_id);
        if(!$connection->isTableExists($advertiser_feed_table)){
           return false;
        }
        $feeds = $this->getAdvertiserFeed($advertiser_id);
        while($feeds){
            foreach($feeds as $feed){
                $result_import = $this->importAdvertiserFeed($feed);
                if($result_import){
                    $this->deleteAdvertiserFeed($advertiser_feed_table, $feed['product_id']);
                } 
				break; //limit 1
            }	
			// limit 1
            //$feeds = $this->getAdvertiserFeed($advertiser_id);
			$feeds = null;
        }
    }

    public function getAdvertiserFeed($advertiser_id)
    {
        $connection = $this->getConnection();
        $advertiser_feed_table = $this->geAdvertiserFeedTableName($advertiser_id);
        if(!$connection->isTableExists($advertiser_feed_table)){
            return false;
        }
        $feeds = $connection->fetchAll("SELECT * FROM `" . $advertiser_feed_table . '` LIMIT ' . self::PRODUCT_FEED_LOOP_LIMIT);
        return $feeds;
    }

    public function deleteAdvertiserFeed($advertiser_feed_table, $feed_product_id)
    {
        $connection = $this->getConnection();
        $result = $connection->query("DELETE FROM `" . $advertiser_feed_table . "` WHERE `product_id` = ?", array($feed_product_id));
        return $result;
    }

    public function importAdvertiserFeed($feed)
    {
        $result = true;
        $magentoConnection = $this->getMagentoConnection();
        $table_map = $this->getTableMap();
        $advertiser_id = $feed['advertiser_id'];
        $feed_product_id = $feed['product_id'];
        $map_exists = $magentoConnection->fetchRow("SELECT * FROM `" . $table_map . "` WHERE advertiser_id = ? AND tr_product_id = ?", array($advertiser_id, $feed_product_id));
        if($map_exists){
            $modification = $feed['modification'];
            $magento_product_id = $map_exists['product_id'];
            if($modification == 'D'){
                try {
                    /* @var $product Mage_Catalog_Model_Product */
                    $product = Mage::getModel('catalog/product');
                    $product->load($magento_product_id);
                    $product->delete();
                    $result = true;
                    $magentoConnection->query("DELETE FROM `" . $table_map . "` WHERE `product_id` = ?", array($magento_product_id));
                } catch (Exception $e){

                }
            } else {
                $product_data = $this->convertFeedToProduct($feed);
                try {
                    /* @var $product Mage_Catalog_Model_Product */
                    $product = Mage::getModel('catalog/product');
                    $product->load($magento_product_id);
                    $product->addData($product_data);
                    $product->save();
                    $result = true;
                } catch (Exception $e){
//                echo $e->getMessage(); exit;
                    // do nothing
                }
            }
        } else {
            try {
                $product_data = $this->convertFeedToProduct($feed, true);
                /* @var $product Mage_Catalog_Model_Product */
                $product = Mage::getModel('catalog/product');
                $product->addData($product_data);
                $product->save();
                $product_id = $product->getId();
                $magentoConnection->query("INSERT INTO `" . $table_map . "` (`advertiser_id`, `tr_product_id`, `product_id`) VALUES (?, ?, ?)", array($advertiser_id, $feed_product_id, $product_id));
                $result = true;
            } catch (Exception $e){
//                echo $e->getMessage(); exit;
                // do nothing
            }
        }
        return $result;
    }

    public function convertFeedToProduct($feed, $is_new = false)
    {
        $data = array();
        foreach($this->mapping as $magento_key => $feed_key){
            $data[$magento_key] = isset($feed[$feed_key]) ? $feed[$feed_key] : '';
        }
        $default_data = array(
            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'weight' => 1,
            'tax_class_id' => 0,
            'stock_data' => array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 0,
            )
        );
        $data = array_merge($default_data, $data);

        $data['sku'] = $feed['advertiser_id'] . "_" . $feed['sku_number'];
        $data['category_ids'] = array(self::DEFAULT_CATEGORY_ID);
		$data['website_ids'] = $this->website_ids;
        $data['type_id'] = self::PRODUCT_TYPE;
        $data['attribute_set_id'] = $this->attribute_set_id;
        if($is_new){
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['status'] = 1;
        }
        $data['price'] = $feed['sale_price'] > 0 ? $feed['sale_price'] : $feed['retail_price'];
		$data['first_period_price'] = $data['price'];
        return $data;
    }
}
