<?php
class LikiMode_Catalog_Helper_Product_Compare extends Mage_Catalog_Helper_Product_Compare
{
    /**
     * Retrieve url for adding product to conpare list
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  string
     */
    public function getAddUrl($product)
    {
		//Changes by LIKI Ext Start
		//Reason of change: Commented to remove compare products URL
       // return $this->_getUrl('catalog/product_compare/add', $this->_getUrlParams($product));
		//LIKI Code End    
	}
}
