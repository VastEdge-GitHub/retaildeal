<?php
/**
 * Main payment model
 *
 * @category    Model
 * @package     Liki Payment 
 
 */

class Liki_CreditApplication_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'CreditApplication';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl( 'CreditApplication/payment/redirect', array( '_secure' => true ) );
	}
}
?>