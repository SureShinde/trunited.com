<?php

if (Mage::helper('core')->isModuleEnabled('Aschroder_SMTPPro')
    && version_compare(
        (string)Mage::getConfig()->getNode()->modules->Aschroder_SMTPPro->version,
        '2.0.6', '>'
    )
) {
    class Monyet_Easygiftcard_Model_Core_Email_Queue_Compatibility
        extends Aschroder_SMTPPro_Model_Email_Queue
    {

    }
} elseif (Mage::helper('core')->isModuleEnabled('Ebizmarts_Mandrill')
    && version_compare(
        (string)Mage::getConfig()->getNode()->modules->Ebizmarts_Mandrill->version,
        '2.0.8', '>='
    )
) {
    class Monyet_Easygiftcard_Model_Core_Email_Queue_Compatibility
        extends Monyet_Easygiftcard_Model_Core_Email_Queue_Mandrill
    {

    }
} else {
    class Monyet_Easygiftcard_Model_Core_Email_Queue_Compatibility
        extends Monyet_Easygiftcard_Model_Core_Email_Queue_Monyet
    {

    }
}
