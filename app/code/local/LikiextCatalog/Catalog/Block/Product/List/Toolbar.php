<?php
include('app/code/core/Mage/Catalog/Block/Product/List/Toolbar.php');
class LikiextCatalog_Catalog_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{

    /**
     * Default direction
     *
     * @var string
     */
	 //LIKI Code Start
	 //Reason of Change: Products should be in descending order by default, when page is opened
    protected $_direction           = 'desc';
	//LIKI Code END
}
