<?php
/**
 * Liki Redirect Log Block
 *
 * @category    Block
 * @package     Nisys Liki Payment
 
 */

class Liki_CreditApplication_Block_Redirectlog extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	* Constructor
	*/
	public function __construct() {
		$this->_controller = 'redirectlog';
		$this->_blockGroup = 'CreditApplication';
		$this->_headerText = Mage::helper( 'CreditApplication' )->__( 'Liki Payment Redirect Log' );
		$this->_addButtonLabel = '';
		parent::__construct();
		$this->_removeButton( 'add' );
	}
}