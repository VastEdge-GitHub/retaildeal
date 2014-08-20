<?php

global $base_url_magento;

require_once $base_url_magento.'app/Mage.php';
Mage::app('default'); // Default or your store view name.

function create_category($result,$mage_cat_data_array,$browsenode_info_array)
{
	for($a=0;$a<count($result);$a++)
	{
		$cat_data		= explode("||",$result[$a]);
		$cat_name		= $cat_data[0];
		$cat_amazonid	= $cat_data[1];
		$cat_path		= $mage_cat_data_array[3];
		if(!in_array($cat_amazonid,$browsenode_info_array))
		{
			$category = Mage::getModel('catalog/category');
			$category->setStoreId(0); 										// 0 = default/all store view. If you want to save data for a specific store view, replace 0 by Mage::app()->getStore()->getId().
			$general['name']				= $cat_name;					// Category Name
			$general['path']				= $cat_path;					// Category level path (IDs of parent category)
			//$general['amazon_category_id']	= $cat_amazonid; 			// Category level path (IDs of parent category)
			$general['description']			= "AmazonID==".$cat_amazonid; 	// Category level path (IDs of parent category)
			$general['is_active'] 			= 1;							// 1=Yes, 2=No

			$category->addData($general);
			try
			{
				$category->save();
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}
}
?>