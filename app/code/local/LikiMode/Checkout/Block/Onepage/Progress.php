<?php
//LIKI Code Start
//Reason of change:Removing the shipping method From Checkout process
class LikiMode_Checkout_Block_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress
//LIKI Code End
{
    public function getShippingMethod()
    {
        //LIKI Code Start
		//Remove/skip the shipping method and just return the true
		return true;
    	//LIKI Code End
	}
}
