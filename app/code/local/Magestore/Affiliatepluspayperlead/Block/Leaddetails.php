<?php

class Magestore_Affiliatepluspayperlead_Block_Leaddetails extends Mage_Core_Block_Template {

    public function _getHelper() {
        return Mage::helper('affiliatepluspayperlead/config');
    }

    public function getPayperleadDescription() {
        return $this->_getHelper()->getPayperleadConfig('description', Mage::app()->getStore()->getId());
    }

    public function getAccount() {
        return Mage::getSingleton('affiliateplus/session')->getAccount();
    }

    public function getAccountEmail() {
        return $this->getAccount()->getEmail();
    }

    public function getPayperleadCustomUrl() {
        if (!$this->hasData('payperlead_custom_url')) {
            $this->setData('payperlead_custom_url', Mage::getSingleton('core/session')->getPayperleadUrl());
            Mage::getSingleton('core/session')->setPayperleadUrl(null);
        }
        return $this->getData('payperlead_custom_url');
    }

    public function getPayperleadBaseUrl() {
        $baseUrl = $this->_getHelper()->getPayperleadConfig('base_link');
        if ($baseUrl) {
            $baseUrl = trim($baseUrl, '/') . '/';
            return $baseUrl;
        }
        return $this->getBaseUrl();
    }

    public function getPayperleadPersonalUrl() {
        if (!$this->hasData('payperlead_personal_url')) {
            $idPath = 'affiliatepluspayperlead/' . Mage::app()->getStore()->getId() . '/' . $this->getAccount()->getId();
            $rewrite = Mage::getModel('core/url_rewrite')->load($idPath, 'id_path');
            //Zend_Debug::dump($rewrite->getData());
            if ($rewrite->getId())
                $this->setData('payperlead_personal_url', $this->getUrl(null, array('_direct' => $rewrite->getRequestPath())));
            else
                $this->setData('payperlead_personal_url', Mage::helper('affiliateplus/url')->addAccToUrl($this->getBaseUrl()));
        }
        return $this->getData('payperlead_personal_url');
    }

    public function formatData($date) {
        $intPos = strrpos($date, "-");
        $str1 = substr($date, 0, $intPos);
        $str2 = substr($date, $intPos + 1);
        if (strlen($str2) == 4) {
            $date = $str2 . "-" . $str1;
        }
        return $date;
    }

    public function trashIsEnable() {
        if (!Mage::getConfig()->getNode('modules/Magestore_AffiliateplusTrash')) {
            return false;
        }
        $isActive = Mage::getConfig()->getNode('modules/Magestore_AffiliateplusTrash/active');
        if ($isActive && in_array((string) $isActive, array('true', '1'))) {
            return true;
        }
        return false;
    }

