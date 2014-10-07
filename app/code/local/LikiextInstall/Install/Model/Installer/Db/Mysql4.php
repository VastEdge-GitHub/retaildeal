<?php
include('app/code/core/Mage/Install/Model/Installer/Db/Mysql4.php');
class LikiextInstall_Install_Model_Installer_Db_Mysql4 extends Mage_Install_Model_Installer_Db_Mysql4
{
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
