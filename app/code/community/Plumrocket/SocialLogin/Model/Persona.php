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


class Plumrocket_SocialLogin_Model_Persona extends Plumrocket_SocialLogin_Model_Account
{
	protected $_type = 'persona';
    protected $_protocol = 'BrowserID';

    protected $_responseType = 'assertion';

	protected $_fields = array(
					'user_id' => 'email',
		            'firstname' => 'firstname', // empty
		            'lastname' => 'lastname', // empty
		            'email' => 'email',
		            'dob' => 'birthday', // empty
                    'gender' => 'gender', // empty
                    'photo' => 'photo', // empty
				);

	public function _construct()
    {      
        parent::_construct();
    }

    public function loadUserData($response)
    {
        if(empty($response)) {
            return false;
        }

        $data = array();

        $params = array(
            'assertion' => $response,
            'audience' => Mage::getBaseUrl() .':'. $_SERVER['SERVER_PORT'],
        );

        if($response = $this->_call('https://verifier.login.persona.org/verify', $params, 'POST')) {
            $data = json_decode($response, true);
        }
        $this->_setLog($data, true);

        if(!$this->_userData = $this->_prepareData($data)) {
        	return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    protected function _prepareData($data)
    {
    	if(empty($data['status']) || $data['status'] != 'okay' || empty($data['email'])) {
    		return false;
    	}

        return parent::_prepareData($data);
    }

}