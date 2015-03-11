<?php
class LikiMode_Sales_Model_Service_Quote extends Mage_Sales_Model_Service_Quote
{
    /**
     * Validate quote data before converting to order
     *
     * @return Mage_Sales_Model_Service_Quote
     */
	//LIKI Code Start
	//Reason of Change: To skip the Shipping Method From checkout process
	protected function _validate()
	{
		$helper = Mage::helper('sales');
		if (!$this->getQuote()->isVirtual()) {
		$address = $this->getQuote()->getShippingAddress();
		$addressValidation = $address->validate();
		if ($addressValidation !== true) {
		Mage::throwException(
		$helper->__('Please check shipping address information. %s', implode(' ', $addressValidation))
		);
		}
		}
		
		$addressValidation = $this->getQuote()->getBillingAddress()->validate();
		if ($addressValidation !== true) {
		Mage::throwException(
		$helper->__('Please check billing address information. %s', implode(' ', $addressValidation))
		);
		}
		
		if (!($this->getQuote()->getPayment()->getMethod())) {
		Mage::throwException($helper->__('Please select a valid payment method.'));
		}
		
		return $this;
	}
	//LIKI Code End
}
