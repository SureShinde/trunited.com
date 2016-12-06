<?php
class Mage_Smtp_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getTransport()
    {
	$config = array(
			'port' => Mage::getStoreConfig('smtp/settings/port')
		);
		$config_auth = Mage::getStoreConfig('smtp/settings/auth');
		if ($config_auth != 'none')
		{
			$config['auth'] = $config_auth;
			$config['username'] = Mage::getStoreConfig('smtp/settings/username');
			$config['password'] = Mage::getStoreConfig('smtp/settings/password');
		}
		if (Mage::getStoreConfig('smtp/settings/ssl')!= 0)
		{
			$config['ssl'] = (Mage::getStoreConfig('smtp/settings/ssl') == 1) ? 'tls' :'ssl';
		}
		$transport = new Zend_Mail_Transport_Smtp(Mage::getStoreConfig('smtp/settings/host'), $config);
		return $transport;
    }
}

