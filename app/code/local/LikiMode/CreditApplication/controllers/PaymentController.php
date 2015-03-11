<?php
/**
 * Main payment controller
 *
 * @category    Controller
 * @package     Liki Payment
 */ 
class LikiMode_CreditApplication_PaymentController extends Mage_Core_Controller_Front_Action {
		
		// The redirect action is triggered when order is Placed
		public function redirectAction(){			
			Mage::register('likipayment',$this->prepareLikiPostParameters());
			// Render layout
			$this->loadLayout();
			$block = $this->getLayout()->createBlock('Mage_Core_Block_Template','creditapplication',array('template' => 'creditapplication/payment/redirect.phtml'));			
			$this->getLayout()->getBlock('content')->append($block);
			//Changes made by LikiExt
			$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');      
			$this->renderLayout();
		}
		
		// The cancel action is triggered when liki's user cancel order
		/*public function cancelAction(){
			$order = new Mage_Sales_Model_Order();
			$order->loadByIncrementId( Mage::getSingleton('checkout/session')->getLastRealOrderId());			
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED , true);
			$order->save();			
			// Render layout
			$this->loadLayout();
			$block = $this->getLayout()->createBlock('Liki_CreditApplication_Block_Cc','creditapplication',array('template' => 'creditapplication/payment/cancel.phtml'));			
			$this->getLayout()->getBlock('content')->append($block);
			$this->renderLayout();
		}	*/
		//LIKI code start
		//Reason of change: Make cancel action URL working
		public function cancelAction()
		{
		$session = Mage::getSingleton('checkout/session');
		$cart = Mage::getSingleton('checkout/cart/');
		$session->setQuoteId($session->getPaypalStandardQuoteId(true));
		if ($session->getLastRealOrderId()) {
			$incrementId = $session->getLastRealOrderId();
			if (empty($incrementId)) {
				$this->_redirect('checkout/onepage/payment');
				return;
			}
			$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
			$session->getQuote()->setIsActive(false)->save();
			$session->clear();
			try {
				$order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_CANCEL, true);
				$order->cancel()->save();
			} catch (Mage_Core_Exception $e) {
				Mage::logException($e);
			}
			$items = $order->getItemsCollection();
			foreach ($items as $item) {
				try {
					$cart->addOrderItem($item);
				} catch (Mage_Core_Exception $e) {
					$session->addError($this->__($e->getMessage()));
					Mage::logException($e);
					continue;
				}
			}
			$cart->save();
		}
		$this->_redirect('checkout/cart/');
		}
		public function changeorderAction()
		{
		$session = Mage::getSingleton('checkout/session');
		$cart = Mage::getSingleton('checkout/cart/');
		$session->setQuoteId($session->getPaypalStandardQuoteId(true));
		if ($session->getLastRealOrderId()) {
			$incrementId = $session->getLastRealOrderId();
			if (empty($incrementId)) {
				$this->_redirect('checkout/onepage/payment');
				return;
			}
			$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
			$session->getQuote()->setIsActive(false)->save();
			$session->clear();
			try {
				$order->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_CANCEL, true);
				$order->cancel()->save();
			} catch (Mage_Core_Exception $e) {
				Mage::logException($e);
			}
			$items = $order->getItemsCollection();
			foreach ($items as $item) {
				try {
					$cart->addOrderItem($item);
				} catch (Mage_Core_Exception $e) {
					$session->addError($this->__($e->getMessage()));
					Mage::logException($e);
					continue;
				}
			}
			$cart->save();
		}
		$this->_redirect('checkout/cart/');
		}
		//Reason of change: Encode all data and remove single quotes
		//Prepare Parameters For Liki Post Data
		private function prepareLikiPostParameters(){		
			// Retrieve order
			$order = new Mage_Sales_Model_Order();
			$order->loadByIncrementId( Mage::getSingleton('checkout/session')->getLastRealOrderId() );
			 $likipayment['liki_payment_url'] =  Mage::getStoreConfig('payment/CreditApplication/submit_url').'/LeaseApplication/ProcessPostLeaseApplication';
			//Now, Order has only  one product  so that $items[0] is hardcorded, it will update when multiple product will come in Order.
		    $items = $order->getAllItems(); 
			$MerchantSession['MerchantId'] = '1';
			$MerchantSession['MerchantCustomerId'] = $order->customer_id;
			$MerchantSession['SuccessURL'] = Mage::getBaseUrl().'CreditApplication/Payment/success';
			$MerchantSession['CancelURL']   =  Mage::getBaseUrl().'CreditApplication/Payment/cancel';
			$MerchantSession['RejectURL']   =  Mage::getBaseUrl().'CreditApplication/Payment/reject';
			$MerchantSession['ChangeOrderURL']   =  Mage::getBaseUrl().'CreditApplication/Payment/changeorder';
			$MerchantSession['LogoURL']   =  Mage::getBaseUrl().'Logo.png';
			$likipayment['MerchantSession']=$MerchantSession;
			$Order['MerchantOrderId']=$order->getEntityId();
			$Order['CreateDate']=date("M d, Y");

			$shipping_cost=$order->getShippingAmount();
			foreach ($items as $value)
			{
				$Item['MerchantProductId']=$value->getProductId();
				$productname = $value->getName();
//				$productname = urlencode($value->getName());
				$Item['MerchantProductDescription']=$productname;
//				$Item['Amount']=urlencode($value->getPrice());
				$qty=$value['qty_ordered'];
				$Item['Amount']=($value->getPrice() * $qty);
//				$Product['ReferenceID']=urlencode($value->getSku());
				$Product['ReferenceID']=$value->getSku();
				$Item["ProductDefinition"]=$Product;
//				$Item['ShippingCost']=urlencode($shipping_cost);
				$Item['ShippingCost']=$shipping_cost;
				$item[] =$Item;
			}
			$Order['OrderItem']=$item;
			
			$shippingAddress=$order->getShippingAddress()->getData();
			$billingAddress=$order->getBillingAddress()->getData();
			$customerId = $order->customer_id;
//			$fisrtname=urlencode($shippingAddress['firstname']);
			$fisrtname=$shippingAddress['firstname'];
			$fisrtname=trim($fisrtname);
			
			$Customer['FirstName']=str_replace("&","and",$fisrtname);
//			$lastname=urlencode($shippingAddress['lastname']);
			$lastname=$shippingAddress['lastname'];
			
			$lastname=trim($lastname);
			$Customer['LastName']=str_replace("&","and",$lastname);	
//			$email=urlencode($shippingAddress['email']);
			$email=$shippingAddress['email'];
			
			//Code Added by LikiExt
			//reason: When user email is blank then payment gateway gives the error. So we can not send blank value of emailid to payment gateway.
 			if($email=='')
			{
				$email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			}
			//LIKI Code end
			
			$Customer['EmailAddress']=str_replace("&","and",$email);
			$LIKI['MerchantCustomerId']=$order->customer_id;
			$Customer['LIKI']=$LIKI;
			$Address = array();
			$PhoneNumber = array();
			if($shippingAddress['address_type']=='shipping')
			{
//				$street=urlencode($shippingAddress['street']);
				$street=$shippingAddress['street'];
				$AddressShipping['Street1']=str_replace("&","and",$street);
//				$postcode=urlencode($shippingAddress['postcode']);
				$postcode=$shippingAddress['postcode'];
				$AddressShipping['PostalCode']=str_replace("&","and",$postcode);
//				$city=urlencode($shippingAddress['city']);
				$city=$shippingAddress['city'];
				$AddressShipping['City']=str_replace("&","and",$city);
				$AddressShipping['Type']='Shipping';
				$region = Mage::getModel('directory/region')->load($shippingAddress['region_id']);
//				$AddressShipping['State']=urlencode($shippingAddress['code']);	
				$AddressShipping['State']=str_replace("&","and",$region['code']);	
				$HomePhoneNumber['Type']='Home';
//				$HomePhoneNumber['Number']=urlencode($shippingAddress['telephone']);
				$HomePhoneNumber['Number']=str_replace("&","and",$shippingAddress['telephone']);
				array_push($Address, $AddressShipping);
			}
			if($billingAddress['address_type']=='billing')
			{
//				$street=urlencode($shippingAddress['street']);
				$street=$shippingAddress['street'];
				$BillingAddress['Street1']=str_replace("&","and",$street);
//				$postcode=urlencode($shippingAddress['postcode']);
				$postcode=$shippingAddress['postcode'];
				$BillingAddress['PostalCode']=str_replace("&","and",$postcode);
//				$city=urlencode($shippingAddress['city']);
				$city=$shippingAddress['city'];
				$BillingAddress['City']=str_replace("&","and",$city);
				$BillingAddress['Type']='billing';
				$regionBilling = Mage::getModel('directory/region')->load($billingAddress['region_id']);
//				$code=urlencode($shippingAddress['code']);
				$code=$shippingAddress['code'];
				$BillingAddress['State']=str_replace("&","and",$regionBilling['code']);	
				//LIKI Code End
				array_push($Address, $BillingAddress);
			}
			array_push($PhoneNumber, $HomePhoneNumber);
			$Customer['Address']=$Address;
			$Customer['PhoneNumber']=$PhoneNumber;
			$likipayment['Customer']=$Customer;
			$likipayment['Order']=$Order;
			
			Mage::Log($Customer);
			return $likipayment;	
		}	
		
		// The cancel action is triggered when liki's user reject order
		public function rejectAction(){
			$this->loadLayout();
			$block = $this->getLayout()->createBlock('Mage_Checkout_Block_Onepage_Success','creditapplication',array('template' => 'checkout/success.phtml'));			
			$this->getLayout()->getBlock('content')->append($block);
			$this->renderLayout();
		}	           
	 
		//Show success message with order details and print order
		public function successAction(){
		
			$this->loadLayout();
			$block = $this->getLayout()->createBlock('Mage_Checkout_Block_Onepage_Success','creditapplication',array('template' => 'checkout/success.phtml'));			
			$this->getLayout()->getBlock('content')->append($block);
			$this->renderLayout();
		}			
		
}