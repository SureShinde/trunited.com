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


class Plumrocket_SocialLogin_Model_Salesforce extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'salesforce';
	
    protected $_url = 'https://login.salesforce.com/services/oauth2/authorize';

	protected $_fields = array(
					'user_id' => 'user_id',
		            'firstname' => 'first_name',
		            'lastname' => 'last_name',
		            'email' => 'email',
		            'dob' => 'birthday', // empty
                    'gender' => 'gender', // empty
                    'photo' => 'photos_thumbnail',
				);

    protected $_gender = array('male', 'female');

	protected $_buttonLinkParams = array();

    protected $_popupSize = array(400, 650);

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
            'grant_type' => 'authorization_code',
        );
    
        $token = null;
        if($response = $this->_callPost('https://na1.salesforce.com/services/oauth2/token', $params)) {
            $token = json_decode($response, true);
        }
        $this->_setLog($token, true);

        if (isset($token['access_token']) && isset($token['id'])) {
            $params = array(
                'access_token' => $token['access_token'],
                'format' => 'json',
            );
    
            if($response = $this->_call($token['id'], $params)) {
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
    	if(empty($data['user_id'])) {
    		return false;
    	}

        // Photo.
        if(!empty($data['photos']['thumbnail'])) {
            $data['photos_thumbnail'] = $data['photos']['thumbnail'];
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