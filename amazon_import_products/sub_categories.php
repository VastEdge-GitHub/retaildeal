<?php
	ini_set("max_execution_time", 0);
	ini_set("memory_limit", -1);
	ini_set('display_errors', 1);
	
	error_reporting(E_ALL | E_STRICT);
	ob_implicit_flush(true);

	global $base_url_magento;
	$base_url_magento = '/opt/bitnami/apps/magento/htdocs/';
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Sub Category Product Import Started \n";
	/////*****\\\\\	
	
    include($base_url_magento."amazon_import_products/amazon_api_class.php");
    include($base_url_magento."amazon_import_products/amazon_parse_xml.php");
	include($base_url_magento."app/Mage.php");
   	include($base_url_magento."amazon_import_products/amazon_insert_products.php");
	global $chk_exception;	

    $obj				= new AmazonProductAPI();
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Loop for sub categories started \n";
	/////*****\\\\\	
												
	$fh				= fopen($base_url_magento.'amazon_import_products/csv_file/sub_categories.txt', 'w');
	fwrite($fh,'True');
	fclose($fh);
	
	$cat_array_namings		= array("Appliances","Automotive","Baby","PCHardware","Electronics","HealthPersonalCare","HomeGarden","PetSupplies","SportingGoods","Toys","Jewelry","Beauty","Kitchen","OutdoorLiving","Photo","Tools","Watches","Wireless","WirelessAccessories");		// Amazon Categories
	$cat_id_arr_ids			= array("3559","3560","3561","3562","3564","3565","3566","3567","3568","3569","3570","3565","3566","3566","3564","3559","3564","3564","3564");																		// Magento category IDs
	$cat_array				= array();
	$browsenodes_array		= array();
	$cat_id_arr				= array();
	$file_count				= 0;
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Getting magento sub categories \n";
	/////*****\\\\\	
												
	// Get all categories and their details fro magento
	Mage::app();
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
			$cat_active		= $data['is_active'];
			$desc_arr		= explode("==",$description);
			$amazon_id		= $desc_arr[1];
			if($cat_active == '1')												// Only active categories
			{
				if($amazon_id != '1' && $amazon_id != '')						// Only categories with browsenode values
				{		
					if($data['level'] == 3)										// Only sub-categories upto level 3
					{
						$parent_name_arr	= explode("/",$data['url_path']);
						$parent_name		= $parent_name_arr[0];
						if($parent_name == 'appliances')
						{
							$cat_name		= $cat_array_namings[0];
							$cat_id 		= $cat_id_arr_ids[0];
						}
						if($parent_name == 'automotive')
						{
							$cat_name 		= $cat_array_namings[1];
							$cat_id 		= $cat_id_arr_ids[1];
						}
						if($parent_name == 'baby-products')
						{
							$cat_name 		= $cat_array_namings[2];
							$cat_id 		= $cat_id_arr_ids[2];
						}
						if($parent_name == 'computers')
						{
							$cat_name 		= $cat_array_namings[3];
							$cat_id 		= $cat_id_arr_ids[3];
						}
						if($parent_name == 'electronics')
						{
							$cat_name 		= $cat_array_namings[4];
							$cat_id 		= $cat_id_arr_ids[4];
						}
						if($parent_name == 'health-personal-care')
						{
							$cat_name 		= $cat_array_namings[5];
							$cat_id 		= $cat_id_arr_ids[5];
						}
						if($parent_name == 'home-garden')
						{
							$cat_name 		= $cat_array_namings[6];
							$cat_id 		= $cat_id_arr_ids[6];
						}
						if($parent_name == 'pet-supplies')
						{
							$cat_name 		= $cat_array_namings[7];
							$cat_id 		= $cat_id_arr_ids[7];
						}
						if($parent_name == 'sports-outdoors')
						{
							$cat_name 		= $cat_array_namings[8];
							$cat_id 		= $cat_id_arr_ids[8];
						}
						if($parent_name == 'toys-games')
						{
							$cat_name 		= $cat_array_namings[9];
							$cat_id 		= $cat_id_arr_ids[9];
						}
						if($parent_name == 'jewelry')
						{
							$cat_name 		= $cat_array_namings[10];
							$cat_id 		= $cat_id_arr_ids[10];
						}
						$cat_info 	= $data['name']."||".$data['entity_id']."||".$data['parent_id']."||".$data['path']."||".$data['level']."||".$data['children_count']."||".$amazon_id."||".$data['is_active'];
						$prod_cat_ids = $cat_id.",".$data['entity_id'];
						array_push($browsenodes_array,$amazon_id);
						array_push($cat_array,$cat_name);
						array_push($cat_id_arr,$prod_cat_ids);
					}
				}
			}
		}
	}
	echo gmdate('Y-m-d H:i:s')."----> Total magento sub categories : ".count($cat_array)." \n";
	for($n=0;$n<count($cat_array);$n++)
	{
		for($l=1;$l<=3;$l++)
		{
			$max_pages		= 10;
			if($l==1)
			{
				$minPrice	= '5000';				// $50
				$maxPrice	= '85000';				// $1250
			}
			if($l==2)
			{
				$minPrice 	= '85100';				// $851
				$maxPrice 	= '160000';				// $1600
			}
			if($l==3)
			{
				$minPrice 	= '165100';				// $1651
				$maxPrice 	= '250000';				// $2500
			}
			$count_error	= 0;
			$pageNum		= 1;
			$lastPage 		= $max_pages;
			while($pageNum <= $lastPage)
			{
				$chk_exception		= '';

	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Amazon Import Started \n";
	/////*****\\\\\
												
				try
				{
					$result = $obj->searchProducts($browsenodes_array[$n],$cat_array[$n],"BrowseNode",$pageNum,$minPrice,$maxPrice);
				}
				catch(Exception $e)
				{
					$chk_exception = $e->getMessage();
					
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> ".$chk_exception ." for: \n";
	echo "Error Count - ".$count_error." || BN ID - ".$browsenodes_array[$n]." || Cat Name - ".$cat_array[$n]." || Page No. - ".$pageNum." || Min Price - ".$minPrice." || Max Price - ".$maxPrice."++++++++++++ \n";
	/////*****\\\\\
										
					if($chk_exception == 'Invalid xml response.')
					{
						$count_error++;
						if($count_error > 1)
						{
							$pageNum++;
							$count_error = 0;
						}
					}
					ob_flush();
					sleep(2);
					continue;
				}
				$TotalPages=$result->Items->TotalPages;
				if($TotalPages < 10){$lastPage = $TotalPages;}
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Amazon Import Completed \n";
	/////*****\\\\\
				
				if($chk_exception != ''){}
				else
				{
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> XML to CSV Started \n";
	/////*****\\\\\
	
					$csv_filename				= $cat_array[$n]."@".$cat_id_arr[$n]."@MainLoop".$n."@SubLoop".$l."@PageNum".$pageNum.".csv"; // xml to csv file name
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> CSV File: ".$csv_filename." \n";
	/////*****\\\\\
	
					$csv_file					= $base_url_magento."amazon_import_products/csv_file/".$csv_filename;		// xml to csv file full path
					$fileconversion_response	= xml_to_csv_conversion($result,$csv_file);
					$file_count++;
	
	/////*****\\\\\	
	echo gmdate('Y-m-d H:i:s')."----> XML to CSV Completed \n";
	/////*****\\\\\
	
					$pageNum++;				
				}
			}
		}
	}
												
	$fh				= fopen($base_url_magento.'amazon_import_products/csv_file/sub_categories.txt', 'w');
	fwrite($fh,'False');
	fclose($fh);
	
	/////*****\\\\\	
	echo gmdate('Y-m-d H:i:s')."----> Total csv files: ".$file_count." \n";
	echo gmdate('Y-m-d H:i:s')."----> Sub Category Product Import Completed \n";
	/////*****\\\\\	
?>