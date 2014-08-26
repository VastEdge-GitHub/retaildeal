<?php
/**
 * Liki Response Log Grid Block
 *
 * @category    Block
 * @package     Nisys Liki Payment
 
 */

class Liki_CreditApplication_Block_Responselog_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	* Constructor
	*/
	public function __construct() {
		parent::__construct();
		$this->setId("CreditApplicationresponseGrid");
		$this->setDefaultSort("CreditApplication_response_id");
		$this->setDefaultDir("DESC");
		$this->setSaveParametersInSession(true);
	}
	
	/**
	* Prepare collection
	*/
	protected function _prepareCollection() {
		$collection = Mage::getModel("CreditApplication/CreditApplicationresponse")->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	* Prepare columns
	*/
	protected function _prepareColumns() {
		// Add columns to grid
		$this->addColumn( 'CreditApplication_response_id', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'ID' ),
			'align' => 'right',
			'width' => '50px',
			'type' => 'number',
			'index' => 'CreditApplication_response_id',
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
		$this->addColumn( 'CreditApplication_response_dtime', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Date' ),
			'type' => 'datetime',
			'index' => 'CreditApplication_response_dtime',
		));
		$this->addColumn( 'CreditApplication_response_ip', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Redirect IP' ),
			'type' => 'text',
			'index' => 'CreditApplication_response_ip',
		));
		$this->addColumn( 'checksum', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Checksum' ),
			'type' => 'text',
			'index' => 'checksum',
		));
		$this->addColumn( 'authdesc', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Authdesc' ),
			'type' => 'text',
			'index' => 'authdesc',
		));
		$this->addColumn( 'card_category', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Card Category' ),
			'type' => 'text',
			'index' => 'card_category',
		));
		$this->addColumn( 'bank_name', array(
			'header' => Mage::helper( 'CreditApplication' )->__( 'Bank Name' ),
			'type' => 'text',
			'index' => 'bank_name',
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