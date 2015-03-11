<?php
/**
 * Liki Redirect Log Grid Block
 *
 * @category    Block
 * @package     Nisys Liki Payment
 
 */

class LikiMode_CreditApplication_Block_Redirectlog_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	* Constructor
	*/
	 // public function __construct() {
		 // parent::__construct();
		 // $this->setId("likipaymentredirectGrid");
		 // $this->setDefaultSort("likipayment_redirect_id");
		 // $this->setDefaultDir("DESC");
		 // $this->setSaveParametersInSession(true);
	 // }
	
	  public function __construct() {
		  parent::__construct();
		  $this->setId("CreditApplicationredirectGrid");
		  $this->setDefaultSort("CreditApplication_redirect_id");
		  $this->setDefaultDir("DESC");
		  $this->setSaveParametersInSession(true);
	  }
	/**
	* Prepare collection
	*/
	protected function _prepareCollection() {
		$collection = Mage::getModel("CreditApplication/CreditApplicationredirect")->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	* Prepare columns
	*/
	protected function _prepareColumns() {
		// Add columns to grid
		$this->addColumn( 'CreditApplication_redirect_id', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'ID' ),
			'align' => 'right',
			'width' => '50px',
			'type' => 'number',
			'index' => 'CreditApplication_redirect_id',
		));
		$this->addColumn( 'merchant_id', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Merchant ID' ),
			'type' => 'text',
			'index' => 'merchant_id',
		));
		$this->addColumn( 'amount', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Amount' ),
			'type' => 'text',
			'index' => 'amount',
		));
		$this->addColumn( 'order_id', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Order ID' ),
			'type' => 'text',
			'index' => 'order_id',
		));
		$this->addColumn( 'CreditApplication_redirect_dtime', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Date' ),
			'type' => 'datetime',
			'index' => 'CreditApplication_redirect_dtime',
		));
		$this->addColumn( 'CreditApplication_redirect_ip', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Redirect IP' ),
			'type' => 'text',
			'index' => 'CreditApplication_redirect_ip',
		));
		$this->addColumn( 'redirect_url', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Redirect URL' ),
			'type' => 'text',
			'index' => 'redirect_url',
		));
		$this->addColumn( 'checksum', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Checksum' ),
			'type' => 'text',
			'index' => 'checksum',
		));
		$this->addColumn( 'billing_cust_name', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing Name' ),
			'type' => 'text',
			'index' => 'billing_cust_name',
		));
		$this->addColumn( 'billing_cust_address', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing Name' ),
			'type' => 'text',
			'index' => 'billing_cust_address',
		));
		$this->addColumn( 'billing_cust_country', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing Country' ),
			'type' => 'text',
			'index' => 'billing_cust_country',
		));
		$this->addColumn( 'billing_cust_state', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing State' ),
			'type' => 'text',
			'index' => 'billing_cust_state',
		));
		$this->addColumn( 'billing_zip', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing ZIP' ),
			'type' => 'text',
			'index' => 'billing_zip',
		));
		$this->addColumn( 'billing_cust_city', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing City' ),
			'type' => 'text',
			'index' => 'billing_cust_city',
		));
		$this->addColumn( 'billing_zip_code', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing ZIP' ),
			'type' => 'text',
			'index' => 'billing_zip_code',
		));
		$this->addColumn( 'billing_cust_tel', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing Tel No' ),
			'type' => 'text',
			'index' => 'billing_cust_tel',
		));
		$this->addColumn( 'billing_cust_email', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Billing Email' ),
			'type' => 'text',
			'index' => 'billing_cust_email',
		));
		$this->addColumn( 'delivery_cust_name', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Delivery Name' ),
			'type' => 'text',
			'index' => 'delivery_cust_name',
		));
		$this->addColumn( 'delivery_cust_address', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Delivery Name' ),
			'type' => 'text',
			'index' => 'delivery_cust_address',
		));
		$this->addColumn( 'delivery_cust_country', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Delivery Country' ),
			'type' => 'text',
			'index' => 'delivery_cust_country',
		));
		$this->addColumn( 'delivery_cust_state', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Delivery State' ),
			'type' => 'text',
			'index' => 'delivery_cust_state',
		));
		$this->addColumn( 'delivery_cust_tel', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Delivery Tel No' ),
			'type' => 'text',
			'index' => 'delivery_cust_tel',
		));
		$this->addColumn( 'delivery_cust_city', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Delivery City' ),
			'type' => 'text',
			'index' => 'delivery_cust_city',
		));
		$this->addColumn( 'delivery_zip_code', array(
			'header' => Mage::helper( 'CreditAppplication' )->__( 'Delivery ZIP' ),
			'type' => 'text',
			'index' => 'delivery_zip_code',
		));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
		$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
		
		// Return columns
		return parent::_prepareColumns();
	}
	
	/**
	* Row URL link
	*/
	public function getRowUrl($row) {
		return '#';
	}
}
