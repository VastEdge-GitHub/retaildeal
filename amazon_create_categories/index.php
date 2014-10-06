<?php
ini_set("max_execution_time", 0);

global $base_url_magento;
global $subcat_array;

$base_url_magento = '/opt/bitnami/apps/magento/htdocs/';

include($base_url_magento."amazon_create_categories/amazon_api_class.php");
include($base_url_magento."app/Mage.php");
include($base_url_magento."amazon_create_categories/create_category.php");

Mage::app('default'); // Default or your store view name.

$public_key				= "AKIAJI5XSRBH6TEPCM2A";
$private_key   			= "62ivalcpr1CSa3+zBIbYepSV7uuDun0cNnrp8wIS";
$region       			= "com"; // or "CA" or "DE" etc.

for($n=0;$n<1;$n++)
{
	$mage_cat_info_array	= array();
	$browsenode_info_array	= array();
	
	$category 				= Mage::getModel('catalog/category');
	$tree 					= $category->getTreeModel();
	$tree->load();
	$ids 					= $tree->getCollection()->getAllIds();
	if($ids)
	{
		foreach($ids as $id)
		{
			$cat 			= Mage::getModel('catalog/category');
			$cat->load($id);
			$data 			= $cat->getData();
			$description	= $data['description'];
			$desc_arr		= explode("==",$description);
			$amazon_id		= $desc_arr[1];
			if($amazon_id != '1' && $amazon_id != '')
			{		
				$cat_info 	= $data['name']."||".$data['entity_id']."||".$data['parent_id']."||".$data['path']."||".$data['level']."||".$data['children_count']."||".$amazon_id."||".$data['is_active'];
				array_push($browsenode_info_array,$amazon_id);
				array_push($mage_cat_info_array,$cat_info);
			}
		}
	}
	
	$obj 						= new AmazonProductAPI($public_key, $private_key, $region);
	for($i=0;$i<count($mage_cat_info_array);$i++)
	{
		$mage_cat_data 			= $mage_cat_info_array[$i];
		$mage_cat_data_array	= explode("||",$mage_cat_data);
		$subcat_array			= array();
		$result 				= $obj->getBrowseNodes($browsenode_info_array[$i]);
		create_category($result,$mage_cat_data_array,$browsenode_info_array);
	}
}
?>