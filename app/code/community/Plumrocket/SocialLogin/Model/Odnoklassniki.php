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
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_SocialLogin_Model_Odnoklassniki extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'odnoklassniki';
	
    protected $_url = 'http://www.odnoklassniki.ru/oauth/authorize';
    protected $_publicKey = null;

	protected $_fields = array(
					'user_id' => 'uid',
		            'firstname' => 'first_name',
		            'lastname' => 'last_name',
		            'email' => 'email',
		            'dob' => 'birthday',
                    'gender' => 'gender',
                    'photo' => 'pic_1',
				);

    protected $_dob = array('year', 'month', 'day', '-');
    protected $_gender = array('male', 'female');

	protected $_buttonLinkParams = array(
					// 'scope' => 'VALUABLE ACCESS',
				);

    protected $_popupSize = array(650, 350);

	public function _construct()
    {      
        parent::_construct();

        $this->_publicKey = trim(Mage::getStoreConfig('pslogin/'. $this->_type .'/public_key'));
        
        $this->_buttonLinkParams = array_merge($this->_buttonLinkParams, array(
            'client_id'     => $this->_applicationId,
            'redirect_uri'  => $this->_redirectUri,
            'response_type' => $this->_responseType
        ));
    }

    public function loadUserData($response)
    {
    	if(empty($response)) {
            return false;
        }

        $data = array();

        $params = array(
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'code' => $response,
            'redirect_uri' => $this->_redirectUri,
            'grant_type' => 'authorization_code',
        );
        
        $token = null;
        if($response = $this->_callPost('http://api.odnoklassniki.ru/oauth/token.do', $params)) {
            $token = json_decode($response, true);
        }
        $this->_setLog($token, true);

        if (isset($token['access_token']) && !empty($this->_publicKey)) {
            $sign = md5("application_key={$this->_publicKey}format=jsonmethod=users.getCurrentUser" . md5("{$token['access_token']}{$this->_secret}"));

            $params = array(
                'method'          => 'users.getCurrentUser',
                'access_token'    => $token['access_token'],
                'application_key' => $this->_publicKey,
                'format'          => 'json',
                'sig'             => $sign
            );

            if($response = $this->_call('http://api.odnoklassniki.ru/fb.do', $params)) {
                $data = json_decode($response, true);
            }
            $this->_setLog($data, true);
        }
 
        if(!$this->_userData = $this->_prepareData($data)) {
        	return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    protected function _prepareData($data)
    {
    	if(empty($data['uid'])) {
    		return false;
    	}

        return parent::_prepareData($data);
    }

    protected function _callPost($url, $params = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

}