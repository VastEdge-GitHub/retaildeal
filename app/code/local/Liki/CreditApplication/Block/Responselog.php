<?php
/**
 * liki Response Log Block
 *
 * @category    Block
 * @package     Nisys Liki Payment
 
 */

class Liki_CreditApplication_Block_Responselog extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	* Constructor
	*/
	public function __construct() {
		$this->_controller = 'responselog';
		$this->_blockGroup = 'CreditApplication';
		$this->_headerText = Mage::helper( 'CreditApplication' )->__( 'Liki Payment Response Log' );
		$this->_addButtonLabel = '';
		parent::__construct();
		$this->_removeButton( 'add' );
	}
}