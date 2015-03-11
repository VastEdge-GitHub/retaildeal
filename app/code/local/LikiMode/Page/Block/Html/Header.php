<?php
class LikiMode_Page_Block_Html_Header extends Mage_Page_Block_Html_Header
{
   

    public function getWelcome()
    {
        if (empty($this->_data['welcome'])) {
            if (Mage::isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_data['welcome'] = $this->__('Welcome, %s!', $this->escapeHtml(Mage::getSingleton('customer/session')->getCustomer()->getName()));
            } else {
			//Changes by LIKI Ext Start
			//Reason of change: Disabled 'welcome' message without login 
                //$this->_data['welcome'] = Mage::getStoreConfig('design/header/welcome');
			//LIKI code END
				$this->_data['welcome'] = '';
            }
        }

        return $this->_data['welcome'];
    }
}
