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
 * obtain it through the world-wide-web, please send an subscriberMail
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
 * rewrite controller for `Mage_Newsletter_SubscriberController`
 *
 * @category    extension
 * @package     Rkt_NLforGuest
 * @author      programmer_rkt   
 *
 */
require_once(Mage::getModuleDir('controllers','Mage_Newsletter').DS.'SubscriberController.php');
 
class Rkt_NLforGuest_SubscriberController extends Mage_Newsletter_SubscriberController
{
    /**
      * New subscription action
      */
    public function newAction()
    { 
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                }

                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
                }

                $status = Mage::getModel('rkt_nlforguest/subscriber')->subscribeNewsletter($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($this->__('Confirmation request has been sent.'));
                } elseif ($status == Rkt_NLforGuest_Model_Subscriber::NO_SUCCESS_MAIL_STATUS) {
                    $session->addNotice($this->__('Sorry, we are not providing newsletter subscription for Guest Users. Please Log-in and try again'));
                } else {
                    $session->addSuccess($this->__('Thank you for your subscription.'));
                }
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription.'));
            }
        }
        $this->_redirectReferer();
    }

	/**
     * Subscription confirm action
     */
    public function confirmAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            $subscriber = Mage::getModel('newsletter/subscriber')->load($id);
            $session = Mage::getSingleton('core/session');
            $model = Mage::getModel('rkt_nlforguest/subscriber');

            if ($subscriber->getId() && $subscriber->getCode()) {
                $status = Mage::helper('rkt_nlforguest')->isMailAllowForGuest();
                $subscriberMail = $subscriber->getSubscriberEmail();

            	//checks whether success mail is not allowed for guest
            	if ($status === false) {
                    $customerSession = Mage::getModel('customer/session');

	            	//check whether customer logged in or not
	            	if (!$customerSession->isLoggedIn()) { 
                        $model->setLoginValidationStatus('1-'.$id);
                        $session->addNotice('In order to complete subscription, you need to login');
	            		$this->_redirect('customer/account/login'); 
                        return;
	            	} else {
                        $customerMail = Mage::getModel('customer/customer')->load($customerSession->getId())->getData('email');
                        
                        //check subscriber customer and current customer are same not same
                        if (Mage::helper('rkt_nlforguest')->validateMail($customerMail, $subscriberMail) == false) {
                            $session->addError('Newsletter Subscription mail is not matching with your mail address. Please try again');
                            $this->_redirectUrl(Mage::getBaseUrl());
                            return;
                        }
                    }
	            }

 	            //this enables to send success mail for confirmation mails
                if ($model->sendSuccessMail($subscriberMail, $status)) {

                    //confirm subscription
                    if($subscriber->confirm($code)) { 
                        $session->addSuccess($this->__('Your subscription has been confirmed. For more details Please check your subscriberMail Inbox'));
                    } else {
                        $session->addError($this->__('Invalid subscription confirmation code.'));
                    }
                } else {
                		$session->addError($this->__('We encountered some error to send success mail. We will update it soon.'));
                }
                
            } else {
                $session->addError($this->__('Invalid subscription ID.'));
            }
        }

        $this->_redirectUrl(Mage::getBaseUrl());
    }
}