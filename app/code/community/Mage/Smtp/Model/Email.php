<?php
class Mage_Smtp_Model_Email extends Mage_Core_Model_Email
{
    public function send()
    {
		if (!Mage::getStoreConfig('smtp/settings/enabled'))
		{
			return parent::send();
		}
		
        if (Mage::getStoreConfigFlag('system/smtp/disable')) {
            return $this;
        }
		
        $mail = new Zend_Mail();

        if (strtolower($this->getType()) == 'html') {
            $mail->setBodyHtml($this->getBody());
        }
        else {
            $mail->setBodyText($this->getBody());
        }
		$transport = Mage::helper('smtp')->getTransport();
        $mail->setFrom($this->getFromEmail(), $this->getFromName())
            ->addTo($this->getToEmail(), $this->getToName())
            ->setSubject($this->getSubject());
			
		
        $mail->send($transport);

        return $this;
    }
}
