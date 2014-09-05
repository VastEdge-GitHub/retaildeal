<?php
//LIKI Code Start
//Reason of change:Removing the shipping method From Checkout process
include('app/code/core/Mage/Checkout/controllers/OnepageController.php');
class LikiextCheckout_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    
	protected $_sectionUpdateFunctions = array(
	'payment-method' => '_getPaymentMethodsHtml',
	'review' => '_getReviewHtml',
	);
   
    /**
     * save checkout billing address
     */
	  public function saveBillingAction()
	  {
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$postData = $this->getRequest()->getPost('billing', array());
			$data = $this->_filterPostData($postData);
			$data = $this->getRequest()->getPost('billing', array());
			$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
		
			if (isset($data['email'])) {
				$data['email'] = trim($data['email']);
			}
			$result = $this->getOnepage()->saveBilling($data, $customerAddressId);
		
			if (!isset($result['error'])) {
			/* check quote for virtual */
				if ($this->getOnepage()->getQuote()->isVirtual()) {
					$result['goto_section'] = 'payment';
					$result['update_section'] = array(
						'name' => 'payment-method',
						'html' => $this->_getPaymentMethodsHtml()
					);
				}
				elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
					$result['goto_section'] = 'payment';
					$result['update_section'] = array(
						'name' => 'payment-method',
						'html' => $this->_getPaymentMethodsHtml()
					);
		
					$result['allow_sections'] = array('shipping');
					$result['duplicateBillingInfo'] = 'true';
				}
				else {
					$result['goto_section'] = 'shipping';
				}
			}
		
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	  }

	public function saveShippingAction()
	{
	if ($this->_expireAjax()) {
	return;
	}
	if ($this->getRequest()->isPost()) {
	$data = $this->getRequest()->getPost('shipping', array());
	$customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
	$result = $this->getOnepage()->saveShipping($data, $customerAddressId);
	
	if (!isset($result['error'])) {
	$result['goto_section'] = 'payment';
	$result['update_section'] = array(
	'name' => 'payment-method',
	'html' => $this->_getPaymentMethodsHtml()
	);
	}
	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	}

    /**
     * Shipping method save action
     */
	 //LIKI Code End
}
?>