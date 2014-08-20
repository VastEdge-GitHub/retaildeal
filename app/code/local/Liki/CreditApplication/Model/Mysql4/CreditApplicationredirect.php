<?php
class Liki_LikiPayment_Model_Mysql4_LikiPaymentredirect extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct() {
        $this->_init( 'likipayment/likipaymentredirect', 'likipayment_redirect_id' );
    }
}