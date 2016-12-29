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
		if(!$data['phone_number']){
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('Something was wrong with this action.')
			);
			$this->_redirectUrl(Mage::getUrl('customer/account/create'));
			return;
		}

		$mobile_prefix = Mage::helper('custompromotions/verify')->getMobileCode();
		$phone = Mage::helper('custompromotions/verify')->getPhoneNumberFormat($mobile_prefix,$data['phone_number']);

		$is_verified = Mage::helper('custompromotions/verify')->isVerified($data['phone_number']);
		if($is_verified){
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('This mobile number was verified with exist account.')
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
				Mage::helper('custompromotions/verify')->saveVerify($data['phone_number'], $new_code);
				/* end save to database: customer_verify_mobile table */
			} catch (Exception $ex){
				Mage::getSingleton('customer/session')->addError(
					Mage::helper('custompromotions')->__('Error sending message: ' . str_replace('[HTTP 400] Unable to create record:','',$ex->getMessage()))
				);
				$this->_redirectUrl(Mage::getUrl('customer/account/create'));
				return;
			}
		}

		/* set data to session */
		Mage::getSingleton('core/session')->setPhoneActive($data['phone_number']);
		Mage::getSingleton('core/session')->setCodeActive($new_code);
		/* end set data to session */
		Mage::getSingleton('customer/session')->addSuccess(
			Mage::helper('custompromotions')->__('The verification code has been sent to your mobile successfully.')
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
				Mage::helper('custompromotions')->__('Thank you! Your mobile number has been verified.')
			);
		} else if($check_verified == Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_NON_EXIST) {
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('Verification code is incorrect. Please enter it again or click <b><a href="'.Mage::getUrl('*/*/resend').'" title="Resend Code" class="resend_code">RESEND CODE</a></b>')
			);
		} else if($check_verified == Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_VERIFIED) {
			Mage::getSingleton('customer/session')->addNotice(
				Mage::helper('custompromotions')->__('This mobile number has already been verified.')
			);
		} else if($check_verified == Magestore_Custompromotions_Model_Verifymobile::VERIFY_ERROR_OTHER) {
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('Verification code incorrect, please try again.')
			);
		}
		$this->_redirectUrl(Mage::getUrl('customer/account/create'));
		return;
	}

	public function backAction()
	{
		Mage::getSingleton('core/session')->unsPhoneActive();
		Mage::getSingleton('core/session')->unsCodeActive();
		Mage::getSingleton('core/session')->unsVerify();
		$this->_redirectUrl(Mage::getUrl('customer/account/create'));
	}

	public function resendAction()
	{
		$phone_number = Mage::getSingleton('core/session')->getPhoneActive();
		if($phone_number == null){
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('The mobile number does not exist.')
			);
			$this->_redirectUrl(Mage::getUrl('customer/account/create'));
			return;
		}

		$mobile_prefix = Mage::helper('custompromotions/verify')->getMobileCode();
		$phone = Mage::helper('custompromotions/verify')->getPhoneNumberFormat($mobile_prefix,$phone_number);

		$is_verified = Mage::helper('custompromotions/verify')->isVerified($phone_number);
		if($is_verified){
			Mage::getSingleton('customer/session')->addError(
				Mage::helper('custompromotions')->__('This mobile number was verified with exist account.')
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
				Mage::helper('custompromotions/verify')->saveVerify($phone_number, $new_code);
				/* end save to database: customer_verify_mobile table */
			} catch (Exception $ex){
				Mage::getSingleton('customer/session')->addError(
					Mage::helper('custompromotions')->__('Error sending message: ' . str_replace('[HTTP 400] Unable to create record:','',$ex->getMessage()))
				);
				$this->_redirectUrl(Mage::getUrl('customer/account/create'));
				return;
			}
		}

		/* set data to session */
		Mage::getSingleton('core/session')->setPhoneActive($phone_number);
		Mage::getSingleton('core/session')->setCodeActive($new_code);
		/* end set data to session */
		Mage::getSingleton('customer/session')->addSuccess(
			Mage::helper('custompromotions')->__('The verification code has been sent to your mobile successfully.')
		);
		$this->_redirectUrl(Mage::getUrl('customer/account/create'));
	}

	public function redirectAction()
	{
		$redirect_url = $_SERVER['HTTP_REFERER'];
		if($redirect_url == null)
			$redirect_url = Mage::getBaseUrl();

		Mage::getSingleton('customer/session')->setBeforeAuthUrl($redirect_url);
		$this->_redirectUrl(Mage::getUrl('customer/account/login'));
	}

}