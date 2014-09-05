<?php
//LIKI Code Start
//Reason of change:Removing the shipping method From Checkout process
include('app/code/core/Mage/Checkout/Block/Onepage/Shipping.php');
class LikiextCheckout_Checkout_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping
//LIKI code End
{
    /**
     * Initialize shipping address step
     */
    protected function _construct()
    {
	//changes done by LIKI code
	//Reason of change: Change the Label of Shipping Information to Shipping Address
        $this->getCheckout()->setStepData('shipping', array(
            'label'     => Mage::helper('checkout')->__('Shipping Address'),
            'is_show'   => $this->isShow()
			//LIKI code END
        ));

       // parent::_construct();
    }
}
