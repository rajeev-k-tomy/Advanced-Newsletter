<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License 3.0 (OSL-3.0)
 * that is bundled with this package in the file LICENSE_RKT_NLFORGUEST.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to rajeevphpdeveloper@gmail.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * This extension is only be used with Magento. It is used to change Magento's
 * custom behaviour. To get more details on what this module does, please have a look on 
 * About_Rkt_NLforGuest.txt in the root folder
 *
 */

/**
 * Observer
 *
 * @category    extension
 * @package     Rkt_NLforGuest
 * @author      programmer_rkt   
 *
 */
class Rkt_NLforGuest_Model_Observer
{
	/**
     * Subscription confirm action
     */
	public function subscribeNewsletter($observer)
	{
		$customerSession = Mage::getSingleton('customer/session');
		$subscriberModel = Mage::getModel('rkt_nlforguest/subscriber');

		//check customer logged in or not
		if ($customerSession->isLoggedIn()) { 
			$customerEmail = trim($observer->getEvent()->getCustomer()->getData('email'));

			//check whether cookie exist
			if ($cookie = $subscriberModel->getLoginValidationStatus()) {
				$array = explode('-', $cookie);print_r($array);
			                	
				/*
				 * expect two parts in $array
				 * first part stands for the status 
				 * second part stands for subscriber id
				*/
				if ($array[0] == 1) {
					$subscriberId = $array[1];
					$subscriber = $subscriberModel->load((int)$subscriberId);

					//validate subscriber
					print_r($subscriber->getId());
					print_r($subscriber->getCode());	
					if ($subscriber->getId() && $code = $subscriber->getCode()) {
						//check for email mathes
						if (trim($subscriber->getEmail()) == $customerEmail) {
							$subscriberModel->unsetLoginValidationStatus();

							//this enables to send success mail for confirmation mails
			                if ($subscriberModel->sendSuccessMail($customerEmail, false)) {
			                	$subscriberModel->setCode($code);
			                    //confirm subscription
			                    if($subscriberModel->confirm($code)) { 
			                        $customerSession->addSuccess(Mage::helper('rkt_nlforguest')->__('Your subscription has been confirmed. For more details Please check your subscriberMail Inbox'));
			                    } else {
			                        $customerSession->addError(Mage::helper('rkt_nlforguest')->__('Invalid subscription confirmation code.'));
			                    }
			                } else {
			                		$customerSession->addError(Mage::helper('rkt_nlforguest')->__('We encountered some error to send success mail. We will update it soon.'));
			                }
						} else {
							$customerSession->addError(Mage::helper('rkt_nlforguest')->__('Newsletter Subscription mail is not matching with your mail address. Please try again'));

						}
					} else {
						$customerSession->addError($this->__('Invalid subscription ID.'));
					}
				}
			} 				
		}
		return;
	}
}
