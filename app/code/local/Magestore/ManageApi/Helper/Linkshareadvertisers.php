<?php

class Magestore_ManageApi_Helper_Linkshareadvertisers extends Mage_Core_Helper_Abstract
{
    public function getHelperData()
    {
        return Mage::helper('manageapi');
    }

    public function processAPI($url, $start_date)
    {
        $data = null;

        $_data = $this->getHelperData()->getContentByCurl($url, $this->getParamsHeader());
        $refresh_token = null;
        $new_token_request = null;
        if(strpos($_data, 'Token Expired') > 0 || strpos($_data, 'Token Inactive') > 0){
            $refresh_token_data = $this->getRefreshToken();
            $refresh_data = json_decode($refresh_token_data, true);

            if(is_array($refresh_data) && isset($refresh_data['refresh_token'])){
                $refresh_token = $refresh_data['refresh_token'];
            }

            if($refresh_token != null)
            {
                $new_token_request_data = $this->getNewTokenRequest($refresh_token);
                $token_request_data = json_decode($new_token_request_data, true);

                if(is_array($token_request_data) && isset($token_request_data['refresh_token'])){
                    $new_token_request = $token_request_data['access_token'];
                }

                if($new_token_request != null) {
                    $_data = $this->getHelperData()->getContentByCurl($url, $this->getParamsHeader('Bearer '.$new_token_request));
                }
            }
        }

        $data = json_decode($_data, true);

        if ($data != null && is_array($data) && sizeof($data) > 0) {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            try {
                $connection->beginTransaction();

                foreach ($data as $sale) {
                    $model = Mage::getModel('manageapi/linkshareadvertisers');
                    $sale['created_time'] = now();
                    $sale['process_date'] = date('Y-m-d H:i:s', strtotime($sale['process_date']));
                    $sale['transaction_date'] = date('Y-m-d H:i:s', strtotime($sale['transaction_date']));
                    $model->setData($sale);

                    $customer = Mage::getModel('customer/customer')->load($sale['u1']);
                    if($customer != null && $customer->getId() && ceil($sale['commissions']) > 0){
                        Mage::helper('rewardpoints/action')->addTransaction('global_brand', $customer, new Varien_Object(array(
                                'product_credit_title' => 0,
                                'product_credit' => 0,
                                'point_amount' =>ceil($sale['commission']),
                                'title' => Mage::helper('manageapi')->__('Points awarded for LinkShare Advertisers: %s on %s', $sale['product_name'], $start_date),
                                'expiration_day' => 0,
                                'expiration_day_credit' => 0,
                                'is_on_hold' => 1,
                                'created_time' => now(),
                                'order_increment_id' => $sale['order_id']
                            ))
                        );
                    }
                    $transactionSave->addObject($model);
                }
                Mage::log('LINK SHARE ADVERTISERS at '.date('Y-m-d H:i:s', time()).' - Result:'.sizeof($data).' - URL: '.$url, null, 'check_manage_api.log');

                $transactionSave->save();
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollback();
            }
        } else {
            $error_message = '';
            $errors = json_decode(json_encode((array)$_data), 1);
            $error_message .= $errors;
            Mage::getSingleton('adminhtml/session')->addError('LINK SHARE ADVERTISERS API: '.$error_message);
        }
    }

    public function processCron()
    {
        $enable = $this->getHelperData()->getDataConfig('enable', 'link_share_advertisers');
        if ($enable) {
            $url = $this->getHelperData()->getDataConfig('link_share_advertiser_api', 'link_share_advertisers');
            if ($url != null) {
                $start_date = date('Y-m-d H:i:s', time());
                $end_data = date('Y-m-d H:i:s', (time() - 3600));
                $_url = str_replace(array('{{start_date}}', '{{end_date}}'), array($start_date, $end_data), $url);
                $this->processAPI($_url, $start_date);
            }
        }
    }

    public function getParamsHeader($authorize_token = null)
    {
        if($authorize_token == null)
            $authorize_token = $this->getHelperData()->getDataConfig('token_request_authorization', 'link_share_advertisers');

        return array(
            "Authorization: $authorize_token",
            "Accept: text/json"
        );
    }

    public function getRefreshToken()
    {
        $token_request = $this->getHelperData()->getDataConfig('token_request', 'link_share_advertisers');
        $url = $this->getHelperData()->getDataConfig('url_request_token', 'link_share_advertisers');
        $data = $this->getHelperData()->getContentByCurl(
            $url,
            array(
                "Authorization: $token_request",
                "Accept: text/json"
            ),
            true,
            array(
                'grant_type' => 'password',
                'username' => $this->getHelperData()->getDataConfig('username', 'link_share_advertisers'),
                'password' => $this->getHelperData()->getDataConfig('password', 'link_share_advertisers'),
                'scope' => $this->getHelperData()->getDataConfig('site_id', 'link_share_advertisers'),
            )
        );

        return $data;
    }

    public function getNewTokenRequest($refresh_token)
    {
        $token_request = $this->getHelperData()->getDataConfig('token_request', 'link_share_advertisers');
        $url = $this->getHelperData()->getDataConfig('url_request_token', 'link_share_advertisers');
        $data = $this->getHelperData()->getContentByCurl(
            $url,
            array(
                "Authorization: $token_request",
                "Accept: text/json"
            ),
            true,
            array(
                'grant_type' => 'refresh_token',
                'refresh_token' => $refresh_token,
                'scope' => $this->getHelperData()->getDataConfig('site_id', 'link_share_advertisers'),
            )
        );

        return $data;
    }
}