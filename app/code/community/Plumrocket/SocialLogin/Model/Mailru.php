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


class Plumrocket_SocialLogin_Model_Mailru extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'mailru';
	
    protected $_url = 'https://connect.mail.ru/oauth/authorize';

	protected $_fields = array(
					'user_id' => 'uid',
		            'firstname' => 'first_name',
		            'lastname' => 'last_name',
		            'email' => 'email',
		            'dob' => 'birthday',
                    'gender' => 'sex',
                    'photo' => 'pic_128',
				);

    protected $_dob = array('day', 'month', 'year', '.');
    protected $_gender = array('0', '1');

	protected $_buttonLinkParams = array();

    protected $_popupSize = array(650, 350);

	public function _construct()
    {      
        parent::_construct();
        
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
            'grant_type'    => 'authorization_code',
        );
    
        $token = null;
        if($response = $this->_callPost('https://connect.mail.ru/oauth/token', $params)) {
            $token = json_decode($response, true);
        }
        $this->_setLog($token, true);

        if (isset($token['access_token'])) {
            $sign = md5("app_id={$this->_applicationId}method=users.getInfosecure=1session_key={$token['access_token']}{$this->_secret}");

            $params = array(
                'method'       => 'users.getInfo',
                'secure'       => '1',
                'app_id'       => $this->_applicationId,
                'session_key'  => $token['access_token'],
                'sig'          => $sign
            );
    
            if($response = $this->_call('http://www.appsmail.ru/platform/api', $params)) {
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
    	if(empty($data[0]['uid'])) {
    		return false;
    	}

        $data = $data[0];

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