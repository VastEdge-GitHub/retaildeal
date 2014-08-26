<?php
	ini_set("max_execution_time", 0);
	ini_set("memory_limit", -1);
	ini_set('display_errors', 1);
	
	error_reporting(E_ALL | E_STRICT);
	ob_implicit_flush(true);

	global $base_url_magento;
	$base_url_magento = '/opt/bitnami/apps/magento/htdocs/';
	// Logging
	//$fh = fopen($base_url_magento.'amazon_import_products/log/log_import_products.text', 'a');
	//fwrite($fh,PHP_EOL.gmdate('Y-m-d H:i:s')."----> Product Import Started".PHP_EOL);
	echo gmdate('Y-m-d H:i:s')."----> Product Import Started \n";
	//// Delete previous cache files if available
	$files_del = glob($base_url_magento.'amazon_import_products/liki_img/cache/*'); // get all file names
	foreach($files_del as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	
	global $new_sku_arr;
	global $allsku_array;	
	
	$new_sku_arr		= array();								// array containing SKU of all amazon products
	$allsku_array 		= array();								// array containing SKU of all magento products

    include($base_url_magento."amazon_import_products/amazon_api_class.php");
    include($base_url_magento."amazon_import_products/amazon_parse_xml.php");
	include($base_url_magento."app/Mage.php");
   	include($base_url_magento."amazon_import_products/amazon_insert_products.php");
	global $chk_exception;	

    $obj				= new AmazonProductAPI();
//	$minPrice 			= '3500';								// $35
//	$maxPrice	 		= '200000';								// $2000
	$csv_filename		= $base_url_magento."amazon_import_products/csv_file/amazon_parse_xml.csv";				// xml to csv file name
	
	
	for($chk=2;$chk<=2;$chk++)
	{
		if($chk==1)																			// Loop for main categories
		{
			echo gmdate('Y-m-d H:i:s')."----> Loop for main categories started \n";
			$cat_array 				= array("Appliances","Automotive","Baby","PCHardware","Electronics","HealthPersonalCare","HomeGarden","PetSupplies","SportingGoods","Toys","Jewelry","Beauty","Kitchen","OutdoorLiving","Photo","Tools","Watches","Wireless","WirelessAccessories");								// Amazon Categories
			$browsenodes_array		= array("2619525011","15690151","165796011","541966","172282","3760931","285080","12923371","3375251","165793011","3880591","11055981","284507","286168","502394","468240","377110011","508494","13900851");	// Amazon BrowseNodes of Categories
			$cat_id_arr 			= array("3559","3560","3561","3562","3564","3565","3566","3567","3568","3569","3570","3565","3566","9","3564","3559","3564","3564","3564");																		// Magento category IDs
		}
		if($chk==2)																			// Loop for sub categories
		{
			echo gmdate('Y-m-d H:i:s')."----> Loop for sub categories started \n";
			$cat_array_namings		= array("Appliances","Automotive","Baby","PCHardware","Electronics","HealthPersonalCare","HomeGarden","PetSupplies","SportingGoods","Toys","Jewelry","Beauty","Kitchen","OutdoorLiving","Photo","Tools","Watches","Wireless","WirelessAccessories");		// Amazon Categories
			$cat_id_arr_ids			= array("3559","3560","3561","3562","3564","3565","3566","3567","3568","3569","3570","3565","3566","9","3564","3559","3564","3564","3564");																		// Magento category IDs
			$cat_array				= array();
			$browsenodes_array		= array();
			$cat_id_arr				= array();
			
			// Get all categories and their details fro magento
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
							if($data['level'] > 2)										// Ony sub-categories
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
		}
		for($n=0;$n<count($cat_array);$n++)
		{
			for($l=1;$l<=5;$l++)
			{
				$max_pages	   = 10;
				if($l==1)
				{
					$minPrice = '3500';					// $35
					$maxPrice = '42500';				// $425
					
				}
				if($l==2)
				{
					$minPrice = '42600';				// $426
					$maxPrice = '81500';				// $815
					
				}
				if($l==3)
				{
					$minPrice = '81600';				// $816
					$maxPrice = '120500';				// $1205
					
				}
				if($l==4)
				{
					$minPrice = '120600';				// $1206
					$maxPrice = '159500';				// $1595
					
				}
				if($l==5)
				{
					$minPrice = '159600';				// $1596
					$maxPrice = '200000';				// $2000
					
				}
				if($cat_array[$n] == 'Appliances'){$max_pages = 5;}
				if($cat_array[$n] == 'Automotive'){$max_pages = 5;}
				//echo $browsenodes_array[$n]."||".$cat_array[$n]."||".$pageNum."||".$minPrice."||".$maxPrice."++++++++++++";
				$count_error			= '0';
				$pageNum	= 1;
				$lastPage = $max_pages;
				while($pageNum <= $lastPage)
				{
					$chk_exception		= '';
					echo gmdate('Y-m-d H:i:s')."----> Amazon Import Started \n";
					try
					{
						$result = $obj->searchProducts($browsenodes_array[$n],$cat_array[$n],"BrowseNode",$pageNum,$minPrice,$maxPrice);
					}
					catch(Exception $e)
					{
						$chk_exception = $e->getMessage();
						echo gmdate('Y-m-d H:i:s')."----> ".$chk_exception ." for: \n";
						echo "BN ID - ".$browsenodes_array[$n]." || Cat Name - ".$cat_array[$n]." || Page No. - ".$pageNum." || Min Price - ".$minPrice." || Max Price - ".$maxPrice."++++++++++++ \n";
						if($chk_exception == 'Invalid xml response.')
						{
							$count_error++;
							if($count_error > '1')
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
					echo gmdate('Y-m-d H:i:s')."----> Amazon Import Completed \n";
					if($chk_exception != ''){}
					else
					{
						echo gmdate('Y-m-d H:i:s')."----> XML to CSV Started \n";
						$fileconversion_response	= xml_to_csv_conversion($result,$csv_filename);
						echo gmdate('Y-m-d H:i:s')."----> XML to CSV Completed \n";
						if($fileconversion_response)
						{
							echo gmdate('Y-m-d H:i:s')."----> Magento products add started \n";
							$insert_products = insert_products($csv_filename,$n,$pageNum,$cat_id_arr[$n],$chk);
						}
						echo gmdate('Y-m-d H:i:s')."----> Magento products add Completed \n";
						$pageNum++;
					}
				}
			}
		}
	}
	echo gmdate('Y-m-d H:i:s')."----> Disable products started \n";
	disable_products();
	echo gmdate('Y-m-d H:i:s')."----> Disable products Completed \n";
	echo gmdate('Y-m-d H:i:s')."----> Product Import Completed \n";
	//fwrite($fh,gmdate('Y-m-d H:i:s')."----> Product Import Stopped".PHP_EOL);
	//fclose($f);
?>