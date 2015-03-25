<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product random items block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class LikiMode_Catalog_Block_Product_List_Random extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
		
		
$mageFilename = 'app/Mage.php';

require_once $mageFilename;

umask(0);
Mage::app('admin');
$types = Array(
          0 => 'config', 
          1 => 'layout',
          2 => 'block_html', 
          3 => 'translate', 
          4 => 'collections',
          5 => 'eav',
          6 => 'config_api',
          7 => 'config_api2'                    
        );

 $allTypes = Mage::app()->useCache();

$updatedTypes = 0;
foreach ($types as $code)
{
    if ($allTypes[$code]==0)
    {
		$cache_disabled = "True";
        break;
	}
    else
    {
		$cache_disabled = "False";
		$updatedTypes++;
	}
}
if($cache_disabled=="True")
{
	if (is_null($this->_productCollection)) {
            $collection = Mage::getResourceModel('catalog/product_collection');
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
//            $collection->getSelect()->order('rand()');
shuffle($collection);
			$collection->addAttributeToFilter('promotion', 1)
                ->addStoreFilter(); 
// $collection->getSelect()->order('rand()');

			//$collection->addStoreFilter();
            $numProducts = $this->getNumProducts() ? $this->getNumProducts() : 0;
      //      $collection->setPage(1, $numProducts);
            $this->_productCollection = $collection;
        }
}
else{		
		if (is_null($this->_productCollection)) {
			$cache = Mage::getSingleton('core/cache');
			$key = 'homepage-most-view-' . $storeId;
			  if(! $data = $cache->load($key)){
			$collection = Mage::getResourceModel('catalog/product_collection');      
			Mage::getModel('catalog/layer')->prepareProductCollection($collection);
			$collection->addAttributeToFilter('promotion', 1)->addStoreFilter();
				  $data = serialize($collection);
				  $cache->save(urlencode($data), $key, array("homepage_cache"), 60*60*24);
			shuffle($collection); $this->_productCollection = $collection;  }
			  else{       $collection = unserialize(urldecode($data)); shuffle($collection);
				  $numProducts = $this->getNumProducts() ? $this->getNumProducts() : 0;
			$this->_productCollection = $collection; }
        }
   }     
        return $this->_productCollection;
    }
}
