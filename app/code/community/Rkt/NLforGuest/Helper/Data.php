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
 * Helper
 *
 * @category    extension
 * @package     Rkt_NLforGuest
 * @author      programmer_rkt   
 *
 */
class Rkt_NLforGuest_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * check status of success mail is applicable for guest users
     *
     * @return boolean 
     *
     */
	public function isMailAllowForGuest()
	{
		$status = Mage::getStoreConfig(Rkt_NLforGuest_Model_Subscriber::XML_PATH_FOR_ALLOW_GUEST);
		if ($status == true) {
			return true;
		} else {
			return false;
		}
	}

	/**
     *  validate mails
     *
     * @param string | $mail1
     * @param string | $mail2
     * @return boolean
     *
     */
	public function validateMail($mail1, $mail2)
	{
		if ($mail1 == $mail2) {
			return true;
		} else {
			return false;
		}
	}
}