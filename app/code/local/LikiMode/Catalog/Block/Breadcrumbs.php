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
 * Catalog breadcrumbs
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */ 
class LikiMode_Catalog_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Retrieve HTML title value separator (with space)
     *
     * @param mixed $store
     * @return string
     */
    public function getTitleSeparator($store = null)
    {
        $separator = (string)Mage::getStoreConfig('catalog/seo/title_separator', $store);
        return ' ' . $separator . ' ';
    }

    /**
     * Preparing layout
     *
     * @return Mage_Catalog_Block_Breadcrumbs
     */
    protected function _prepareLayout()
	{
	if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
	$breadcrumbsBlock->addCrumb('home', array(
	'label'=>Mage::helper('catalog')->__('Home'),
	'title'=>Mage::helper('catalog')->__('Go to Home Page'),
	'link'=>Mage::getBaseUrl()
	));
	 
	// MOD to add the full category breadcrumb path.
	$current_category = Mage::registry('current_category');
	$current_product = Mage::registry('current_product');
	 
	if(!$current_category && $current_product){
	$categories = $current_product->getCategoryCollection()->addAttributeToSelect('name')->setPageSize(1);
	foreach($categories as $category) {
	Mage::unregister('current_category');
	Mage::register('current_category', $category);
	} 
	}
	// end MOD
	$title = array();
	$path = Mage::helper('catalog')->getBreadcrumbPath();
	foreach ($path as $name => $breadcrumb) {
	$breadcrumbsBlock->addCrumb($name, $breadcrumb);
	$title[] = $breadcrumb['label'];
	}
	 
	if ($headBlock = $this->getLayout()->getBlock('head')) {
	$headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
	}
	}
	return parent::_prepareLayout();
	} 
}