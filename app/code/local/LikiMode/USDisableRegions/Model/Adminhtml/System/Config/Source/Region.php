<?php
class LikiMode_USDisableRegions_Model_Adminhtml_System_Config_Source_Region {

    public function toOptionArray() {

        $allowedRegions = array();

        foreach(Mage::getModel('directory/region_api')->items("US") as $region) {

            $allowedRegions[] = array('value' => $region["name"], 'label' =>$region["name"]);

        }
        return $allowedRegions;
    }
}
