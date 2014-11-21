<<<<<<< HEAD
 <?php
=======
<?php
>>>>>>> master
/**
* Magento
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* Authorize (PREAUTH)
*	Authorize (PREAUTH) is the normal transaction method for ShopSite. When a customer clicks "Submit This Order," ShopSite sends the transaction to First Data Global for authorization, the transaction is authorized, and ShopSite is notified of the authorization. A "hold" for the amount of the purchase is placed on the customer's credit card, but the funds are not "captured" until the merchant goes to the Orders screen in ShopSite and clicks Bill Orders.
* Authorize and Capture (SALE)
*	Authorize and Capture (SALE) authorizes a transaction and captures funds all at once. ShopSite sends a transaction to First Data Global for approval, the transaction is approved, ShopSite is notified of the approval, and the transaction automatically settles at the end of the business day without any further action by the merchant.
* POST AUTH
*	A POSTAUTH transaction is used to capture funds authorized previously using an PREAUTH transaction.
*
* @category   Mage
* @package    Mage_Paygate
* @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*
* @author     Sreeprakash.N. <sree@schogini.com>
* @copyright  Copyright (c) 2008 Schogini Systems (http://schogini.in)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

class Mage_Firstdataglobalgateway_Model_Firstdataglobalgateway extends Mage_Payment_Model_Method_Cc
{
	const REQUEST_METHOD_CC     = 'CC';
	const REQUEST_METHOD_ECHECK = 'ECHECK';

	const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
	const REQUEST_TYPE_AUTH_ONLY    = 'AUTH_ONLY';
	const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';
	const REQUEST_TYPE_CREDIT       = 'REFUND';
	const REQUEST_TYPE_VOID         = 'VOID';
	const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

	const ECHECK_ACCT_TYPE_CHECKING = 'CHECKING';
	const ECHECK_ACCT_TYPE_BUSINESS = 'BUSINESSCHECKING';
	const ECHECK_ACCT_TYPE_SAVINGS  = 'SAVINGS';

	const ECHECK_TRANS_TYPE_CCD = 'CCD';
	const ECHECK_TRANS_TYPE_PPD = 'PPD';
	const ECHECK_TRANS_TYPE_TEL = 'TEL';
	const ECHECK_TRANS_TYPE_WEB = 'WEB';

	const RESPONSE_DELIM_CHAR = ',';

	const RESPONSE_CODE_APPROVED = 1;
	const RESPONSE_CODE_DECLINED = 2;
	const RESPONSE_CODE_ERROR    = 3;
	const RESPONSE_CODE_HELD     = 4;

	protected $_code  = 'firstdataglobalgateway';

	protected $_isGateway               = true;
	protected $_canAuthorize            = true;
	protected $_canCapture              = true;
	protected $_canCapturePartial       = false;
	protected $_canRefund               = true;
	protected $_canVoid                 = true;
	protected $_canUseInternal          = true;
	protected $_canUseCheckout          = true;
	protected $_canUseForMultishipping  = true;
	protected $_canSaveCc 				= false; // Not needed. Using tagged transactions as per Tom Morris suggestion tom@eggheadventures.com

	protected $_authorize	= '';

	function processInvoice ($invoice, $payment) {
		//$payment->getOrder()->sendNewOrderEmail();
		$order = $payment->getOrder();
		if (!$invoice->isCanceled()) {
			$invoice->sendEmail();
			$order->addStatusToHistory(
						$order->getStatus(),
						'Invoice email sent',
						'Invoice email sent'
					);
		}
	}	
	
	/**
	* Send authorize request to gateway
	*
	* @param   Varien_Object $payment
	* @param   decimal $amount
	* @return  Mage_Firstdataglobalgateway_Model_Firstdataglobalgateway
	*/
	public function authorize(Varien_Object $payment, $amount)
	{
		$error = false;
		//$this->logit('authorize start', array());
		//$this->logit('authorize start', get_class($payment));//Mage_Uos_Model_Order_Payment
		//$this->logit('authorize start', get_class_methods(get_class($payment)));
		//$this->logit('authorize get_class_vars', get_class_vars(get_class($payment)));

		if ($amount>0) {
			$payment->setAnetTransType(self::REQUEST_TYPE_AUTH_ONLY);
			$payment->setAmount($amount);

			//$this->logit('Calling _buildRequest', array());
			$request = $this->_buildRequest($payment);
			//$this->logit('buildrequest call returned', $request);

			$result  = $this->_postRequest($request);
			//$this->logit('postRequest call returned', $result);
			
			$payment->setCcApproval($result->getApprovalCode())
			->setLastTransId($result->getTransactionId())
			->setCcTransId($result->getTransactionId())
			->setCcAvsStatus($result->getAvsResultCode())
			->setCcCidStatus($result->getCardCodeResponseCode());


			$code = $result->getResponseReasonCode();
			$text = $result->getResponseReasonText();

			switch ($result->getResponseCode()) {
				case self::RESPONSE_CODE_APPROVED:
					$payment->setStatus(self::STATUS_APPROVED);
					if ($result->getTransactionId() != $payment->getParentTransactionId()) {
						$payment->setTransactionId($result->getTransactionId());
					}					
					$payment
						->setIsTransactionClosed(0)
						->setTransactionAdditionalInfo('real_transaction_id', $result->getTransactionId());					
					// added by Gayatri 10/Jun/2010
					if( !$order = $payment->getOrder() )
					{
						$order = $payment->getQuote();
					}
					$order->addStatusToHistory(
						$order->getStatus(),
						urldecode($result->getResponseReasonText()) . ' at FirstdataGlobalgateway, Trans ID: ' . $result->getTransactionId(),
						$result->getResponseReasonText() . ' from FirstdataGlobalgateway, Trans ID: ' . $result->getTransactionId()
					);
					// end added by Gayatri 10/Jun/2010
					break;
					
				case self::RESPONSE_CODE_DECLINED:
<<<<<<< HEAD
					// comment by liki 20/Nov/2014 Reason: change the payment decline message.
					//$error = Mage::helper('paygate')->__('Payment authorization transaction has been declined. ' . "\n$text");
					$error = Mage::helper('paygate')->__('Your card was declined.Please choose an alternative payment method.');
					// end liki 
					break;
					
				default:
					     // comment by liki 20/Nov/2014 Reason: change the payment decline message.
					//$error = Mage::helper('paygate')->__('Payment authorization error. ' . "\n$text");
					$error = Mage::helper('paygate')->__('Your card was declined.Please choose an alternative payment method.');
					// end liki 
=======
					$error = Mage::helper('paygate')->__('Payment authorization transaction has been declined. ' . "\n$text");
					break;
					
				default:
					$error = Mage::helper('paygate')->__('Payment authorization error. ' . "\n$text");
>>>>>>> master
					break;
			}
		} else {
			$error = Mage::helper('paygate')->__('Invalid amount for authorization.');
		}

		if ($error !== false) {
			Mage::throwException($error);
		}
    
		return $this;
	}

	public function capture(Varien_Object $payment, $amount)
	{
		//$this->logit('capture amount', $amount);
		$error = false;
		
        if ($payment->getParentTransactionId()) {
            $payment->setAnetTransType(self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE);
        } else {
            $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_CAPTURE);
        }		
		
		$payment->setAmount($amount);
		
		$request = $this->_buildRequest($payment);
		$result  = $this->_postRequest($request);
		if ($result->getResponseCode() == self::RESPONSE_CODE_APPROVED) {
			$payment->setStatus(self::STATUS_APPROVED);
			$payment->setCcTransId($result->getTransactionId());
			$payment->setLastTransId($result->getTransactionId());
			if ($result->getTransactionId() != $payment->getParentTransactionId()) {
				$payment->setTransactionId($result->getTransactionId());
			}			
			$payment
				->setIsTransactionClosed(0)
				->setTransactionAdditionalInfo('real_transaction_id', $result->getTransactionId());
			// added by Gayatri 10/Jun/2010
			if( !$order = $payment->getOrder() )
			{
				$order = $payment->getQuote();
			}
			$order->addStatusToHistory(
				$order->getStatus(),
				urldecode($result->getResponseReasonText()) . ' at FirstdataGlobalgateway, Trans ID: ' . $result->getTransactionId(),
				$result->getResponseReasonText() . ' from FirstdataGlobalgateway, Trans ID: ' . $result->getTransactionId()
			);
			// end added by Gayatri 10/Jun/2010			
		} else {
			if ($result->getResponseReasonText()) {
				$error = $result->getResponseReasonText();
			} else {
				$error = Mage::helper('paygate')->__('Error in capturing the payment');
			}
			if( !$order = $payment->getOrder() )
			{
				$order = $payment->getQuote();
			}
			$order->addStatusToHistory(
				$order->getStatus(),
				urldecode($error) . ' at FirstdataGlobalgateway',
				$error . ' from FirstdataGlobalgateway'
			);			
		}

		if ($error !== false) {
			Mage::throwException($error);
		}

		return $this;
	}

    /**
     * Check void availability
     *
     * @return bool
     */
    public function canVoid(Varien_Object $payment)
    {
		return $this->_canVoid;
    }
	public function void(Varien_Object $payment)
	{
		$error = false;
		$transactionId = $payment->getVoidTransactionId();
		if (empty($transactionId)) {
			$transactionId = $payment->getParentTransactionId();
		}
		
		$amount = $payment->getAmount();
		if ($amount <= 0) {
			$amount = $payment->getAmountAuthorized();
			$payment->setAmount($payment->getAmountAuthorized());
		}
		
		if ($transactionId && $amount > 0) {
			$payment->setAnetTransType(self::REQUEST_TYPE_VOID);
			$request 	 = $this->_buildRequest($payment);
			
			$result = $this->_postRequest($request);
			if ($result->getResponseCode()==self::RESPONSE_CODE_APPROVED) {
				$payment->setStatus(self::STATUS_VOID);
				if ($result->getTransactionId() != $payment->getParentTransactionId()) {
					$payment->setTransactionId($result->getTransactionId());
				}
				$payment
					->setIsTransactionClosed(1)
					->setShouldCloseParentTransaction(1)
					->setTransactionAdditionalInfo('real_transaction_id', $result->getTransactionId());
			} else {
				$errorMsg = $result->getResponseReasonText();
				$error = true;
			}
		} else if (!$transactionId) {
			$errorMsg = Mage::helper('paygate')->__('Error in voiding the payment. Transaction ID not found');
			$error = true;
			
		} else if ($amount <= 0) {
			$errorMsg = Mage::helper('paygate')->__('Error in voiding the payment. Payment amount is 0');
			$error = true;
			
		} else {
			$errorMsg = Mage::helper('paygate')->__('Error in voiding the payment');
			$error = true;
			
		}
		
		if ($error !== false) {
			Mage::throwException($errorMsg);
		}
		return $this;	
	}

    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
		return $this->_canRefund;
    }
	public function refund(Varien_Object $payment, $amount)
	{
		$error = false;
		$transactionId = $payment->getRefundTransactionId();
		if (empty($transactionId)) {
			$transactionId = $payment->getParentTransactionId();
		}		
		if ((($this->getConfigData('test') && $transactionId == 0) || $transactionId) && $amount>0) {
			$payment->setAnetTransType(self::REQUEST_TYPE_CREDIT);
			$request = $this->_buildRequest($payment);
			$request->setXAmount($amount);
			$result = $this->_postRequest($request);
			if ($result->getResponseCode()==self::RESPONSE_CODE_APPROVED) {
				$payment->setStatus(self::STATUS_SUCCESS);
				if ($result->getTransactionId() != $payment->getParentTransactionId()) {
					$payment->setTransactionId($result->getTransactionId());
				}
				$shouldCloseCaptureTransaction = $payment->getOrder()->canCreditmemo() ? 0 : 1;
				$payment
					 ->setIsTransactionClosed(1)
					 ->setShouldCloseParentTransaction($shouldCloseCaptureTransaction)
					 ->setTransactionAdditionalInfo('real_transaction_id', $result->getTransactionId());				
			} else {
				$errorMsg = $result->getResponseReasonText();
				$error = true;
			}

		} else {
			$errorMsg = Mage::helper('paygate')->__('Error in refunding the payment');
			$error = true;
		}

		if ($error !== false) {
			Mage::throwException($errorMsg);
		}
		return $this;
	}

	/**
	* Prepare request to gateway
	*
	* @link   http://www.authorize.net/support/AIM_guide.pdf
	* @param  Mage_Sales_Model_Document $order
	* @return unknown
	*/
	protected function _buildRequest(Varien_Object $payment)
	{
		//$this->logit('Inside _buildRequest calling getOrder', array());
		//$this->logit('Inside _buildRequest AAA', get_class($payment));
		//$this->logit('Inside _buildRequest AAA', get_class_methods(get_class($payment)));

		$order = $payment->getOrder();
		
		//$this->logit('Inside _buildRequest called getOrder', array());
		//$this->logit('Inside _buildRequest BBB', get_class($order));
		//$this->logit('Inside _buildRequest BBB', get_class_methods(get_class($order)));

		if (!$payment->getAnetTransMethod()) {
			$payment->setAnetTransMethod(self::REQUEST_METHOD_CC);
		}

		//$this->logit('Inside _buildRequest A1', array());

		$request = Mage::getModel('firstdataglobalgateway/firstdataglobalgateway_request')
		->setXVersion(3.1)
		->setXDelimData('True')
		->setXDelimChar(self::RESPONSE_DELIM_CHAR)
		->setXRelayResponse('False');

		//$this->logit('Inside _buildRequest A2', get_class_methods(get_class($request)));
		$request->setXTestRequest($this->getConfigData('test') ? 'TRUE' : 'FALSE');

		//$this->logit('Inside _buildRequest A3', array());

		$request->setXLogin($this->getConfigData('login'))
		->setXTranKey($this->getConfigData('trans_key'))
		->setXType($payment->getAnetTransType())
		->setXMethod($payment->getAnetTransMethod());

		if ($payment->getAmount()) {
			$request->setXAmount($payment->getAmount(),2);
			$request->setXCurrencyCode($order->getBaseCurrencyCode());
			
		}
		
		//$this->logit('Inside _buildRequest A4', array());

		switch ($payment->getAnetTransType()) {
			case self::REQUEST_TYPE_CREDIT:
			case self::REQUEST_TYPE_VOID:
			case self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
				$request->setXTransId($payment->getCcTransId());
				$request->setXCardNum($payment->getCcNumber())
					->setXExpDate(sprintf('%02d-%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
					->setXCardCode($payment->getCcCid())
					->setXCardName($payment->getCcOwner())    //SreeAdded
					;				
				break;

			case self::REQUEST_TYPE_CAPTURE_ONLY:
				$request->setXAuthCode($payment->getCcAuthCode());
				break;
		}

		//$this->logit('Inside _buildRequest A5', array());
		//$this->logit('Inside _buildRequest A2', get_class($order));                   
		//$this->logit('Inside _buildRequest A2', get_class_methods(get_class($order)));

		if (!empty($order)) {
			//$this->logit('ORDER NOT EMPTY AND ORDER FREIGHT AMOUNT', array($order->getShippingAmount()));
			//$this->logit('ORDER NOT EMPTY AND ORDER TAX AMOUNT', array($order->getTaxAmount()));
			//$this->logit('ORDER NOT EMPTY AND ORDER SUBTOTAL AMOUNT', array($order->getSubtotal()));
			$freight = $order->getShippingAmount();
			$tax = $order->getTaxAmount();
			$subtotal = $order->getSubtotal();
			
			$request->setXInvoiceNum($order->getIncrementId());

			$billing = $order->getBillingAddress();
			//$this->logit('Inside _buildRequest CCC order->getBillingAddress', get_class($billing));
			//$this->logit('Inside _buildRequest CCC order->getBillingAddress', get_class_methods(get_class($billing)));
			if (!empty($billing)) {

				$email = $billing->getEmail();
				if(!$email)$email = $order->getBillingAddress()->getEmail();
				if(!$email)$email = $order->getCustomerEmail();

				$request->setXFirstName($billing->getFirstname())
				->setXLastName($billing->getLastname())
				->setXCompany($billing->getCompany())
				->setXAddress($billing->getStreet(1))
				->setXCity($billing->getCity())
				->setXState($billing->getRegion())
				->setXZip($billing->getPostcode())
				->setXCountry($billing->getCountry())
				->setXPhone($billing->getTelephone())
				->setXFax($billing->getFax())
				->setXCustId($billing->getCustomerId())
				->setXCustomerIp($order->getRemoteIp())
				->setXCustomerTaxId($billing->getTaxId())
				->setXEmail($email)  //Sree 17Nov2008
				->setXEmailCustomer($this->getConfigData('email_customer'))
				->setXMerchantEmail($this->getConfigData('merchant_email'));
			}

			$shipping = $order->getShippingAddress();
			//$this->logit('Inside _buildRequest DDD shipping = order->getShippingAddress()', get_class($shipping));
			if (!$shipping) {
				$shipping = $billing;
			}
			if (!empty($shipping)) {
				//$this->logit('SHIPPING OBJECT NOT EMPTY', array());			
				$request->setXShipToFirstName($shipping->getFirstname())
				->setXShipToLastName($shipping->getLastname())
				->setXShipToCompany($shipping->getCompany())
				->setXShipToAddress($shipping->getStreet(1))
				->setXShipToCity($shipping->getCity())
				->setXShipToState($shipping->getRegion())
				->setXShipToZip($shipping->getPostcode())
				->setXShipToCountry($shipping->getCountry());

				if(!isset($freight) || $freight<=0) $freight = $shipping->getShippingAmount();
				if(!isset($tax) || $tax<=0) $tax = $shipping->getTaxAmount();
				if(!isset($subtotal) || $subtotal<=0) $subtotal = $shipping->getSubtotal();				
			}

			$request->setXPoNum($payment->getPoNumber())
			->setXTax($tax)
			->setXSubtotal($subtotal)
			->setXFreight($freight);
			//$this->logit('LAST FREIGHT AMOUNT', array($freight));
			//$this->logit('LAST TAX AMOUNT', array($tax));
			//$this->logit('LAST SUB TOTAL', array($subtotal));
		}

		//$this->logit('Inside _buildRequest A6', array());

		switch ($payment->getAnetTransMethod()) {
			case self::REQUEST_METHOD_CC:
				if($payment->getCcNumber()){				
					$request->setXCardNum($payment->getCcNumber())
					->setXExpDate(sprintf('%02d-%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
					->setXCardCode($payment->getCcCid())
					->setXCardName($payment->getCcOwner())    //SreeAdded
					;
				}
				break;

			case self::REQUEST_METHOD_ECHECK:
				$request->setXBankAbaCode($payment->getEcheckRoutingNumber())
				->setXBankName($payment->getEcheckBankName())
				->setXBankAcctNum($payment->getEcheckAccountNumber())
				->setXBankAcctType($payment->getEcheckAccountType())
				->setXBankAcctName($payment->getEcheckAccountName())
				->setXEcheckType($payment->getEcheckType());
				break;
		}
		//$this->logit('Inside _buildRequest A7', array());

		return $request;
	}

	protected function _postRequest(Varien_Object $request)
	{
		$result = Mage::getModel('firstdataglobalgateway/firstdataglobalgateway_result');
		
		/**
		* @TODO
		* Sree handle exception
		*/
		$m = $request->getData();

		//$this->logit("_postRequest m array", array('m' => $m));

		// Pre-Build Returned results
		$r = array (
		0 => '1',
		1 => '1',
		2 => '1',
		3 => '(TESTMODE) This transaction has been approved.',
		4 => '000000',
		5 => 'P',
		6 => '0',
		7 => '100000018',
		8 => '',
		9 => '2704.99',
		10 => 'CC',
		11 => 'auth_only',
		12 => '',
		13 => 'Sreeprakash',
		14 => 'N.',
		15 => 'Schogini',
		16 => 'XYZ',
		17 => 'City',
		18 => 'Idaho',
		19 => '695038',
		20 => 'US',
		21 => '1234567890',
		22 => '',
		23 => '',
		24 => 'Sreeprakash',
		25 => 'N.',
		26 => 'Schogini',
		27 => 'XYZ',
		28 => 'City',
		29 => 'Idaho',
		30 => '695038',
		31 => 'US',
		32 => '',
		33 => '',
		34 => '',
		35 => '',
		36 => '',
		37 => '382065EC3B4C2F5CDC424A730393D2DF',
		38 => '',
		39 => '',
		40 => '',
		41 => '',
		42 => '',
		43 => '',
		44 => '',
		45 => '',
		46 => '',
		47 => '',
		48 => '',
		49 => '',
		50 => '',
		51 => '',
		52 => '',
		53 => '',
		54 => '',
		55 => '',
		56 => '',
		57 => '',
		58 => '',
		59 => '',
		60 => '',
		61 => '',
		62 => '',
		63 => '',
		64 => '',
		65 => '',
		66 => '',
		67 => '',
		);

		//Replace the values from Magento 
		$r[7]  = $m['x_invoice_num']; //InvoiceNumber
		$r[8]  = ''; //Description
		$r[9]  = $m['x_amount']; //Amount
		$r[10] = $m['x_method']; //Method = CC
		$r[11] = $m['x_type']; //TransactionType
		$r[12] = $m['x_cust_id']; //CustomerId
		$r[13] = $m['x_first_name']; 
		$r[14] = $m['x_last_name'];
		$r[15] = $m['x_company'];
		$r[16] = $m['x_address'];
		$r[17] = $m['x_city'];
		$r[18] = $m['x_state'];
		$r[19] = $m['x_zip'];
		$r[20] = $m['x_country'];
		$r[21] = $m['x_phone'];
		$r[22] = $m['x_fax'];
		$r[23] = '';
		
		//no shipping
		$m['x_ship_to_first_name'] 	= !isset($m['x_ship_to_first_name'])?$m['x_first_name']:$m['x_ship_to_first_name'];
		$m['x_ship_to_first_name'] 	= !isset($m['x_ship_to_first_name'])?$m['x_first_name']:$m['x_ship_to_first_name'];
		$m['x_ship_to_last_name'] 	= !isset($m['x_ship_to_last_name'])?$m['x_last_name']:$m['x_ship_to_last_name'];
		$m['x_ship_to_company'] 	= !isset($m['x_ship_to_company'])?$m['x_company']:$m['x_ship_to_company'];
		$m['x_ship_to_address'] 	= !isset($m['x_ship_to_address'])?$m['x_address']:$m['x_ship_to_address'];
		$m['x_ship_to_city'] 		= !isset($m['x_ship_to_city'])?$m['x_city']:$m['x_ship_to_city'];
		$m['x_ship_to_state'] 		= !isset($m['x_ship_to_state'])?$m['x_state']:$m['x_ship_to_state'];
		$m['x_ship_to_zip'] 		= !isset($m['x_ship_to_zip'])?$m['x_zip']:$m['x_ship_to_zip'];
		$m['x_ship_to_country'] 	= !isset($m['x_ship_to_country'])?$m['x_country']:$m['x_ship_to_country'];

		$r[24] = $m['x_ship_to_first_name'];
		$r[25] = $m['x_ship_to_last_name'];
		$r[26] = $m['x_ship_to_company'];
		$r[27] = $m['x_ship_to_address'];
		$r[28] = $m['x_ship_to_city'];
		$r[29] = $m['x_ship_to_state'];
		$r[30] = $m['x_ship_to_zip'];
		$r[31] = $m['x_ship_to_country'];

		//Dummy Replace the values from FirstdataGlobalgateway 
		$r[0]  = '1';  // response_code
		$r[1]  = '1';  // ResponseSubcode
		$r[2]  = '1';  // ResponseReasonCode
		$r[3]  = '(TESTMODE2) This transaction has been approved.'; //ResponseReasonText
		$r[4]  = '000000'; //ApprovalCode
		$r[5]  = 'P'; //AvsResultCode
		$r[6]  = '0'; //TransactionId
		$r[37] = '382065EC3B4C2F5CDC424A730393D2DF'; //Md5Hash
		$r[39] = ''; //CardCodeResponse

		// Add FirstdataGlobalgateway Here
		$rr = $this->_linpointapi($m);
		//$this->logit("_linpointapi call returned back", array('rr' => $rr));

		//Replace the values from FirstdataGlobalgateway 
		$r[0]  = $rr['response_code'];
		$r[1]  = $rr['response_subcode'];
		$r[2]  = $rr['response_reason_code'];
		$r[3]  = $rr['response_reason_text']; //'(TESTMODE2) This transaction has been approved.'; //ResponseReasonText
		$r[4]  = $rr['approval_code']; //'000000'; //ApprovalCode
		$r[5]  = $rr['avs_result_code']; //'P'; //AvsResultCode
		$r[6]  = $rr['transaction_id']; //'0'; //TransactionId
		$r[37] = $rr['md5_hash'];
		$r[39] = $rr['card_code_response'];

		//$this->logit("after r array loaded with rr", array('r' => $r));

		if ($r) {
			//$this->logit("setting", '');
			$result->setResponseCode( (int)str_replace('"','',$r[0]) );
			#$result->setResponseCode( 1 );
			//$this->logit("setting 2", '');
			$result->setResponseSubcode((int)str_replace('"','',$r[1]));
			//$this->logit("setting 3", '');
			$result->setResponseReasonCode((int)str_replace('"','',$r[2]));
			//$this->logit("setting 4", '');
			$result->setResponseReasonText($r[3]);
			//$this->logit("setting 5", '');
			$result->setApprovalCode($r[4]);
			//$this->logit("setting 6", '');
			$result->setAvsResultCode($r[5]);
			//$this->logit("setting 7", '');
			$result->setTransactionId($r[6]);
			//$this->logit("setting 8", '');
			$result->setInvoiceNumber($r[7]);
			//$this->logit("setting 9", '');
			$result->setDescription($r[8]);
			//$this->logit("setting 10", '');
			$result->setAmount($r[9]);
			//$this->logit("setting 11", '');
			$result->setMethod($r[10]);
			//$this->logit("setting 12", '');
			$result->setTransactionType($r[11]);
			//$this->logit("setting 13", '');
			$result->setCustomerId($r[12]);
			//$this->logit("setting 14", '');
			$result->setMd5Hash($r[37]);
			//$this->logit("setting 15", '');
			$result->setCardCodeResponseCode($r[39]);
			//$this->logit("setting 16", '');			
		} else {
			Mage::throwException(
			Mage::helper('paygate')->__('Error in payment gateway')
			);
		}
		
		return $result;
	}
	
	function _linpointapi($m)
	{
		//$this->logit("_firstdataglobalgatewayapi-1 inside the function with param m", array('m' => $m));
		
		$username			= Mage::helper('core')->decrypt($this->getConfigData('username'));
		$gatewayId			= $this->getConfigData('gatewayid');
		$gatewayPassword	= Mage::helper('core')->decrypt($this->getConfigData('gatewaypass'));
		$isTest				= $this->getConfigData('test');
		$authNum			= '';
		$transTag			= '';
		$cardDetails		= array(
							  "Card_Number"			=> '',
							  "Expiry_Date"			=> '',
							  "CardHoldersName"		=> '',
							  "VerificationStr2"	=> '',
							);
		$amount 			= str_replace(",", "", number_format($m['x_amount'], 2)); //Proper number format. No commas to avoid XML error					
		$expDate			= substr($m['x_exp_date'],0,2) . substr($m['x_exp_date'],-2);
		
		// billing address
		$address 			= trim($m['x_address'] . ', ' . $m['x_city'] . ',' . $m['x_state'] . ','. $m['x_country']);
		$cardHoldersName	= htmlentities(xmlentities(trim($m['x_first_name'] . ' ' . $m['x_last_name'])), ENT_QUOTES, 'UTF-8');
		$billingAddress		= htmlentities(xmlentities($address), ENT_QUOTES, 'UTF-8');
		$zipcode			= $m['x_zip'];
		
		switch ($m['x_type']) {
			case 'AUTH_CAPTURE':
				$transactionType = "00";
				$cardDetails = array(
				  "Card_Number"			=> $m['x_card_num'],
				  "Expiry_Date"			=> $expDate,
				  "CardHoldersName"		=> $cardHoldersName,
				  "VerificationStr2"	=> $m['x_card_code']
				);				
				break;
			case 'CAPTURE_ONLY':
			case 'PRIOR_AUTH_CAPTURE':
				//$transactionType = "02";				
				$transactionType = "32";
				list($authNum, $transTag) = explode('|', $m['x_trans_id']);
				break;
			case 'VOID':
				//$transactionType = "13";
				$transactionType = "33";
				list($authNum, $transTag) = explode('|', $m['x_trans_id']);
				break;				
			case 'REFUND': // refund
				//$transactionType = "04";
				$transactionType = "34";
				list($authNum, $transTag) = explode('|', $m['x_trans_id']);
				break;
			case 'AUTH_ONLY':
			default:
				$transactionType = "01";
				$cardDetails = array(
				  "Card_Number"			=> $m['x_card_num'],
				  "Expiry_Date"			=> $expDate,
				  "CardHoldersName"		=> $cardHoldersName,
				  "VerificationStr2"	=> $m['x_card_code']
				);
		}
		
		// get the end point based on test or live mode.
		if ($isTest) {
			//$wsdl = 'https://api.demo.globalgatewaye4.firstdata.com/transaction/wsdl';
			$wsdl = 'https://api.demo.globalgatewaye4.firstdata.com/transaction/v11/wsdl';
		} else {
			//$wsdl = 'https://api.globalgatewaye4.firstdata.com/transaction/wsdl';
			$wsdl = 'https://api.globalgatewaye4.firstdata.com/transaction/v11/wsdl';
		}
<<<<<<< HEAD
=======
		
>>>>>>> master
		$trxnProperties = array_merge($cardDetails, array(
		  "User_Name"			=> $username,
		  "ExactID"				=> $gatewayId,
		  "Password"			=> $gatewayPassword,		  
		  "Secure_AuthResult"	=> "",
		  "Ecommerce_Flag"		=> "0",
		  "XID"					=> "",		  
		  "CAVV"				=> "",
		  "CAVV_Algorithm"		=> "",
		  "Transaction_Type"	=> $transactionType, 
		  "Reference_No"		=> $m['x_invoice_num'],
		  "Customer_Ref"		=> '',
		  "Reference_3"			=> '',
		  "Client_IP"			=> $_SERVER['REMOTE_ADDR'],
		  "Client_Email"		=> $m['x_email'],
		  "Language"			=> 'en',				//English="en" French="fr"
		  "Track1"				=> "",
		  "Track2"				=> "",
		  "Authorization_Num"	=> $authNum,
		  "Transaction_Tag"		=> $transTag,
		  "DollarAmount"		=> $amount,
		  "VerificationStr1"	=> $billingAddress,
		  "CVD_Presence_Ind"	=> "",
		  "Secure_AuthRequired"	=> "",
		  "Currency"			=> "",
		  "PartialRedemption"	=> "",
		  
		  // Level 2 fields 
		  "ZipCode"		=> $m['x_zip'],
		  "Tax1Amount"	=> '',
		  "Tax1Number"	=> '',
		  "Tax2Amount"	=> '',
		  "Tax2Number"	=> '',
		  
		  "SurchargeAmount"	=> '',	//Used for debit transactions only
		  "PAN"				=> ''	//Used for debit transactions only		  
		  ));
		
		//$this->logit("_firstdataglobalgatewayapi xml input", array('xml' => $trxnProperties));
		
		$errors = '';
		try {
			$trxn 	= array("Transaction" => $trxnProperties);
			$client = new SoapClient($wsdl);		
			$trxnResult = $client->__soapCall('SendAndCommit', $trxn);
			
		} catch (SoapFault $e) {
			$msg = '';
			if ($e->faultcode == 'HTTP') {
				$msg = 'Invalid Credentials';
				
			} else {
				$msg = '(' . $e->faultcode . ')' . $e->getMessage();
				
			}
			$errors = 'SOAP Fault: ' . $msg;
			Mage::throwException('SOAP Fault: ' . $msg);
			
		} catch (Exception $e) {
			$msg = $e->getMessage();
			if (empty($msg)) {
				$msg = 'Unknown error';
			}
			$errors = 'SOAP Exception: ' . $msg;
			Mage::throwException('SOAP Exception: ' . $msg);
		}		
		
		
		/*$trxn = array("Transaction" => $trxnProperties);
		$client = new SoapClient($wsdl);
		try {
			$trxnResult = $client->__soapCall('SendAndCommit', $trxn);
		} catch (Exception $e) {
			Mage::throwException('Error: ' . $e->getMessage());
		}*/
		if(@$client->fault){
			// there was a fault, inform
			$errorMsg = $client->faultstring . '(' . $client->faultcode . ')';
			Mage::throwException('Error: ' . $errorMsg);
		}		
		
		// Load Default Dummy Values
		$rr 						= array();
		$rr['response_code']		= '1';	
		$rr['response_subcode']		= '1';
		$rr['response_reason_code']	= '1';
		$rr['response_reason_text'] = '(TESTMODE2) This transaction has been approved.';
		$rr['approval_code'] 		= '000000'; //ApprovalCode
		$rr['avs_result_code']		= 'P';
		$rr['transaction_id']		= '0';
		$rr['md5_hash']				= '382065EC3B4C2F5CDC424A730393D2DF';
		$rr['card_code_response']	= '';
		//$this->logit("_firstdataglobalgatewayapi preloading rr with defaults", array('rr' => $rr));
		
		if (!isset($trxnResult->Bank_Resp_code) && isset($trxnResult->Bank_Resp_Code)) {
			$trxnResult->Bank_Resp_code = $trxnResult->Bank_Resp_Code;
		} else if (!isset($trxnResult->Bank_Resp_Code) && isset($trxnResult->Bank_Resp_code)) {
			$trxnResult->Bank_Resp_Code = $trxnResult->Bank_Resp_code;
		}		
		
		// check the response
		if ($trxnResult->Transaction_Approved == 1) {
			// success
			//$this->logit("_firstdataglobalgatewayapi SUCCESS", array());
			
			$rr['transaction_id'] 		= $trxnResult->Authorization_Num . '|' . $trxnResult->Transaction_Tag;
			$rr['approval_code'] 		= $trxnResult->SequenceNo;
			$rr['response_reason_text'] = $trxnResult->Bank_Message . '-' . $trxnResult->Bank_Resp_Code;
			if ($trxnResult->Bank_Resp_Code == '' || $trxnResult->Bank_Resp_Code == '000' || $trxnResult->Bank_Resp_Code == '00') {
				$rr['response_reason_text'] = $trxnResult->Exact_Message . '-' . $trxnResult->Exact_Resp_code;
			}
			$rr['avs_result_code']		= $trxnResult->AVS;
			$rr['response_reason_text'] .= "\nTransaction ID: " . $trxnResult->Authorization_Num;
			$rr['response_reason_text'] .= "\nAVS Response: " . $this->getAvsResponseText($trxnResult->AVS)  . '[' . $trxnResult->AVS . ']';
			$rr['response_reason_text'] .= "\nCVV Response: " . $this->getCvvResponseText($trxnResult->CVV2) . '[' . $trxnResult->CVV2 . ']';
			
			$rr['response_code']		= '1';	
			$rr['response_subcode']		= '1';
			$rr['response_reason_code']	= '1';

			if ($trxnResult->AVS == '8' || $trxnResult->CVV2 == 'N') {
				$to  = $this->getConfigData('merchant_email');
				$sub = 'Inv Num #' . $m['x_invoice_num'] . ': AVS or CCV failed';
				$msg = $rr['response_reason_text'] . "\n";
				$msg .= 'AVS Code: ' . $trxnResult->AVS . "\n";
				$msg .= 'CVV Code: ' . $trxnResult->CVV2 . "\n";
				$msg .= 'Transaction ID: ' . $trxnResult->Authorization_Num . "\n";
				$msg .= 'Approval Code: ' . $rr['approval_code'] . "\n";
				$msg .= "\nCUSTOMER INFO\n";
				$msg .= "\nName: {$m['x_first_name']}, {$m['x_last_name']}\nAddress: {$m['x_address']}\nCity: {$m['x_city']}\nState: {$m['x_state']}\nZip: {$m['x_zip']}\nCountry: {$m['x_country']}\nEmail:{$m['x_email']}\nPhone:{$m['x_phone']}\n";
				//@mail($to, $sub, $message, 'From: ' . $to);
			}
		} else {
			// error
			//$this->logit("_firstdataglobalgatewayapi FAILED", array());
			
			$rr['response_code']		= '0';	
			$rr['response_subcode']		= '0';
			$rr['response_reason_code']	= '0';
			$rr['response_reason_text'] = $trxnResult->Bank_Message . '-' . $trxnResult->Bank_Resp_Code;
			if ($trxnResult->Bank_Resp_Code == '' || $trxnResult->Bank_Resp_Code == '000' || $trxnResult->Bank_Resp_Code == '00') {
				$rr['response_reason_text'] = $trxnResult->EXact_Message . '(' . $trxnResult->EXact_Resp_Code . ')';
			}			
			$rr['approval_code'] 		= '000000'; //ApprovalCode
			$rr['avs_result_code']		= 'P';
			$rr['transaction_id']		= '0';		
		}
		
		//$this->logit("_firstdataglobalgatewayapi returning rr", array('rr' => $rr));
		return $rr;
	}

	function getAvsResponseText($avs) 
	{
		$avs = trim($avs);
		$msg = 'Unrecognized response';
		switch ($avs) {
			case 'X': 
				$msg = 'exact match, 9 digit zip';
				break;
			case 'Y': 
				$msg = 'exact match, 5 digit zip';
				break;
			case 'A': 
				$msg = 'address match only';
				break;
			case 'W': 
				$msg = '9 digit zip match only';
				break;
			case 'Z': 
				$msg = '5 digit zip match only';
				break;
			case 'N': 
				$msg = 'no address or zip match';
				break;
			case 'U': 
				$msg = 'address unavailable';
				break;
			case 'G': 
				$msg = 'non-North American issuer, does not participate';
				break;
			case 'R': 
				$msg = 'issuer system unavailable';
				break;
			case 'E': 
				$msg = 'not a Mail/Phone order';
				break;
			case 'S': 
				$msg = 'service not supported';
				break;
			case 'Q': 
				$msg = 'Bill to address did not pass edit checks';
				break;
			case 'D': 
				$msg = 'International street address and postal code match';
				break;
			case 'B': 
				$msg = 'International street address match, postal code not verified due to incompatable formats';
				break;
			case 'C': 
				$msg = 'International street address and postal code not verified due to incompatable formats';
				break;
			case 'P': 
				$msg = 'International postal code match, street address not verified due to incompatable format';
				break;
			case '1': 
				$msg = 'Cardholder name matches';
				break;
			case '2': 
				$msg = 'Cardholder name, billing address, and postal code match';
				break;
			case '3': 
				$msg = 'Cardholder name and billing postal code match';
				break;
			case '4': 
				$msg = 'Cardholder name and billing address match';
				break;
			case '5': 
				$msg = 'Cardholder name incorrect, billing address and postal code match';
				break;
			case '6': 
				$msg = 'Cardholder name incorrect, billing postal code matches';
				break;
			case '7': 
				$msg = 'Cardholder name incorrect, billing address matches';
				break;
			case '8': 
				$msg = 'Cardholder name, billing address, and postal code are all incorrect';
				break;			
		}
		
		return $msg;
	}
	
	function getCvvResponseText($cvv)
	{
		$cvv = trim($cvv);
		$msg = 'Unrecognized response';
		switch ($cvv) {
			case 'M': 
				$msg = 'CVV2 / CVC2/CVD Match.'; 
				break;
			case 'N': 
				$msg = 'CVV2 / CVC2/CVD No Match.'; 
				break;
			case 'P': 
				$msg = 'Not Processed.'; 
				break;
			case 'S': 
				$msg = 'Merchant has indicated that CVV2 / CVC2/CVD is not present on the card.'; 
				break;
			case 'U': 
				$msg = 'Issuer is not certified and / or has not provided Visa encryption keys.'; 
				break;			
		}
		
		return $msg;
	}
	
	function logit($func, $arr=array()) 
	{
		// get the log file
		if(!isset($this->pth)||empty($this->pth)){
			$cfg = Mage::getConfig();
			$this->pth = $cfg->getBaseDir();
		}
		$f = $this->pth . '/magento_log.txt';
		
		// If, debug mode is off or module is live then, truncate & delete the file
		if (!$this->getConfigData('debug') || !$this->getConfigData('test')) {
			if (file_exists($f)) {
				$FH = @fopen($f, "w");
				fclose($FH);
				@unlink($f);
			}
			return;
		}
	
		// do not log in live mode
		if (!$this->getConfigData('test')) return;
		
		if (!is_writable($f)) return;
		
		$a = '';
		if(count($arr)>0) $a = var_export($arr,true);
		
		// card details should never be stored anywhere not even in the logs
		$cleanCard = "<creditcard>
						<cardnumber>xxxxxxxxxxxxxxxx</cardnumber>
						<cardexpmonth>xx</cardexpmonth>
						<cardexpyear>xx</cardexpyear>
						<cvmvalue>xxx</cvmvalue>
						<cvmindicator>provided</cvmindicator>
					</creditcard>";
		$a = preg_replace('/<creditcard>(.*)<\/creditcard>/smUi', $cleanCard, $a);
		@file_put_contents($f , '----- Inside ' . $func . ' =1= ' . date('d/M/Y H:i:s') . ' -----' . "\n" . $a, FILE_APPEND);
	}
}

if( !function_exists( 'xmlentities' ) ) { 
    function xmlentities( $string ) { 
        $not_in_list = "A-Z0-9a-z\s_-"; 
        return preg_replace_callback( "/[^{$not_in_list}]/" , 'get_xml_entity_at_index_0' , $string ); 
    } 
    function get_xml_entity_at_index_0( $CHAR ) { 
        if( !is_string( $CHAR[0] ) || ( strlen( $CHAR[0] ) > 1 ) ) { 
            die( "function: 'get_xml_entity_at_index_0' requires data type: 'char' (single character). '{$CHAR[0]}' does not match this type." ); 
        } 
        switch( $CHAR[0] ) { 
            case "'":    case '"':    case '&':    case '<':    case '>': 
                return htmlspecialchars( $CHAR[0], ENT_QUOTES );    break; 
            default: 
                return numeric_entity_4_char($CHAR[0]);                break; 
        }        
    } 
    function numeric_entity_4_char( $char ) { 
        return "&#".str_pad(ord($char), 3, '0', STR_PAD_LEFT).";"; 
    }    
}