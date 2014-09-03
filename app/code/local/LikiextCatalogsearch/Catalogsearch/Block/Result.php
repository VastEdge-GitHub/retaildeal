<?php
include('app\code\core\Mage\CatalogSearch\Block\Result.php');
class LikiextCatalogsearch_CatalogSearch_Block_Result extends Mage_CatalogSearch_Block_Result
{
   /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListCollection()
    {
	//Changes by LIKI Ext Start
	//Reason of change: Commented remove from setListCollection()
        $this->getListBlock()
          ->setCollection($this->_getProductCollection());
       //LIKI Code End
	   return $this;
    }
}
