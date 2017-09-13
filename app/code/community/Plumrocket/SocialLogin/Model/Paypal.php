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


class Plumrocket_SocialLogin_Model_Paypal extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'paypal';
	
	protected $_responseType = 'code';
    protected $_url = 'https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize';
    // protected $_url = 'https://www.sandbox.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize'; // Sandbox

	protected $_fields = array(
					'user_id' => 'user_id',
		            'firstname' => 'given_name',
		            'lastname' => 'family_name',
		            'email' => 'email',
		            'dob' => 'birthday',
                    'gender' => 'gender', // empty
                    'photo' => 'picture', // empty
				);

    protected $_dob = array('year', 'month', 'day', '-');
    protected $_gender = array('male', 'female');

	protected $_buttonLinkParams = array(
                'scope' => 'openid profile email address phone https://uri.paypal.com/services/paypalattributes'
            );

    protected $_popupSize = array(450, 520);

	public function _construct()
    {      
        parent::_construct();
        
        $this->_buttonLinkParams = array_merge($this->_buttonLinkParams, array(
            'client_id'     => $this->_applicationId,
            'redirect_uri'  => $this->_redirectUri,
            'response_type' => $this->_responseType,
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
        if($response = $this->_call('https://api.paypal.com/v1/identity/openidconnect/tokenservice', $params)) {
            $token = json_decode($response, true);
        }
        $this->_setLog($token, true);
    
        if (isset($token['access_token'])) {
            $params = array(
                'access_token' => $token['access_token'],
                'schema' => 'openid',
            );
    
            if($response = $this->_call('https://api.paypal.com/v1/identity/openidconnect/userinfo/', $params)) {
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

        return parent::_prepareData($data);
    }

}