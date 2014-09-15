<?php
/**
 * Main payment controller
 *
 * @category    Controller
 * @package     Liki Payment
 */ 
class Liki_CreditApplication_PaymentController extends Mage_Core_Controller_Front_Action {
		
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
		public function cancelAction()
		{
		$session = Mage::getSingleton('checkout/session');
		$cart = Mage::getSingleton('checkout/cart/');
		$session->setQuoteId($session->getPaypalStandardQuoteId(true));
		if ($session->getLastRealOrderId()) {
			$incrementId = $session->getLastRealOrderId();
			if (empty($incrementId)) {
				//$session->addError($this->__('You have cancelled the order'));
				$this->_redirect('checkout/onepage');
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
			//$session->addError($this->__('You have cancelled your order'));
		}
		$this->_redirect('checkout/onepage/');
		}
		//liki code End
		
		//LIKI Code start
		//Reason of change: Encode all data and remove single quotes
		//Prepare Parameters For Liki Post Data
		private function prepareLikiPostParameters(){		
			// Retrieve order
			$order = new Mage_Sales_Model_Order();
			$order->loadByIncrementId( Mage::getSingleton('checkout/session')->getLastRealOrderId() );
			 $likipayment['liki_payment_url'] =  Mage::getStoreConfig('payment/CreditApplication/submit_url').'/LeaseApplication/ProcessPostLeaseApplication';
			//Now, Order has only  one product  so that $items[0] is hardcorded, it will update when multiple product will come in Order.
		    $items = $order->getAllItems(); 
			$MerchantSession['MerchantId'] = '11';
			$MerchantSession['MerchantCustomerId'] = $order->customer_id;
			$MerchantSession['SuccessURL'] = Mage::getBaseUrl().'CreditApplication/Payment/success';
			$MerchantSession['CancelURL']   =  Mage::getBaseUrl().'CreditApplication/Payment/cancel';
			$MerchantSession['RejectURL']   =  Mage::getBaseUrl().'CreditApplication/Payment/reject';
			$MerchantSession['LogoURL']   =  Mage::getBaseUrl().'Logo.png';
			
			$likipayment['MerchantSession']=$MerchantSession;
			$Order['MagentoOrderId']=$order->getEntityId();
			$Order['CreateDate']=date("M d, Y");

			// Code Added by LIKIext
			$shipping_cost=$order->getShippingAmount();
			// End of code by LIKIext
			foreach ($items as $value)
			{
				$Item['MerchantProductId']=$value->getProductId();
				$productname = urlencode($value->getName());
				//echo $productname;
				$Item['MerchantProductDescription']=$productname;
				//echo "<br>".$Item['MerchantProductDescription'];
				//$Item['MerchantProductDescription']=$value->getName();
				$Item['Amount']=urlencode($value->getPrice());
				$Product['ReferenceID']=urlencode($value->getSku());
				$Item["ProductDefinition"]=$Product;
				//echo "<br>".$Item["ProductDefinition"];
				// Code Added by LIKIext
				$Item['ShippingCost']=urlencode($shipping_cost);
				// End of code by LIKIext
				$item[] =$Item;
			}
			$Order['OrderItem']=$item;
			
			$shippingAddress=$order->getShippingAddress()->getData();
			$billingAddress=$order->getBillingAddress()->getData();
			$customerId = $order->customer_id;
			$fisrtname=urlencode($shippingAddress['firstname']);
			$Customer['FirstName']=$fisrtname;
			//$Customer['FirstName']=$shippingAddress['firstname'];
			$lastname=urlencode($shippingAddress['lastname']);
			$Customer['LastName']=$lastname;	
			//$Customer['LastName']=$shippingAddress['lastname'];
			$email=urlencode($shippingAddress['email']);
			$Customer['EmailAddress']=$email;
			//$Customer['EmailAddress']=$shippingAddress['email'];	
			//Mage::helper('core')->decrypt($encrypted_data);
			$LIKI['MerchantCustomerId']=$order->customer_id;
			$Customer['LIKI']=$LIKI;
			$Address = array();
			$PhoneNumber = array();
			if($shippingAddress['address_type']=='shipping')
			{
				$street=urlencode($shippingAddress['street']);
				$AddressShipping['Street1']=$street;
				//$AddressShipping['Street1']=$shippingAddress['street'];
				$postcode=urlencode($shippingAddress['postcode']);
				$AddressShipping['PostalCode']=$postcode;
				//$AddressShipping['PostalCode']=$shippingAddress['postcode'];
				$city=urlencode($shippingAddress['city']);
				$AddressShipping['City']=$city;
				//$AddressShipping['City']=$shippingAddress['city'];
				$AddressShipping['Type']='Shipping';
				$region = Mage::getModel('directory/region')->load($shippingAddress['region_id']);
				//$code=urlencode($shippingAddress['code']);
				//$AddressShipping['State']=$code;
				$AddressShipping['State']=urlencode($shippingAddress['code']);	
				$HomePhoneNumber['Type']='Home';
				//$telephone=urlencode($shippingAddress['telephone']);
				//$HomePhoneNumber['Number']=$telephone;
				$HomePhoneNumber['Number']=urlencode($shippingAddress['telephone']);
				array_push($Address, $AddressShipping);
			}
			if($billingAddress['address_type']=='billing')
			{
				$street=urlencode($shippingAddress['street']);
				$BillingAddress['Street1']=$street;
				//$BillingAddress['Street1']=$shippingAddress['street'];
				$postcode=urlencode($shippingAddress['postcode']);
				$BillingAddress['PostalCode']=$postcode;
				//$BillingAddress['PostalCode']=$shippingAddress['postcode'];
				$city=urlencode($shippingAddress['city']);
				$BillingAddress['City']=$city;
				//$BillingAddress['City']=$shippingAddress['city'];
				$BillingAddress['Type']='billing';
				$regionBilling = Mage::getModel('directory/region')->load($billingAddress['region_id']);
				$code=urlencode($shippingAddress['code']);
				$BillingAddress['State']=$code;	
				//$BillingAddress['State']=$shippingAddress['code'];	
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