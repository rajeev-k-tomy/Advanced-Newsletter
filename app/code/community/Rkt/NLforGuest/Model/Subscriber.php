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
 * Model
 *
 * @category    extension
 * @package     Rkt_NLforGuest
 * @author      programmer_rkt   
 *
 */
class Rkt_NLforGuest_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
	const ALLOW_TO_SEND_MAIL_WHEN_LOGIN = 1;
	const DONT_ALLOW_TO_SEND_MAIL_WHEN_LOGIN = 0;
    const NO_SUCCESS_MAIL_STATUS = 10;

	const XML_PATH_FOR_ALLOW_GUEST       = 'newsletter/subscription/status_sm_for_guest';

	/**
     * constructor
     */
    protected function _construct()
    {
        parent::_construct();
    }
	
	/**
     * Send success mail for valid newsletter confirmations
     *
     * @param string | $email
     * @param boolean | $status
     * @return boolean 
     */
	public function sendSuccessMail($email = null, $status = null) 
	{ 
		if ($email != null) {
			$this->loadByEmail($email);
			$customerSession = Mage::getSingleton('customer/session');
            //print_r($customerSession->getData());die();
			$isSubscribeOwnEmail = $customerSession->isLoggedIn();

	        if ($isSubscribeOwnEmail) {
	            $this->setStoreId($customerSession->getCustomer()->getStoreId());
	            $this->setCustomerId($customerSession->getCustomerId());
	        } else {
	            $this->setStoreId(Mage::app()->getStore()->getId());
	            $this->setCustomerId(0);
	        }
	       $this->sendConfirmationSuccessEmail();
           return true;
	        
	    }
	    return false;
    }

    /**
     * Set login validation
     *
     * @param int
     */
    public function setLoginValidationStatus($status)
    {
    	/*
    		Using cookie method to validate subscriber confirmation after customer logged in.
    		Another way is to create a new field for this and set it.
    	*/
    	$cookie = Mage::getModel('core/cookie')->set('login_validation', $status, 7200);
    	return $cookie;
    }

     /**
     * Get login validation
     *
     */
    public function getLoginValidationStatus()
    {
    	
    	return Mage::getModel('core/cookie')->get('login_validation');
    	
    }

     /**
     * Delete login validation
     *
     */
    public function unsetLoginValidationStatus()
    {
        Mage::getModel('core/cookie')->delete('login_validation');
    }
    


    /**
     * Subscribes by email
     *
     * @param string $email
     * @throws Exception
     * @return int
     */
    public function subscribeNewsletter($email)
    {
        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('customer/session');

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed   = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;
        $ownerId = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email)
            ->getId();
        $isSubscribeOwnEmail = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId();

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $isOwnSubscribes = $isSubscribeOwnEmail;
                if ($isOwnSubscribes == true){
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                } else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setSubscriberEmail($email);
        }

        if ($isSubscribeOwnEmail) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
        } else {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        try {
            $this->save();
            if ($isConfirmNeed === true
                && $isOwnSubscribes === false
            ) {
                $this->sendConfirmationRequestEmail();
            } elseif (Mage::getStoreConfig(self::XML_PATH_FOR_ALLOW_GUEST) == 1) {
                $this->sendConfirmationSuccessEmail();
            } else {
                $this->setStatus(self::NO_SUCCESS_MAIL_STATUS);
            }
            return $this->getStatus();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
