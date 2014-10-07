<?php
<<<<<<< HEAD
include_once('app/code/core/Mage/Core/Model/Email/Template.php');
=======
include('app/code/core/Mage/Core/Model/Email/Template.php');
>>>>>>> master
class LikiextCore_Core_Model_Email_Template extends Mage_Core_Model_Email_Template
{

    /**
     * Retrieve mail object instance
     *
     * @return Zend_Mail
     */
    public function getMail()
    {
        if (is_null($this->_mail)) {
		//Changes by LIKI Ext Start
		//Reason of change: Added code related to email; Zend_Mail_Transport_Smtp($my_smtp_host, $config);
	  $my_smtp_host = Mage::getStoreConfig('system/smtp/host');
          $my_smtp_port = Mage::getStoreConfig('system/smtp/port');
          $config = array(
               'ssl' => 'tls',
               'port' => $my_smtp_port,
               'auth' => 'login',
               'username' => 'likisender@gmail.com',
                'password' => 'Torusliki34'
          );
          $transport = new Zend_Mail_Transport_Smtp($my_smtp_host, $config);
          Zend_Mail::setDefaultTransport($transport);
		//LIKI Code End 
          $this->_mail = new Zend_Mail('utf-8');
        }
	
        return $this->_mail;
    }

}
