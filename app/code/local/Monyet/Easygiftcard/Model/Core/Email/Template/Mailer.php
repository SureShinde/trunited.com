<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Monyet_Easygiftcard_Model_Core_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{
    /**
     * override to add an event before email is being sent off
     *
     * @return Fooman_EmailAttachments_Model_Core_Email_Template_Mailer
     */
    public function send()
    {
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailTemplate = Mage::getModel('core/email_template');
            $emailInfo = array_pop($this->_emailInfos);
            $this->dispatchAttachEvent($emailTemplate, $emailInfo);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            //support queuing on newer Magento versions
            $emailTemplate->setQueue($this->getQueue());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                    $this->getTemplateId(),
                    $this->getSender(),
                    $emailInfo->getToEmails(),
                    $emailInfo->getToNames(),
                    $this->getTemplateParams(),
                    $this->getStoreId()
                );
        }
        return $this;
    }

    /**
     * handle dispatching of events based on template being sent
     *
     * @param $emailTemplate
     * @param $emailInfo
     */
    public function dispatchAttachEvent($emailTemplate, $emailInfo)
    {
        $storeId = $this->getStoreId();
        $templateParams = $this->getTemplateParams();

        //compare template id to work out what we are currently sending
        switch ($this->getTemplateId()) {

            //Order
            case Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'monyet_easygiftcard_before_send_order',
                    array(
                         'update'     => false,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['order'],
                         'email_info' => $emailInfo
                    )
                );
                break;
            //Order Updates
			/*
            case Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'monyet_easygiftcard_before_send_order',
                    array(
                         'update'     => true,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['order'],
                         'email_info' => $emailInfo
                    )
                );
                break;
			*/
            default:
                Mage::dispatchEvent(
                    'monyet_easygiftcard_before_send',
                    array(
                         'template'   => $emailTemplate,
                         'params'     => $templateParams,
                         'email_info' => $emailInfo
                    )
                );
        }
    }

    public function sendMageEvent($name, $update, $emailTemplate, $object, $emailInfo)
    {
        Mage::dispatchEvent(
            $name,
            array(
                 'update'     => $update,
                 'template'   => $emailTemplate,
                 'object'     => $object,
                 'email_info' => $emailInfo
            )
        );
    }
}
