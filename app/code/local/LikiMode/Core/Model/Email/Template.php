<?php
include_once('app/code/core/Mage/Core/Model/Email/Template.php');
class LikiMode_Core_Model_Email_Template extends Mage_Core_Model_Email_Template
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
    
    public function getProcessedTemplate(array $variables = array())
    {
        $processor = $this->getTemplateFilter();
        $processor->setUseSessionInUrl(false)
            ->setPlainTemplateMode($this->isPlain());

        if (!$this->_preprocessFlag) {
            $variables['this'] = $this;
        }

        if (isset($variables['subscriber']) && ($variables['subscriber'] instanceof Mage_Newsletter_Model_Subscriber)) {
            $processor->setStoreId($variables['subscriber']->getStoreId());
        }

        if (!isset($variables['logo_url'])) {
            $variables['logo_url'] = $this->_getLogoUrl($processor->getStoreId());
        }
        if (!isset($variables['logo_alt'])) {
            $variables['logo_alt'] = $this->_getLogoAlt($processor->getStoreId());
        }
        $variables['current_date'] =Mage::getModel('core/date')->date('m-d-Y');
        $processor->setIncludeProcessor(array($this, 'getInclude'))
            ->setVariables($variables);

        $this->_applyDesignConfig();
        try {
            $processedResult = $processor->filter($this->getPreparedTemplateText());
        }
        catch (Exception $e)   {
            $this->_cancelDesignConfig();
            throw $e;
        }
        $this->_cancelDesignConfig();
        return $processedResult;
    }

}
