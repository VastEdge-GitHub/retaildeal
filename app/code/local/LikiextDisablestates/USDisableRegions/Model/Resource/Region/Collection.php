<?php
class LikiextDisablestates_USDisableRegions_Model_Resource_Region_Collection extends Mage_Directory_Model_Resource_Region_Collection
{
    public function addCountryFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter('main_table.country_id', array('in' => $countryId));
            } else {
                $this->addFieldToFilter('main_table.country_id', $countryId);
            }
        }

        $allowedRegions = Mage::getStoreConfig('general/enabled_regions/region');

        if(!Mage::app()->getStore()->isAdmin() && Mage::getDesign()->getArea() != 'adminhtml') {

            if($countryId == "US" || is_array($countryId) && implode($countryId) == "US") {
                $this->addRegionNameFilter(explode(",", $allowedRegions));
            }
        }

        return $this;
    }
}
