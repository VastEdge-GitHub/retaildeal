<?php
class LikiextInstall_Install_Model_Installer_Db_Mysql4 extends Mage_Install_Model_Installer_Db_Abstract
{
    /**
     * Retrieve DB server version
     *
     * @return string (string version number | 'undefined')
     */
    public function getVersion()
    {
        $version  = $this->_getConnection()
            ->fetchOne('SELECT VERSION()');
        $version    = $version ? $version : 'undefined';
        $match = array();
        if (preg_match("#^([0-9\.]+)#", $version, $match)) {
            $version = $match[0];
        }
        return $version;
    }

    /**
     * Check InnoDB support
     *
     * @return bool
     */
    public function supportEngine()
    {
	//Changes by LIKI Ext Start
	//Reason of change: Support engine function is customized
        $variables  = $this->_getConnection()
            ->fetchPairs('SHOW ENGINES');
        return (isset($variables['InnoDB']) && $variables['InnoDB'] != 'NO');
	//LIKI code END
    }
}