    protected function _construct() {
        parent::_construct();
        $resource = Mage::getModel('core/resource');
        $read = $resource->getConnection('core_read');
        $store_id = Mage::app()->getStore()->getId();
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $collection = Mage::getModel('affiliatepluspayperlead/lead')->getCollection();
        $collection->addFieldToFilter('main_table.account_id', $account->getId())
                // ->addFieldToFilter('store_id',$store_id)
                ->addFieldToFilter('main_table.status', 1)
                ->addFieldToFilter('type', array('in' => array(4, 5)))
                ->setOrder('created_time', 'DESC');

        $balanceIsGlobal = (Mage::getStoreConfig('affiliateplus/account/balance') == 'global');
        $trashIsEnable = $this->trashIsEnable();

        if (Mage::getStoreConfig('affiliateplus/account/balance') == 'store') {
            $collection->addFieldToFilter('store_id', $store_id);
        } elseif (Mage::getStoreConfig('affiliateplus/account/balance') == 'website') {
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        }

        /* Mage::dispatchEvent('affiliateplus_prepare_sales_collection',array(
          'collection'	=> $collection,
          )); */
        $signup = $read->select()
                ->from($resource->getTableName('affiliateplus_transaction'), array('account_id', "number_account_signup" => "COUNT(customer_email)", 'date_created' => 'date(created_time)'))
                ->where('type=4 AND status=1 AND account_id=' . $account->getId())
                ->group('date(created_time)');
        /* edit by blanka */
        if (Mage::getStoreConfig('affiliateplus/account/balance') == 'store') {
            $signup->where('store_id = ?', $store_id);
        } elseif (Mage::getStoreConfig('affiliateplus/account/balance') == 'website') {
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $stIds = '(';
            foreach ($storeIds as $id) {
                $stIds .= $id . ',';
            }
            $stIds = trim($stIds, ',');
            $stIds .= ')';
            $signup->where('store_id in ' . $stIds);
        }
        /* end edit */
        if ($trashIsEnable) {
            $signup->where('transaction_is_deleted = ?', 0);
        }

        $subcribe = $read->select()
                ->from($resource->getTableName('affiliateplus_transaction'), array('account_id', "number_customer_subcribe" => "COUNT(customer_email)", 'date_created' => 'date(created_time)'))
                ->where('type=5 AND status=1 AND account_id=' . $account->getId())
                ->group('date(created_time)');
        /* edit by blanka */
        if (Mage::getStoreConfig('affiliateplus/account/balance') == 'store') {
            $subcribe->where('store_id = ?', $store_id);
        } elseif (Mage::getStoreConfig('affiliateplus/account/balance') == 'website') {
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $stIds = '(';
            foreach ($storeIds as $id) {
                $stIds .= $id . ',';
            }
            $stIds = trim($stIds, ',');
            $stIds .= ')';
            $signup->where('store_id in ' . $stIds);
        }
        /* blanka */

        if ($trashIsEnable) {
            $subcribe->where('transaction_is_deleted = ?', 0);
        }

        $collection->getSelect()
                ->joinLeft(array('signup_table' => new Zend_Db_Expr("({$signup->assemble()})")), 'main_table.account_id = signup_table.account_id AND date(main_table.created_time)=date(signup_table.date_created)', array('number_account_signup' => 'if(signup_table.number_account_signup IS NULL,0,signup_table.number_account_signup)'))
                ->joinLeft(array('subcribe_table' => new Zend_Db_Expr("({$subcribe->assemble()})")), 'main_table.account_id = subcribe_table.account_id AND date(main_table.created_time)=date(subcribe_table.date_created)', array('number_customer_subcribe' => 'if(subcribe_table.number_customer_subcribe IS NULL,0,subcribe_table.number_customer_subcribe)'))
                ->columns('SUM(main_table.commission) AS commission_total')
                ->group('date(created_time)')
        ;
        if ($fromDate = $this->getRequest()->getParam('from-date'))
            $collection->addFieldToFilter('date(created_time)', array('gteq' => $this->formatData($fromDate)));
        if ($toDate = $this->getRequest()->getParam('to-date'))
            $collection->addFieldToFilter('date(created_time)', array('lteq' => $this->formatData($toDate)));
        $this->setCollection($collection);
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'lead_pager')
                ->setTemplate('affiliateplus/html/pager.phtml')
                ->setCollection($this->getCollection());
        $this->setChild('lead_pager', $pager);

        $grid = $this->getLayout()->createBlock('affiliateplus/grid', 'lead_grid');

        // prepare column
        $grid->addColumn('created_time', array(
            'header' => $this->__('Date'),
            'index' => 'created_time',
            'type' => 'date',
            'format' => 'medium',
            'align' => 'left',
            'searchable' => true,
        ));

        $grid->addColumn('number_account_signup', array(
            'header' => $this->__('Total new account'),
            'index' => 'number_account_signup',
            'align' => 'left',
        ));

        $grid->addColumn('number_customer_subcribe', array(
            'header' => $this->__('Total new subscription'),
            'align' => 'left',
            'index' => 'number_customer_subcribe'
        ));

        $grid->addColumn('commission_total', array(
            'header' => $this->__('Total Commission'),
            'align' => 'left',
            'type' => 'baseprice',
            'index' => 'commission_total'
        ));


        Mage::dispatchEvent('affiliateplus_prepare_sales_columns', array(
            'grid' => $grid,
        ));

        /* $grid->addColumn('status',array(
          'header'	=> $this->__('Status'),
          'align'		=> 'left',
          'index'		=> 'status',
          'type'		=> 'options',
          'options'	=> array(
          1	=> $this->__('Complete'),
          2	=> $this->__('Pending'),
          3	=> $this->__('Cancel')
          )
          )); */

        $this->setChild('lead_grid', $grid);
        return $this;
    }

    public function getGridHtml() {
        return $this->getChildHtml('lead_grid');
    }

    public function getPagerHtml() {
        return $this->getChildHtml('lead_pager');
    }

    protected function _toHtml() {
        $this->getChild('lead_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

    public function getStatisticInfo() {
        $accountId = Mage::getSingleton('affiliateplus/session')->getAccount()->getId();
        $storeId = Mage::app()->getStore()->getId();
        $scope = Mage::getStoreConfig('affiliateplus/account/balance', $storeId);
        $totalCommission = 0;
        $number = 0;
        $collection = $this->getCollection();
        /* edit by blanka */
        if ($storeId && $scope == 'store')
            $collection->addFieldToFilter('store_id', $storeId);
        elseif ($scope == 'website') {
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        }
        /* end edit */
        foreach ($collection as $item) {
            $number += $item->getNumberAccountSignup();
            $number += $item->getNumberCustomerSubcribe();
            $totalCommission += $item->getCommissionTotal();
        }
        return array(
            'number_commission' => $number,
            'transactions' => $this->__('Lead Transactions'),
            'commissions' => $totalCommission,
            'earning' => $this->__('Lead Earnings')
        );
    }

}
