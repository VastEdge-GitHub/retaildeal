<?php
class LikiextCatalog_Catalog_Helper_Product_Compare extends Mage_Core_Helper_Url
{
    /**
     * Product Compare Items Collection
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection
     */
    protected $_itemCollection;

    /**
     * Product Comapare Items Collection has items flag
     *
     * @var bool
     */
    protected $_hasItems;

    /**
     * Allow used Flat catalog product for product compare items collection
     *
     * @var bool
     */
    protected $_allowUsedFlat = true;

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Retrieve Catalog Session instance
     *
     * @return Mage_Catalog_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('catalog/session');
    }

    /**
     * Retrieve compare list url
     *
     * @return string
     */
    public function getListUrl()
    {
         $itemIds = array();
         foreach ($this->getItemCollection() as $item) {
             $itemIds[] = $item->getId();
         }

         $params = array(
            'items'=>implode(',', $itemIds),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
         );

         return $this->_getUrl('catalog/product_compare', $params);
    }

    /**
     * Get parameters used for build add product to compare list urls
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  array
     */
    protected function _getUrlParams($product)
    {
        return array(
            'product' => $product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
    }

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
        //return $this->_getUrl('catalog/product_compare/add', $this->_getUrlParams($product));
		//LIKI Code End    
	}

    /**
     * Retrive add to wishlist url
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        $beforeCompareUrl = Mage::getSingleton('catalog/session')->getBeforeCompareUrl();

        $params = array(
            'product'=>$product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
        );

        return $this->_getUrl('wishlist/index/add', $params);
    }

    /**
     * Retrive add to cart url
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCartUrl($product)
    {
        $beforeCompareUrl = Mage::getSingleton('catalog/session')->getBeforeCompareUrl();
        $params = array(
            'product'=>$product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
        );

        return $this->_getUrl('checkout/cart/add', $params);
    }

    /**
     * Retrieve remove item from compare list url
     *
     * @param   $item
     * @return  string
     */
    public function getRemoveUrl($item)
    {
        $params = array(
            'product'=>$item->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
        return $this->_getUrl('catalog/product_compare/remove', $params);
    }

    /**
     * Retrieve clear compare list url
     *
     * @return string
     */
    public function getClearListUrl()
    {
        $params = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
        return $this->_getUrl('catalog/product_compare/clear', $params);
    }

    /**
     * Retrieve compare list items collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection
     */
    public function getItemCollection()
    {
        if (!$this->_itemCollection) {
            $this->_itemCollection = Mage::getResourceModel('catalog/product_compare_item_collection')
                ->useProductItem(true)
                ->setStoreId(Mage::app()->getStore()->getId());

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_itemCollection->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_itemCollection->setCustomerId($this->_customerId);
            } else {
                $this->_itemCollection->setVisitorId(Mage::getSingleton('log/visitor')->getId());
            }

            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($this->_itemCollection);

            /* Price data is added to consider item stock status using price index */
            $this->_itemCollection->addPriceData();

            $this->_itemCollection->addAttributeToSelect('name')
                ->addUrlRewrite()
                ->load();

            /* update compare items count */
            $this->_getSession()->setCatalogCompareItemsCount(count($this->_itemCollection));
        }

        return $this->_itemCollection;
    }

    /**
     * Calculate cache product compare collection
     *
     * @param  bool $logout
     * @return Mage_Catalog_Helper_Product_Compare
     */
    public function calculate($logout = false)
    {
        // first visit
        if (!$this->_getSession()->hasCatalogCompareItemsCount() && !$this->_customerId) {
            $count = 0;
        } else {
            /** @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection */
            $collection = Mage::getResourceModel('catalog/product_compare_item_collection')
                ->useProductItem(true);
            if (!$logout && Mage::getSingleton('customer/session')->isLoggedIn()) {
                $collection->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            } elseif ($this->_customerId) {
                $collection->setCustomerId($this->_customerId);
            } else {
                $collection->setVisitorId(Mage::getSingleton('log/visitor')->getId());
            }

            /* Price data is added to consider item stock status using price index */
            $collection->addPriceData();

            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($collection);

            $count = $collection->getSize();
        }

        $this->_getSession()->setCatalogCompareItemsCount($count);

        return $this;
    }

    /**
     * Retrieve count of items in compare list
     *
     * @return int
     */
    public function getItemCount()
    {
        if (!$this->_getSession()->hasCatalogCompareItemsCount()) {
            $this->calculate();
        }

        return $this->_getSession()->getCatalogCompareItemsCount();
    }

    /**
     * Check has items
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItemCount() > 0;
    }

    /**
     * Set is allow used flat (for collection)
     *
     * @param bool $flag
     * @return Mage_Catalog_Helper_Product_Compare
     */
    public function setAllowUsedFlat($flag)
    {
        $this->_allowUsedFlat = (bool)$flag;
        return $this;
    }

    /**
     * Retrieve is allow used flat (for collection)
     *
     * @return bool
     */
    public function getAllowUsedFlat()
    {
        return $this->_allowUsedFlat;
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return Mage_Catalog_Helper_Product_Compare
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }
}
