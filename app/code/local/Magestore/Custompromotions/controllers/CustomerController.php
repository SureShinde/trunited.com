<?php
require_once   Mage::getBaseDir('lib').'/Twilio/autoload.php';
use Twilio\Rest\Client;

class Magestore_Custompromotions_CustomerController extends Mage_Core_Controller_Front_Action
{
	public function sendCodeAction()
	{
		if(!Mage::helper('custompromotions/verify')->isEnable()){
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('This feature is disabled')
			);
			$this->_redirectUrl(Mage::getUrl('customer/account/create'));
			return;
		}
		$data = $this->getRequest()->getParams();
		if(!$data['phone_prefix'] || !$data['phone_number']){
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('Something was wrong with this action.')
			);
			$this->_redirectUrl(Mage::getUrl('customer/account/create'));
			return;
		}

		$phone = Mage::helper('custompromotions/verify')->getPhoneNumberFormat($data['phone_prefix'],$data['phone_number']);
		$is_verified = Mage::helper('custompromotions/verify')->isVerified($phone);
		if($is_verified){
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('This phone number was verified with exist account.')
			);
			$this->_redirectUrl(Mage::getUrl('customer/account/create'));
			return;
		} else {
			try{
				/* sending code to customer */
				$sid = Mage::helper('custompromotions/verify')->getAccountSID();
				$token = Mage::helper('custompromotions/verify')->getAuthToken();
				$from = Mage::helper('custompromotions/verify')->getSenderNumber();
				$new_code = Mage::helper('custompromotions/verify')->generateRandomString();
				$message = Mage::helper('custompromotions')->__('Here is your Trunited verification code: %s',$new_code);
				$client = new Client($sid, $token);
				$client->messages->create(
					$phone,
					array(
						'from' => $from,
						'body' => $message
					)
				);
				/* end sending sms */

				/* save to database: customer_verify_mobile table */
				Mage::helper('custompromotions/verify')->saveVerify($phone, $new_code);
				/* end save to database: customer_verify_mobile table */
			} catch (Exception $ex){
				Mage::getSingleton('customer/session')->addError(
					Mage::helper('custompromotions')->__('Error sending message: ' . $ex->getMessage())
				);
				$this->_redirectUrl(Mage::getUrl('customer/account/create'));
				return;
			}
		}

		/* set data to session */
		Mage::getSingleton('core/session')->setPhoneActive($phone);
		Mage::getSingleton('core/session')->setCodeActive($new_code);
		/* end set data to session */
		Mage::getSingleton('customer/session')->addSuccess(
			Mage::helper('custompromotions')->__('The verification code has sent to your phone successfully.')
		);
		$this->_redirectUrl(Mage::getUrl('customer/account/create'));
	}

	public function verifyCodeAction()
	{
		$data = $this->getRequest()->getParams();
		if(!isset($data['verify_code']) || $data['verify_code'] == null)
		{
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('Something was wrong with this action.')
			);
			$this->_redirectUrl(Mage::getUrl('customer/account/create'));
			return;
		}

		$phone = Mage::getSingleton('core/session')->getPhoneActive();
		$check_verified = Mage::helper('custompromotions/verify')->verify($phone, $data['verify_code']);
		if($check_verified == Magestore_Custompromotions_Model_Verifymobile::VERIFY_SUCCESS)
		{
			Mage::getSingleton('core/session')->setVerify(true);
			Mage::getSingleton('customer/session')->addSuccess(
				Mage::helper('custompromotions')->__('Thank you! Your phone number has been verified.')
			);
		} else if($check_verified == Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_NON_EXIST) {
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('Your phone incorrect! Click here to enter again.')
			);
		} else if($check_verified == Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_VERIFIED) {
			Mage::getSingleton('customer/session')->addNotice(
				Mage::helper('custompromotions')->__('This phone number was verified with exist account. If you didn\'t receive the code. Please click here to resend the code.')
			);
		} else if($check_verified == Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_OTHER) {
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('Verification code incorrect, please try again.')
			);
		}
		$this->_redirectUrl(Mage::getUrl('customer/account/create'));
		return;
	}

}