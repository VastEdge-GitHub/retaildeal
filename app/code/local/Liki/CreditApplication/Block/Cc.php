<?php

class Liki_CreditApplication_Block_Cc extends Mage_Payment_Block_Form
{
 
	//Retrieve payment configuration object
    protected function _getConfig()
    {
       return Mage::getSingleton('payment/config');
    }
	
	// Retrieve availables credit card types
    public function getCcAvailableTypes()
    {	
		$availableTypes = explode(',', Mage::getStoreConfig('payment/CreditApplication/cctypes'));
        $types = $this->_getConfig()->getCcTypes();
		foreach ($types as $code=>$name) {
			if (!in_array($code, $availableTypes)) {
					unset($types[$code]);
			}
		}
		return $types;	
    }
	
	//Retrieve credit card expire months
     public function getCcMonths()
    {
		 $months = $this->getData('cc_months');
         if (is_null($months)) {
            $months[0] =  $this->__('Month');
            $months = array_merge($months, $this->_getConfig()->getMonths());
            $this->setData('cc_months', $months);
        }
        return $months;
	}
	
    //Retrieve credit card expire years    
    public function getCcYears()
    {
        $years = $this->getData('cc_years');
        if (is_null($years)) {
            $years = $this->_getConfig()->getYears();
            $years = array(0=>$this->__('Year'))+$years;
            $this->setData('cc_years', $years);
        }
        return $years;
    }
}
