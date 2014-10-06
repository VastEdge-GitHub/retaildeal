<?php
include('app\code\core\Mage\Checkout\Block\Onepage\Abstract.php');
abstract class LikiextCheckout_Checkout_Block_Onepage_Abstract extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
		 //Code Changed By LIKI
		//return array('login', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
		return array('login', 'billing', 'shipping', 'payment', 'review');
    	//LIKI Code End
	}
}
